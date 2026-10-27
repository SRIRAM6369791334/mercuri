<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductStock;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductMasterImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private int    $headingRowNumber;
    private array  $categoryCache    = [];   // normalisedName  → category id (int)
    private array  $subCategoryCache = [];   // catId|normName  → subcat id (int)
    private string $lastSegment      = '';   // forward-fill for blank segment cells

    public array   $report = [
        'created'       => [],
        'updated'       => [],
        'skipped'       => [],
        'errors'        => [],
        'products_list' => [],
    ];

    public function __construct(int $headingRowNumber = 1)
    {
        $this->headingRowNumber = $headingRowNumber;
    }

    public function headingRow(): int
    {
        return $this->headingRowNumber;
    }

    public function collection(Collection $rows): void
    {
        $rowNum = $this->headingRowNumber;

        foreach ($rows as $row) {
            $rowNum++;

            // ── 1. Forward-fill Category ─────────────────────────────
            $rawSegment = $this->stringOrNull(
                $row['product_segment'] ??
                $row['product_segnment'] ?? // Handle typo with 'N'
                $row['product_segment_for_ex_mhe_standard'] ??
                $row['segment'] ??
                $row['category'] ?? null
            );

            if ($rawSegment !== null) {
                $this->lastSegment = $rawSegment;
            }

            if (empty($this->lastSegment)) {
                $this->report['skipped'][] = "Row {$rowNum}: No category/segment found, skipped.";
                continue;
            }

            $catId = $this->resolveCategory($this->lastSegment);

            // ── 2. Sub-category ──────────────────────────────────────
            $groupName = $this->stringOrNull(
                $row['product_group'] ??
                $row['group'] ??
                $row['sub_category'] ??
                $row['subcategory'] ?? null
            );

            $subCatId = null;
            if ($groupName !== null && $groupName !== '-') {
                $subCatId = $this->resolveSubCategory($catId, $groupName);
            }

            // ── 3. Product name/description required ─────────────────
            $productName = $this->stringOrNull(
                $row['product_name'] ??
                $row['product_description'] ??
                $row['product_details'] ??
                $row['description'] ?? null
            );

            if ($productName === null) {
                $this->report['skipped'][] = "Row {$rowNum}: Missing product name/description, skipped.";
                continue;
            }

            // ── 4. Parse remaining fields ────────────────────────────
            $model = $this->stringOrNull(
                $row['product_model'] ??
                $row['model'] ?? null
            );

            $rate = $this->numericOrDefault(
                $row['rate'] ??
                $row['rate_price'] ??
                $row['price'] ?? null,
                0
            );

            // Parse Quantity & UOM safely
            $rawQty = $row['quantity'] ?? $row['qty'] ?? null;
            $uom = $this->stringOrNull($row['unit_uom'] ?? $row['uom'] ?? $row['unit'] ?? null);

            $qty = null;
            $stockQty = 0;

            if (is_string($rawQty) && !is_numeric($rawQty)) {
                // E.g. "NO", "NOS", "SET", "UNIT"
                $cleanText = strtoupper(trim($rawQty));
                if (!$uom) {
                    $uom = ($cleanText === 'NO') ? 'NOS' : $cleanText;
                }
                // When "NO" or text is given in quantity column, store NULL for quantity and 0 for stock
                $qty = null;
                $stockQty = 0;
            } elseif (is_numeric($rawQty)) {
                $numVal = (int) $rawQty;
                if ($numVal <= 0) {
                    $qty = null;
                    $stockQty = 0;
                } else {
                    $qty = $numVal;
                    $stockQty = $numVal;
                }
                if (!$uom) {
                    $uom = 'NOS';
                }
            } else {
                // Empty / null cell
                $qty = null;
                $stockQty = 0;
                if (!$uom) {
                    $uom = 'NOS';
                }
            }

            $hsn    = $this->stringOrNull($row['hsn'] ?? null);
            $partno = $this->stringOrNull($row['part_no'] ?? $row['partno'] ?? null);
            
            // Store NULL if GST is blank/empty
            $rawGst = $this->numericOrNull($row['gst'] ?? $row['gst_percent'] ?? null);
            $gst    = $rawGst !== null ? (string)$rawGst : null;

            // ── 5. Duplicate Safety (updateOrCreate) ──────────────────
            $product = null;
            if ($model) {
                $product = Product::where('productmodel', $model)->where('productname', $productName)->first()
                    ?? Product::where('productname', $productName)->first();
            } else {
                $product = Product::where('productname', $productName)->first();
            }

            $catName = ProductCategory::find($catId)?->category_name ?? 'N/A';

            if ($product) {
                $product->update([
                    'productname'     => $productName,
                    'productmodel'    => $model,
                    'Productcategory' => (string) $catId,
                    'sub_cat_id'      => $subCatId,
                    'rate'            => (string) $rate,
                    'quantity'        => $qty,
                    'uom'             => $uom,
                    'gst'             => $gst,
                    'hsn'             => $hsn,
                    'partno'          => $partno,
                    'productdes'      => $productName,
                ]);

                $this->report['updated'][] = "Row {$rowNum}: {$productName}" . ($model ? " [{$model}]" : "");
                $status = 'Updated';
            } else {
                $product = Product::create([
                    'productname'     => $productName,
                    'productmodel'    => $model,
                    'Productcategory' => (string) $catId,
                    'sub_cat_id'      => $subCatId,
                    'rate'            => (string) $rate,
                    'quantity'        => $qty,
                    'uom'             => $uom,
                    'gst'             => $gst,
                    'hsn'             => $hsn,
                    'partno'          => $partno,
                    'productdes'      => $productName,
                ]);

                $this->report['created'][] = "Row {$rowNum}: {$productName}" . ($model ? " [{$model}]" : "");
                $status = 'Created';
            }

            // ── 6. Create / Update Product Stock ─────────────────────
            ProductStock::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'total_stock'     => $stockQty,
                    'available_stock' => $stockQty,
                    'used_stock'      => 0,
                ]
            );

            $this->report['products_list'][] = [
                'id'       => $product->id,
                'model'    => $model ?? '-',
                'name'     => $productName,
                'category' => $catName,
                'rate'     => $rate,
                'uom'      => $uom,
                'quantity' => $qty,
                'status'   => $status,
            ];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Resolve Category — find existing or create new with smart alias matching
    // ─────────────────────────────────────────────────────────────────────────
    private function resolveCategory(string $name): int
    {
        $trimmed = trim($name);
        $key = $this->normalise($trimmed);

        if (isset($this->categoryCache[$key])) {
            return $this->categoryCache[$key];
        }

        // Handle common shorthand aliases like "MHE -STD", "MHE-STD" -> "MHE Standard"
        if ($key === 'mhe-std' || $key === 'mhestd' || $key === 'mhestandard') {
            $mheStd = ProductCategory::where('id', 1)
                ->orWhereRaw("REPLACE(LOWER(category_name), ' ', '') LIKE '%mhestandard%'")
                ->first();
            if ($mheStd) {
                $this->categoryCache[$key] = $mheStd->id;
                return $mheStd->id;
            }
        }

        $cat = ProductCategory::whereRaw(
            "REPLACE(LOWER(category_name), ' ', '') = ?", [$key]
        )->first();

        if (!$cat) {
            $lower = strtolower($trimmed);
            $type  = str_contains($lower, 'rack') ? 'rack'
                   : (str_contains($lower, 'pallet') ? 'pallet' : 'mhe');

            $subCategory = str_contains($lower, 'special') ? 'special' : 'standard';

            $cat = ProductCategory::create([
                'category_name' => $trimmed,
                'sub_category'  => $subCategory,
                'type'          => $type,
                'dispatch_time' => null,
            ]);
        }

        $this->categoryCache[$key] = $cat->id;
        return $cat->id;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Resolve SubCategory — find existing or create new
    // ─────────────────────────────────────────────────────────────────────────
    private function resolveSubCategory(int $catId, string $name): int
    {
        $trimmed = trim($name);
        $key = $catId . '|' . $this->normalise($trimmed);

        if (isset($this->subCategoryCache[$key])) {
            return $this->subCategoryCache[$key];
        }
        $norm = $this->normalise($trimmed);

        // Special handling: "ELECTRIC STACKER" must map to "Electric Stacker - Ride on" (ID 17), NOT "Semi Electric Stacker" (ID 5)
        if ($norm === 'electricstacker' || (str_contains($norm, 'electricstacker') && !str_contains($norm, 'semi'))) {
            $rideOn = ProductSubCategory::where('cat_id', $catId)
                ->where('subcategory_name', 'LIKE', '%Electric Stacker%Ride on%')
                ->first();
            if ($rideOn) {
                $this->subCategoryCache[$key] = $rideOn->id;
                return $rideOn->id;
            }
        }

        $sub = ProductSubCategory::where('cat_id', $catId)
            ->whereRaw(
                "REPLACE(LOWER(subcategory_name), ' ', '') = ?",
                [$norm]
            )->first();

        if (!$sub) {
            // Also check partial matching (e.g. "ELECTRIC STACKER" matches "Electric Stacker - Ride on")
            $norm = $this->normalise($trimmed);
            $sub = ProductSubCategory::where('cat_id', $catId)
                ->whereRaw("REPLACE(LOWER(subcategory_name), ' ', '') LIKE ?", ["%{$norm}%"])
                ->first();
        }

        if (!$sub) {
            $sub = ProductSubCategory::firstOrCreate([
                'cat_id'           => $catId,
                'subcategory_name' => $trimmed,
            ]);
        }

        $this->subCategoryCache[$key] = $sub->id;
        return $sub->id;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Value helpers
    // ─────────────────────────────────────────────────────────────────────────
    private function numericOrNull(mixed $v): mixed
    {
        if (is_null($v) || (is_float($v) && is_nan($v))) return null;
        if (is_string($v)) {
            $clean = preg_replace('/[^\d.]/', '', $v);
            return (is_numeric($clean) && $clean !== '') ? (float) $clean : null;
        }
        if (!is_numeric($v)) return null;
        return (float) $v;
    }

    private function numericOrDefault(mixed $v, mixed $default): mixed
    {
        if (is_null($v) || (is_float($v) && is_nan($v))) return $default;
        if (is_string($v)) {
            $clean = preg_replace('/^[^\d]*/u', '', $v);
            $clean = str_replace([',', ' '], '', $clean);
            $clean = preg_replace('/[^\d.]/', '', $clean);
            return (is_numeric($clean) && $clean !== '') ? (float) $clean : $default;
        }
        if (!is_numeric($v)) return $default;
        return (float) $v;
    }

    private function stringOrNull(mixed $v): ?string
    {
        if (is_null($v)) return null;
        if (is_float($v) && is_nan($v)) return null;
        $s = trim((string) $v);
        return ($s === '' || strtolower($s) === 'nan') ? null : $s;
    }

    private function normalise(string $v): string
    {
        return strtolower(str_replace([' ', '-', '_'], '', $v));
    }
}
