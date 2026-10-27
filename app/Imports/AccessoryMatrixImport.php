<?php

namespace App\Imports;

use App\Models\Accessory;
use App\Models\Product;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccessoryMatrixImport
{
    private bool $resetBeforeImport;

    public array $report = [
        'products_matched'   => 0,
        'accessories_mapped' => 0,
        'skipped_products'   => [],
    ];

    public function __construct(bool $resetBeforeImport = false)
    {
        $this->resetBeforeImport = $resetBeforeImport;
    }

    public function import(Worksheet $sheet): void
    {
        if ($this->resetBeforeImport) {
            Accessory::query()->delete();
        }

        $highestRow = $sheet->getHighestRow();
        $headerRow  = 2;

        // Extract accessory column names from Row 2 (Columns C to M)
        $accessoryCols = [];
        for ($col = 'C'; $col <= 'M'; $col++) {
            $name = trim((string) $sheet->getCell($col . $headerRow)->getValue());
            if ($name !== '' && !str_starts_with(strtoupper($name), 'TOTAL')) {
                $accessoryCols[$col] = $name;
            }
        }

        // Preload products for fast matching
        $products = Product::select('id', 'productname', 'productmodel', 'Productcategory')->get();
        $productsByNorm = [];
        foreach ($products as $p) {
            $norm = $this->normalise($p->productname);
            $productsByNorm[$norm] = $p;
        }

        // Pass 1: Build a pricing map for Lead-Acid models by family to fallback for Lithium models
        $leadAcidPricing = [];
        for ($r = 3; $r <= $highestRow; $r++) {
            $rawProdName = trim((string) $sheet->getCell('B' . $r)->getValue());
            if ($rawProdName === '' || $this->isLithium($rawProdName)) {
                continue;
            }

            $family = $this->detectFamily($rawProdName);
            if (!$family) continue;

            foreach ($accessoryCols as $col => $accName) {
                $rawVal = $sheet->getCell($col . $r)->getValue();
                if (is_numeric($rawVal) && (float)$rawVal > 0) {
                    if (!isset($leadAcidPricing[$family][$accName])) {
                        $leadAcidPricing[$family][$accName] = (float)$rawVal;
                    }
                }
            }
        }

        // Pass 2: Map accessories to all products
        for ($r = 3; $r <= $highestRow; $r++) {
            $rawProdName = trim((string) $sheet->getCell('B' . $r)->getValue());
            if ($rawProdName === '') {
                continue;
            }

            // Find matching product with smart matcher
            $product = $this->matchProduct($rawProdName, $productsByNorm, $products);

            if (!$product) {
                $this->report['skipped_products'][] = [
                    'row'  => $r,
                    'name' => $rawProdName,
                ];
                continue;
            }

            $this->report['products_matched']++;
            $catId = is_numeric($product->Productcategory) ? (int)$product->Productcategory : null;
            $family = $this->detectFamily($rawProdName);
            $isLi   = $this->isLithium($rawProdName);

            foreach ($accessoryCols as $col => $accName) {
                $rawVal = $sheet->getCell($col . $r)->getValue();
                $price  = null;

                if (!is_null($rawVal) && $rawVal !== '' && $rawVal !== '-' && is_numeric($rawVal)) {
                    $val = (float) $rawVal;
                    if ($val > 0) {
                        $price = $val;
                    }
                }

                // If Lithium-Ion and accessory cell is blank, fallback to Lead Acid family pricing (never for Additional Battery)
                if ($price === null && $isLi && $family && $accName !== 'Additional Battery' && isset($leadAcidPricing[$family][$accName])) {
                    $price = $leadAcidPricing[$family][$accName];
                }

                // 1. Remove existing DB record if it already exists
                Accessory::where('product_id', $product->id)
                    ->where('name', $accName)
                    ->delete();

                // 2. If valid price in sheet, create fresh record
                if ($price !== null && $price > 0) {
                    Accessory::create([
                        'product_id'          => $product->id,
                        'name'                => $accName,
                        'price'               => $price,
                        'tax_percent'         => 18.00,
                        'status'              => 'active',
                        'product_category_id' => $catId,
                    ]);

                    $this->report['accessories_mapped']++;
                }
            }
        }

        // CQD15 6200mm (DB ID 174) fallback: copy accessories from CQD15 7400mm (ID 175) if ID 174 has 0 accessories
        $cqd174 = Product::find(174);
        if ($cqd174 && Accessory::where('product_id', 174)->count() === 0) {
            $refAccs = Accessory::where('product_id', 175)->get();
            foreach ($refAccs as $ra) {
                Accessory::updateOrCreate(
                    ['product_id' => 174, 'name' => $ra->name],
                    ['price' => $ra->price, 'tax_percent' => $ra->tax_percent, 'status' => 'active', 'product_category_id' => (int)$cqd174->Productcategory]
                );
                $this->report['accessories_mapped']++;
            }
        }

        // Clean up dummy generic product ID 184 accessories
        Accessory::where('product_id', 184)->delete();
    }

    /**
     * Smart Product Matcher
     * 1. Exact normalized name match
     * 2. SP Series smart token match (Model, Fork Width, Battery Chemistry)
     * 3. Forklift Series smart token match (Model, Lift Height, Battery Chemistry)
     * 4. CQD Reach Truck smart token match (Model, Lift Height, Battery Chemistry)
     * 5. Stacker (ST12 / ST15) smart token match (Model, Subtype, Width, Lift Height, Battery Chemistry)
     * 6. Substring fallback match with safeguards
     */
    public function matchProduct(string $rawProdName, array $productsByNorm, $products): ?Product
    {
        // 1. Exact normalized match
        $normProd = $this->normalise($rawProdName);
        if (isset($productsByNorm[$normProd])) {
            return $productsByNorm[$normProd];
        }

        // 2. SP Series Smart Matching
        $spAttr = $this->extractSpAttributes($rawProdName);
        if ($spAttr) {
            foreach ($products as $p) {
                $dbAttr = $this->extractSpAttributes($p->productname);
                if ($dbAttr &&
                    $dbAttr['model'] === $spAttr['model'] &&
                    $dbAttr['width'] === $spAttr['width'] &&
                    $dbAttr['battery'] === $spAttr['battery']) {
                    return $p;
                }
            }
        }

        // 3. Forklift Series Smart Matching
        $fkAttr = $this->extractForkliftAttributes($rawProdName);
        if ($fkAttr) {
            foreach ($products as $p) {
                $dbAttr = $this->extractForkliftAttributes($p->productname);
                if ($dbAttr &&
                    $dbAttr['model'] === $fkAttr['model'] &&
                    $dbAttr['height'] === $fkAttr['height'] &&
                    $dbAttr['battery'] === $fkAttr['battery']) {
                    return $p;
                }
            }
        }

        // 4. CQD Reach Truck Series Smart Matching
        $cqdAttr = $this->extractCqdAttributes($rawProdName);
        if ($cqdAttr) {
            foreach ($products as $p) {
                if ($p->id == 184) continue; // Skip dummy generic ID 184
                $dbAttr = $this->extractCqdAttributes($p->productname);
                if ($dbAttr &&
                    $dbAttr['model'] === $cqdAttr['model'] &&
                    $dbAttr['height'] === $cqdAttr['height'] &&
                    $dbAttr['battery'] === $cqdAttr['battery']) {
                    return $p;
                }
            }
        }

        // 5. Stacker (ST12 / ST15) Series Smart Matching
        $stAttr = $this->extractStackerAttributes($rawProdName);
        if ($stAttr) {
            foreach ($products as $p) {
                $dbAttr = $this->extractStackerAttributes($p->productname);
                if ($dbAttr &&
                    $dbAttr['model'] === $stAttr['model'] &&
                    $dbAttr['height'] === $stAttr['height'] &&
                    $dbAttr['battery'] === $stAttr['battery'] &&
                    ($stAttr['width'] === null || $dbAttr['width'] === $stAttr['width']) &&
                    ($stAttr['sub'] === 'OTHER' || $dbAttr['sub'] === $stAttr['sub'])
                ) {
                    if ($stAttr['variant'] === 'WC' && $dbAttr['variant'] !== 'WC') continue;
                    if ($stAttr['variant'] !== 'WC' && $dbAttr['variant'] === 'WC') continue;
                    return $p;
                }
            }
        }

        // 6. Fallback: Partial substring match (Safeguarded against generic dummy names)
        foreach ($products as $p) {
            if ($p->id == 184) continue; // Never match dummy ID 184 via substring
            if (strlen($p->productname) < 25) continue; // Ignore short generic names
            if (str_contains($p->productname, $rawProdName) || str_contains($rawProdName, $p->productname)) {
                return $p;
            }
        }

        return null;
    }

    /**
     * Differentiate Battery Chemistry with zero ambiguity
     */
    public function normalizeBattery(string $text): string
    {
        $t = strtolower($text);
        if (preg_match('/(lithium[- ]?ion|li[- ]?ion|liion|lithium)/i', $t)) {
            return 'LITHIUM';
        }
        if (preg_match('/(lead[- ]?acid|leadacid|lead|battery\s*\d{2}v)/i', $t)) {
            return 'LEAD_ACID';
        }
        return 'UNKNOWN';
    }

    /**
     * Extract SP Series Attributes (Model, Fork Width, Battery Chemistry)
     */
    public function extractSpAttributes(string $text): ?array
    {
        if (!preg_match('/\b(SP20|SP25|SP36|SP50)\b/i', $text, $mModel)) {
            return null;
        }
        $model = strtoupper($mModel[1]);

        $width = null;
        if (preg_match('/(?:[xX\s])(520|685|720)\s*[wW]?\b/i', $text, $mWidth)) {
            $width = $mWidth[1];
        } elseif (preg_match('/(520|685|720)/i', $text, $mWidth)) {
            $width = $mWidth[1];
        }

        $battery = $this->normalizeBattery($text);

        return [
            'series'  => 'SP',
            'model'   => $model,
            'width'   => $width,
            'battery' => $battery,
        ];
    }

    /**
     * Extract Forklift Series Attributes (Model, Lift Height, Battery Chemistry)
     */
    public function extractForkliftAttributes(string $text): ?array
    {
        if (!preg_match('/\b(FB16R|FB20R|FB30R|FB50R)\b/i', $text, $mModel)) {
            return null;
        }
        $model = strtoupper($mModel[1]);

        $height = null;
        if (preg_match('/(?:lift\s*height|lif\s*height|lh)[-\s:]*(\d{4})/i', $text, $mH)) {
            $height = $mH[1];
        } elseif (preg_match('/(3000|4500|6000|6500)/i', $text, $mH)) {
            $height = $mH[1];
        }

        $battery = $this->normalizeBattery($text);

        return [
            'series'  => 'FORKLIFT',
            'model'   => $model,
            'height'  => $height,
            'battery' => $battery,
        ];
    }

    /**
     * Extract CQD Reach Truck Attributes (Model, Lift Height, Battery Chemistry)
     */
    public function extractCqdAttributes(string $text): ?array
    {
        if (!preg_match('/\b(CQD15|CQD20D|CQD20)\b/i', $text, $mModel)) {
            return null;
        }
        $model = strtoupper($mModel[1]);
        if ($model === 'CQD20') $model = 'CQD20D';

        $height = null;
        if (preg_match('/(?:lift\s*height|lh)[-\s:]*(\d{4,5})/i', $text, $mH)) {
            $height = $mH[1];
        } elseif (preg_match('/(\d{4,5})mm/i', $text, $mH)) {
            $height = $mH[1];
        }

        $battery = $this->normalizeBattery($text);
        if ($battery === 'UNKNOWN') {
            $battery = 'LEAD_ACID'; // Default for non-lithium CQD
        }

        return [
            'series'  => 'CQD',
            'model'   => $model,
            'height'  => $height,
            'battery' => $battery,
        ];
    }

    /**
     * Extract Stacker (ST12 / ST15) Attributes
     */
    public function extractStackerAttributes(string $text): ?array
    {
        if (!preg_match('/\b(ST12|ST15)\b/i', $text, $mModel)) {
            return null;
        }
        $model = strtoupper($mModel[1]);

        $sub = 'OTHER';
        if (stripos($text, 'Mono') !== false) $sub = 'MONO';
        elseif (stripos($text, 'FFTL') !== false) $sub = 'FFTL';
        elseif (stripos($text, 'Tele') !== false) $sub = 'TELE';

        $variant = 'STD';
        if (stripos($text, '-WC') !== false || stripos($text, ' WC') !== false) $variant = 'WC';
        elseif (stripos($text, '-SS') !== false || stripos($text, ' SS') !== false) $variant = 'SS';

        $width = null;
        if (preg_match('/(560|685|750)\s*[wW]/i', $text, $mW)) $width = $mW[1];
        elseif (preg_match('/(560|685|750)/i', $text, $mW)) $width = $mW[1];

        $height = null;
        if (preg_match('/(?:lift\s*height|lh)[-\s:]*(\d{4})/i', $text, $mH)) {
            $height = $mH[1];
        } elseif (preg_match('/(\d{4})mm/i', $text, $mH)) {
            $height = $mH[1];
        }

        $battery = $this->normalizeBattery($text);

        return [
            'series'  => 'STACKER',
            'model'   => $model,
            'sub'     => $sub,
            'variant' => $variant,
            'width'   => $width,
            'height'  => $height,
            'battery' => $battery,
        ];
    }

    private function isLithium(string $text): bool
    {
        return $this->normalizeBattery($text) === 'LITHIUM';
    }

    private function detectFamily(string $text): ?string
    {
        $n = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $text));
        if (str_contains($n, 'ST15WC')) return 'ST15WC';
        if (str_contains($n, 'ST15SS') || str_contains($n, 'ST15TELE')) return 'ST15SS';
        if (str_contains($n, 'ST12')) return 'ST12';
        if (str_contains($n, 'ST15')) return 'ST15';
        if (str_contains($n, 'SC15')) return 'SC15';
        if (str_contains($n, 'SC18')) return 'SC18';
        if (str_contains($n, 'CQD20')) return 'CQD20';
        if (str_contains($n, 'CQD15')) return 'CQD15';
        if (str_contains($n, 'SP20')) return 'SP20';
        if (str_contains($n, 'SP25')) return 'SP25';
        if (str_contains($n, 'SP36')) return 'SP36';
        if (str_contains($n, 'SP50')) return 'SP50';
        return null;
    }

    private function normalise(string $v): string
    {
        return strtolower(preg_replace('/[^A-Za-z0-9]/', '', $v));
    }
}
