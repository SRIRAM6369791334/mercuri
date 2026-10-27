<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSpecification;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductSpecificationController extends Controller
{
    //
    public function specifications()
{
    $products = Product::all();
    return view('pages.masters.product_specifications', compact('products'));
}

public function getSpecifications($productid)
{
    $specifications = ProductSpecification::where('productid', $productid)->get();
    return response()->json($specifications);
}

/**
 * Store / Update specifications for a product.
 *
 * Strategy: delete all existing specs for this product, then re-insert.
 * This handles add, edit, and remove in one clean operation.
 */
public function store(Request $request)
{
    $request->validate([
        'productid'       => 'required|exists:products,id',
        'description'     => 'required|array|min:1',
        'description.*'   => 'nullable|string|max:500',
        'unit'            => 'required|array',
        'unit.*'          => 'nullable|string|max:100',
        'specification'   => 'required|array',
        'specification.*' => 'nullable|string|max:500',
    ]);

    $productId = $request->productid;

    // Build rows, skip completely empty ones
    $rows = [];
    foreach ($request->description as $key => $description) {
        $description   = trim($description ?? '');
        $unit          = trim($request->unit[$key]          ?? '');
        $specification = trim($request->specification[$key] ?? '');

        if ($description === '' && $specification === '') {
            continue;  // skip blank rows
        }

        $rows[] = [
            'productid'     => $productId,
            'description'   => $description,
            'unit'          => $unit !== '' ? $unit : null,
            'specification' => $specification,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }

    if (empty($rows)) {
        return back()->with('error', 'No specification data to save.');
    }

    // Delete old → insert new (handles add + edit + remove in one step)
    ProductSpecification::where('productid', $productId)->delete();
    ProductSpecification::insert($rows);

    return back()->with('success', 'Specifications updated successfully.');
}



   public function bulkUploadForm()
    {
        return view('pages.masters.bulkupload_spec');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file        = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheetNames  = $spreadsheet->getSheetNames();
        } catch (\Exception $e) {
            return redirect()->back()->with('bulk_results', [
                'success' => [],
                'skipped' => [],
                'errors'  => ['Failed to read Excel file: ' . $e->getMessage()],
            ]);
        }

        $results  = ['success' => [], 'skipped' => [], 'errors' => []];
        $products = Product::select('id', 'productmodel', 'productname')->get();

        if ($products->isEmpty()) {
            return redirect()->back()->with('bulk_results', [
                'success' => [],
                'skipped' => [],
                'errors'  => ['No products found in the database. Please add products first.'],
            ]);
        }

        foreach ($sheetNames as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);

            $specs = $this->parseSpecifications($sheet);

            if (empty($specs)) {
                $results['skipped'][] = "Sheet \"{$sheetName}\" — no specification data found (check column layout)";
                continue;
            }

            $matchedProducts = $this->matchProducts($sheetName, $products, $specs);

            if (empty($matchedProducts)) {
                $results['skipped'][] = "Sheet \"{$sheetName}\" — no matching product found";
                continue;
            }

            foreach ($matchedProducts as $matchedProduct) {
                try {
                    ProductSpecification::where('productid', $matchedProduct->id)->delete();

                    $sanitizedSpecs = $this->sanitizeSpecsForProduct($matchedProduct, $specs);

                    $rows = [];
                    foreach ($sanitizedSpecs as $spec) {
                        $rows[] = [
                            'productid'     => $matchedProduct->id,
                            'description'   => $spec['description'],
                            'unit'          => $spec['unit'],
                            'specification' => $spec['specification'],
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ];
                    }

                    ProductSpecification::insert($rows);

                } catch (\Exception $e) {
                    $results['errors'][] = "Sheet \"{$sheetName}\" Product {$matchedProduct->id} — DB error: " . $e->getMessage();
                }
            }

            $results['success'][] =
                "Sheet \"{$sheetName}\" → " . count($matchedProducts) . " product(s) mapped — "
                . count($specs) . " specifications uploaded";
        }

        return redirect()->back()->with('bulk_results', $results);
    }

    // =========================================================================
    // SMART MATCHING  (3-pass strategy)
    // =========================================================================
    //
    //  PASS 1 — Full match WITH battery gate
    //    Token overlap score ≥ 0.50 AND battery type compatible.
    //
    //  PASS 2 — Relaxed match WITHOUT battery gate
    //    For cases where sheet says LI-ION but only a Lead-Acid product exists in DB
    //    (or vice-versa). Uses same token score ≥ 0.50, ignores battery type.
    //
    //  PASS 3 — Model-code-only match (for products like CQD20D, CQD15D)
    //    When sheet and product share the same model code but the LH/height numbers
    //    differ between sheet name and product description (data inconsistency).
    //    Extracts just the base model code (letters+digits before first hyphen/space
    //    that is followed by LH) and finds the best model-only match.

    /*
    // =========================================================================
    // PREVIOUS CONDITION (COMMENTED OUT AS PER USER REQUEST)
    // =========================================================================
    private function matchProducts_old(string $sheetName, $products): array
    {
        $snNorm = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $sheetName));
        $snHeight = $this->extractLiftHeightFromText($sheetName);
        $snIsFFTL = str_contains($snNorm, 'FFTL') || $snNorm === 'ST15SSLH3600LIION';
        $snHas750 = str_contains($sheetName, '750') || $snNorm === 'ST15WCFFTLLH5100';
        $snIsWC   = str_contains($snNorm, 'ST15WC') || str_contains($snNorm, 'WC');
        $snIsSS   = str_contains($snNorm, 'ST15SS') || (str_contains($snNorm, 'SS') && !str_contains($snNorm, 'WC'));

        $matched = [];

        // 1. Precise mechanical dimension matching (covers all variants of model & height)
        if ($snHeight && ($snIsWC || $snIsSS)) {
            foreach ($products as $p) {
                $pNorm = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', ($p->productname ?? '') . ($p->productmodel ?? '')));
                $pHeight = $this->extractLiftHeightFromText($p->productname ?? '');
                $pIsNameTele = str_contains(strtoupper($p->productname ?? ''), 'TELE');
                $pIsNameFFTL = str_contains(strtoupper($p->productname ?? ''), 'FFTL');

                $pHas750 = str_contains($p->productname ?? '', '750W') || str_contains($p->productname ?? '', '750');
                $pIsWC = str_contains($pNorm, 'ST15WC');
                $pIsSS = str_contains($pNorm, 'ST15SS') || (str_contains($pNorm, 'ST15') && !str_contains($pNorm, 'WC'));

                if (!$pHeight || $snHeight !== $pHeight) continue;
                if ($snIsWC && !$pIsWC) continue;
                if ($snIsSS && !$pIsSS) continue;

                if ($snIsWC) {
                    if ($snHas750 && !$pHas750) continue;
                    if (!$snHas750 && $pHas750) continue;
                }

                if ($snIsFFTL) {
                    if ($pIsNameTele && !$pIsNameFFTL) continue;
                } else {
                    if ($pIsNameFFTL && !$pIsNameTele) continue;
                }

                $matched[$p->id] = $p;
            }
        }

        // 2. Fallback to 3-pass algorithm for all other equipment types (CQD, FB, SC, etc.)
        if (empty($matched)) {
            $single = $this->matchProduct($sheetName, $products);
            if ($single) {
                $matched[$single->id] = $single;
            }
        }

        return array_values($matched);
    }
    */

    /**
     * NEW 4-POINT DNA FINGERPRINT MATCHING ENGINE (ZERO-GUESS ARCHITECTURE)
     * Matches products strictly against 4 core dimensions:
     * 1. Chassis / Width Type (MONO vs SS vs WC_685 vs WC_750 vs PLAIN)
     * 2. Mast Type (MONO vs TELE vs FFTL)
     * 3. Lift Height (exact numeric mm)
     * 4. Battery Technology (Lithium-Ion vs Lead Acid)
     */
    private function matchProducts(string $sheetName, $products, array $sheetSpecs = []): array
    {
        $sheetDna = $this->extractProductDna($sheetName, '', $sheetSpecs);
        $matched = [];

        // Tier 1: Deterministic matching for equipment series (ST15, ST12, SC15, SC18, CQD, FB, etc.)
        if ($sheetDna['series']) {
            foreach ($products as $p) {
                $pDna = $this->extractProductDna($p->productname ?? '', $p->productmodel ?? '');

                // 1. Series must match exactly
                if ($pDna['series'] !== $sheetDna['series']) continue;

                // 2. Battery Gate: dedicated LI sheets strictly match LI products.
                // For series without dedicated LI sheets (e.g. ST15, ST12, SC15, CQD),
                // baseline mechanical sheets match both LA and LI variants (battery is sanitized dynamically).
                if ($sheetDna['battery'] === 'LI' && $pDna['battery'] !== 'LI') {
                    continue;
                }
                $hasDedicatedLi = in_array($sheetDna['series'], ['FB20R', 'FB30R', 'FB50R', 'SP20', 'SP25', 'SP36']);
                if ($sheetDna['battery'] === 'LA' && $pDna['battery'] === 'LI' && $hasDedicatedLi) {
                    continue;
                }

                // 3. Width & Wheel Gate for Pallet Trucks (PV25E, SP20, SP25, SP36, SP50, PTE15)
                if (in_array($sheetDna['series'], ['PV25E', 'SP20', 'SP25', 'SP36', 'SP50', 'PTE15'])) {
                    if ($sheetDna['width'] && $pDna['width'] && $sheetDna['width'] !== $pDna['width']) {
                        continue;
                    }
                    if ($sheetDna['wheel'] && $pDna['wheel'] && $sheetDna['wheel'] !== $pDna['wheel']) {
                        continue;
                    }
                }

                // 4. Chassis Match for Stackers (ST15, ST12)
                if ($sheetDna['series'] === 'ST15' || $sheetDna['series'] === 'ST12') {
                    if ($sheetDna['chassis'] === 'MONO') {
                        if ($pDna['chassis'] !== 'MONO') continue;
                    } elseif ($sheetDna['chassis'] === 'PLAIN') {
                        // Standard wrap-over sheet (e.g. ST15 -LH- 2450)
                        if ($pDna['chassis'] !== 'PLAIN') continue;
                    } elseif ($sheetDna['chassis'] === 'SS') {
                        // ST15-SS sheet (e.g. ST15-SS-TELE-LH-4200, ST15-SS-FFTL-LH-4200)
                        // Matches both ST15-SS products and ST15-WC products (since ST15-WC shares the straddle mast structure)
                        $isSsOrWc = ($pDna['chassis'] === 'SS' || str_starts_with($pDna['chassis'], 'WC'));
                        if (!$isSsOrWc) continue;
                    }
                } elseif ($sheetDna['chassis'] !== 'STD') {
                    if ($sheetDna['chassis'] !== $pDna['chassis']) continue;
                }

                // 5. Lift Height Match
                if ($sheetDna['height']) {
                    if ($pDna['height'] !== $sheetDna['height']) continue;
                }

                // 6. Mast Match (Tele vs FFTL)
                if ($sheetDna['mast'] && $sheetDna['mast'] !== 'TELE_FFTL') {
                    if ($pDna['mast'] && $pDna['mast'] !== 'TELE_FFTL' && $sheetDna['mast'] !== $pDna['mast']) {
                        continue;
                    }
                }

                $matched[$p->id] = $p;
            }
        }

        // Tier 2: Exact Base Model / Non-series fallback (e.g. HGL-1000, HSL-2000, etc.)
        if (empty($matched) && empty($sheetDna['series'])) {
            $single = $this->matchProduct($sheetName, $products);
            if ($single) {
                $matched[$single->id] = $single;
            }
        }

        return array_values($matched);
    }

    /**
     * Extracts a mechanical DNA fingerprint from product details or excel sheet.
     */
    private function extractProductDna(string $name, string $model = '', array $specs = []): array
    {
        $combined = strtoupper($name . ' ' . $model);

        // 1. In-sheet specs extraction
        $inSheetModel   = null;
        $inSheetHeight  = null;
        $inSheetBattery = null;
        $inSheetWidth   = null;
        $inSheetMast    = null;

        foreach ($specs as $s) {
            $desc = strtoupper(trim($s['description'] ?? ''));
            $val  = trim($s['specification'] ?? '');
            if ($val === '') continue;

            if (in_array($desc, ['MODEL', 'PRODUCT MODEL', 'EQUIPMENT MODEL'])) {
                $inSheetModel = strtoupper($val);
            } elseif (!str_contains($desc, 'CAPACITY') && preg_match('/(?:LIFT\s*HEIGHT|LIFTING\s*HEIGHT|MAX\s*FORK\s*HEIGHT|MAX\s*LIFT\s*HEIGHT|^LH$)/i', $desc)) {
                if (preg_match('/(\d{4})/', $val, $m)) {
                    $inSheetHeight = $m[1];
                }
            } elseif (str_contains($desc, 'BATTERY TYPE') || $desc === 'BATTERY') {
                $inSheetBattery = strtoupper($val);
            } elseif (str_contains($desc, 'WIDTH') || str_contains($desc, 'CHASSIS')) {
                if (preg_match('/(750|720|685|695|570|560|550|540|520)/', $val, $m)) {
                    $inSheetWidth = $m[1];
                }
            } elseif (str_contains($desc, 'MAST')) {
                $inSheetMast = strtoupper($val);
            }
        }

        // 2. Base Series / Equipment Family
        $series = null;
        if (preg_match('/ST[- ]*15/i', $combined) || ($inSheetModel && preg_match('/ST[- ]*15/i', $inSheetModel))) {
            $series = 'ST15';
        } elseif (preg_match('/ST[- ]*12/i', $combined) || ($inSheetModel && preg_match('/ST[- ]*12/i', $inSheetModel))) {
            $series = 'ST12';
        } elseif (preg_match('/SC[- ]*15/i', $combined)) {
            $series = 'SC15';
        } elseif (preg_match('/SC[- ]*18/i', $combined)) {
            $series = 'SC18';
        } elseif (preg_match('/CQD[- ]*20D/i', $combined)) {
            $series = 'CQD20D';
        } elseif (preg_match('/CQD[- ]*20/i', $combined)) {
            $series = 'CQD20';
        } elseif (preg_match('/CQD[- ]*15/i', $combined)) {
            $series = 'CQD15';
        } elseif (preg_match('/CDD[- ]*15R[- ]*E/i', $combined)) {
            $series = 'CDD15R-E';
        } elseif (preg_match('/FB[- ]*20R/i', $combined)) {
            $series = 'FB20R';
        } elseif (preg_match('/FB[- ]*30R/i', $combined)) {
            $series = 'FB30R';
        } elseif (preg_match('/FB[- ]*50R/i', $combined)) {
            $series = 'FB50R';
        } elseif (preg_match('/PV[- ]*25E/i', $combined)) {
            $series = 'PV25E';
        } elseif (preg_match('/(?:PTE[- ]*15|WALKIE\s*ELECTRIC\s*PALLET\s*TRUCK)/i', $combined)) {
            $series = 'PTE15';
        } elseif (preg_match('/SP[- ]*20/i', $combined)) {
            $series = 'SP20';
        } elseif (preg_match('/SP[- ]*25/i', $combined)) {
            $series = 'SP25';
        } elseif (preg_match('/SP[- ]*36/i', $combined)) {
            $series = 'SP36';
        } elseif (preg_match('/SP[- ]*50/i', $combined)) {
            $series = 'SP50';
        } elseif (preg_match('/MS[- ]*10B/i', $combined)) {
            $series = 'MS10B';
        } elseif (preg_match('/MS[- ]*10/i', $combined)) {
            $series = 'MS10';
        } elseif (preg_match('/SHP[- ]*10/i', $combined)) {
            $series = 'SHP10';
        }

        // Width extraction (for pallet trucks & stackers)
        $width = null;
        if (preg_match('/(?:[Xx\- ]|^)(750|720|685|695|570|560|550|540|520|68|55)(?:[WwMm\)\-\/\s]|$)/i', $combined, $m)) {
            $foundW = $m[1];
            if ($foundW === '55') $foundW = '550';
            elseif ($foundW === '68') $foundW = '685';
            $width = $foundW;
        } elseif ($inSheetWidth) {
            $width = $inSheetWidth;
        }

        // Wheel type (for hand pallet trucks like PV25E)
        $wheel = null;
        if (str_contains($combined, 'NYLON')) {
            $wheel = 'NYLON';
        } elseif (str_contains($combined, 'PU')) {
            $wheel = 'PU';
        } elseif ($series === 'PV25E') {
            $wheel = 'NYLON';
        }

        // 3. Chassis / Width Type
        $chassis = 'STD';
        if (str_contains($combined, 'MONO')) {
            $chassis = 'MONO';
        } elseif (str_contains($combined, 'WC') || str_contains($combined, 'WIDE CHASSIS') || ($inSheetModel && str_contains($inSheetModel, 'WC'))) {
            if ($width === '750' || str_contains($combined, '750')) {
                $chassis = 'WC_750';
            } elseif ($width === '685' || str_contains($combined, '685')) {
                $chassis = 'WC_685';
            } else {
                $chassis = 'WC';
            }
        } elseif (preg_match('/(?:ST[- ]*15[- ]*SS|ST15SS|\bSS\b)/i', $combined) || ($inSheetModel && str_contains($inSheetModel, 'SS'))) {
            $chassis = 'SS';
        } elseif ($series === 'ST15' || $series === 'ST12') {
            $chassis = 'PLAIN';
        }

        // 4. Mast Type (Prioritize productname description over productmodel typo)
        $nameUpper = strtoupper($name);
        $modelUpper = strtoupper($model . ' ' . ($inSheetModel ?? '') . ' ' . ($inSheetMast ?? ''));
        $mast = null;

        if (str_contains($nameUpper, 'MONO') || str_contains($modelUpper, 'MONO')) {
            $mast = 'MONO';
        } elseif (str_contains($nameUpper, 'FFTL') || str_contains($nameUpper, 'TRIPLEX')) {
            $mast = 'FFTL';
        } elseif (str_contains($nameUpper, 'TELE')) {
            $mast = 'TELE';
        } elseif (str_contains($modelUpper, 'FFTL') || str_contains($modelUpper, 'TRIPLEX')) {
            $mast = 'FFTL';
        } elseif (str_contains($modelUpper, 'TELE')) {
            $mast = 'TELE';
        }

        // 5. Lift Height
        $height = null;
        if (preg_match('/(?:Lift\s*Height|LH|Height)[^\d]*(\d{4})/i', $name, $m)) {
            $height = $m[1];
        } elseif (preg_match('/(\d{4})\s*mm/i', $name, $m)) {
            $height = $m[1];
        } elseif ($inSheetHeight) {
            $height = $inSheetHeight;
        } else {
            $height = $this->extractLiftHeightFromText($combined);
        }

        // Stacker lift heights <= 1600mm (e.g. 1580mm, 1600mm) are physically Mono mast
        if ($height && (int)$height <= 1600 && ($series === 'ST15' || $series === 'ST12')) {
            $mast = 'MONO';
            if ($chassis === 'PLAIN' || $chassis === 'STD') {
                $chassis = 'MONO';
            }
        }

        // 6. Battery Technology
        $battery = null;
        $isLi = preg_match('/(?:LITHIUM|LI-ION|LIION|\bLI\b)/i', $combined) ||
                ($inSheetBattery && preg_match('/(?:LITHIUM|LI-ION|LIION|\bLI\b)/i', $inSheetBattery));
        $isLead = preg_match('/(?:LEAD\s*ACID|LEAD|\bLA\b)/i', $combined) ||
                  ($inSheetBattery && preg_match('/(?:LEAD\s*ACID|LEAD|\bLA\b)/i', $inSheetBattery));

        if ($isLi && !$isLead) {
            $battery = 'LI';
        } elseif ($isLead && !$isLi) {
            $battery = 'LA';
        }

        return [
            'series'   => $series,
            'chassis'  => $chassis,
            'width'    => $width,
            'wheel'    => $wheel,
            'mast'     => $mast,
            'height'   => $height,
            'battery'  => $battery,
        ];
    }

    private function extractLiftHeightFromText(string $text): ?string
    {
        if (preg_match('/(?:Lift\s*Height|LH|Height)[^\d]*(\d{4})/i', $text, $m)) {
            return $m[1];
        }
        if (preg_match('/(\d{4})\s*mm/i', $text, $m)) {
            return $m[1];
        }
        if (preg_match('/(?:SS|WC|TELE|FFTL|LH|MS10B?|SC[- ]*15|SC[- ]*18|ST[- ]*15|ST[- ]*12|CDD[- ]*15)[^\d]*(\d{4})/i', $text, $m)) {
            return $m[1];
        }
        return null;
    }

    private function matchProduct(string $sheetName, $products)
    {
        $baseModel = $this->extractBaseModel($sheetName);
        $sheetBaseNorm = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $baseModel));

        // ── PASS 1: Full match with battery gate & strict model keyword ───────────────
        $best      = null;
        $bestScore = -1;

        foreach ($products as $product) {
            if ($sheetBaseNorm !== '') {
                $prodBase = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $product->productmodel ?? ''));
                $nameBase = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $product->productname  ?? ''));
                if (!str_contains($prodBase, $sheetBaseNorm) && !str_contains($nameBase, $sheetBaseNorm)) {
                    continue; // Strict model keyword gate: skip products that do not match the sheet's model code
                }
            }

            $score = $this->scoreMatch($sheetName, $product->productname ?? '', $product->productmodel ?? '', true);
            if ($score > $bestScore) {
                $bestScore = $score;
                $best      = $product;
            }
        }

        if ($bestScore >= 0.5) {
            return $best;
        }

        // ── PASS 2: Relaxed match without battery gate & strict model keyword ─────────
        $best      = null;
        $bestScore = -1;

        foreach ($products as $product) {
            if ($sheetBaseNorm !== '') {
                $prodBase = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $product->productmodel ?? ''));
                $nameBase = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $product->productname  ?? ''));
                if (!str_contains($prodBase, $sheetBaseNorm) && !str_contains($nameBase, $sheetBaseNorm)) {
                    continue; // Strict model keyword gate: skip products that do not match the sheet's model code
                }
            }

            $score = $this->scoreMatch($sheetName, $product->productname ?? '', $product->productmodel ?? '', false);
            if ($score > $bestScore) {
                $bestScore = $score;
                $best      = $product;
            }
        }

        if ($bestScore >= 0.5) {
            return $best;
        }

        // ── PASS 3: Model-code-only match ─────────────────────────────────────
        // Handles products like CQD20D, CQD15D where the LH numbers in sheet
        // names don't match the lift heights in product descriptions.
        // Extract just the base model code and score by that alone.
        if ($baseModel !== '') {
            $best      = null;
            $bestScore = -1;

            foreach ($products as $product) {
                $prodBase = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $product->productmodel ?? ''));
                $nameBase = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $product->productname  ?? ''));

                if (str_contains($prodBase, $sheetBaseNorm) || str_contains($nameBase, $sheetBaseNorm)) {
                    // Among all model-matching products, prefer highest token score
                    $score = $this->scoreMatch($sheetName, $product->productname ?? '', $product->productmodel ?? '', false);
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $best      = $product;
                    }
                }
            }

            // Accept model-only match at lower threshold (0.3)
            if ($bestScore >= 0.3) {
                return $best;
            }
        }

        return null;
    }

    /**
     * Score how well a sheet name matches a product.
     *
     * @param  bool $useBatteryGate  If true, incompatible battery types return -1.
     */
    private function scoreMatch(
        string $sheetName,
        string $productName,
        string $productModel,
        bool   $useBatteryGate
    ): float {
        $sheetTokens = $this->extractTokens($sheetName);

        if (empty($sheetTokens)) {
            return 0;
        }

        if ($useBatteryGate) {
            $sheetHasLi   = $this->hasLiIon($sheetName);
            $sheetHasLead = $this->hasLeadAcid($sheetName);
            $prodHasLi    = $this->hasLiIon($productName)    || $this->hasLiIon($productModel);
            $prodHasLead  = $this->hasLeadAcid($productName)  || $this->hasLeadAcid($productModel);

            if ($sheetHasLi   && ! $prodHasLi)   return -1;
            if ($sheetHasLead && ! $prodHasLead)  return -1;
        }

        $prodTokens = array_unique(array_merge(
            $this->extractTokens($productName),
            $this->extractTokens($productModel)
        ));

        $common = array_intersect($sheetTokens, $prodTokens);
        $score  = count($common) / count($sheetTokens);

        // Tie-breaker: prefer lead-acid when sheet has no battery keyword
        $sheetHasLi   = $this->hasLiIon($sheetName);
        $sheetHasLead = $this->hasLeadAcid($sheetName);
        $prodHasLead  = $this->hasLeadAcid($productName) || $this->hasLeadAcid($productModel);

        if (! $sheetHasLi && ! $sheetHasLead && $prodHasLead) {
            $score += 0.001;
        }

        return $score;
    }

    /**
     * Extract the base model code from a sheet name.
     * "CQD20D-LH-8600"   → "CQD20D"
     * "CQD15D LH-7400"   → "CQD15D"
     * "FB20R- LH-6500"   → "FB20R"
     * "ST15-LH-3600"     → "ST15"
     */
    private function extractBaseModel(string $sheetName): string
    {
        $sheetName = trim($sheetName);

        // Remove spaces around hyphens for consistent splitting
        $sheetName = preg_replace('/\s*-\s*/', '-', $sheetName);

        // Split on hyphen or space
        $parts = preg_split('/[-\s]+/', $sheetName);

        if (empty($parts)) {
            return '';
        }

        // Take parts until we hit a pure-number or "LH" token
        $model = '';
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') continue;

            // Stop if we hit "LH" (lift height marker) or a standalone number
            if (strtoupper($part) === 'LH' || (is_numeric($part) && strlen($part) >= 4)) {
                break;
            }

            $model .= ($model ? '-' : '') . $part;
        }

        return $model;
    }

    /**
     * Extract meaningful tokens from text.
     *
     * - Strips mm/MM suffix from dimensions  (6500mm → 6500, 1150X520mm → 1150X520)
     * - Uppercases everything
     * - Splits on any non-alphanumeric character
     * - Removes generic noise words that appear in all product names
     */
    private function extractTokens(string $text): array
    {
        $text = preg_replace('/(\d+)[Mm][Mm]/', '$1', $text);
        // Separate concatenated numbers and letters like 5100LI -> 5100 LI, ST15 -> ST 15
        $text = preg_replace('/(\d+)([A-Za-z]+)/', '$1 $2', $text);
        $text = strtoupper($text);

        preg_match_all('/[A-Z0-9]+/', $text, $matches);

        $noise = [
            'MAKE', 'WITH', 'AND', 'FOR', 'THE', 'MAINI',
            'CAPACITY', 'MASTS', 'DOUBLE', 'AC', 'CONTROLLER',
            'SOLID', 'TYRES', 'TYRE', 'FFL', 'CHARGER', 'SIDESHIFT',
            'BATTERY', 'ELECTRIC', 'FORKLIFT', 'TRUCK', 'PALLET',
            'HYDRAULIC', 'HAND', 'STACKER', 'TRIPLEX', 'SEMI',
            'KG', 'AH', 'V', 'MM',
        ];

        return array_values(array_diff($matches[0], $noise));
    }

    /** True if text contains li-ion / lithium-ion / LIION / LITHIUM */
    private function hasLiIon(string $text): bool
    {
        $n = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $text));
        return str_contains($n, 'LIION') || str_contains($n, 'LITHIUM') || str_contains($n, 'LI');
    }

    /** True if text contains lead-acid / lead acid / LEADACID */
    private function hasLeadAcid(string $text): bool
    {
        $n = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $text));
        return str_contains($n, 'LEADACID') || str_contains($n, 'LEAD');
    }

    // =========================================================================
    // PARSE SPECIFICATIONS FROM SHEET
    // =========================================================================
    //
    // FIX: Accepts ALL column header name variants found in your Excel files:
    //
    //   Col A variants  → Particulars / PARTICULARS / PARTICULAR / DESCRIPTION / DESCRIPTIONS
    //   Col B variants  → Unit / UNIT / UNITS / MODEL
    //   Col C variants  → Technical Specification / TECHNICAL SPECIFICATIONS /
    //                     Technical Specifications / SPECIFICATION / SPECIFICATIONS
    //
    // Detection: scans col-A rows 1–10 for any value that STARTS WITH
    //            "PARTICULAR" or "DESCRIPTION" (case-insensitive).

    private function parseSpecifications($sheet): array
    {
        $specs      = [];
        $highestRow = $sheet->getHighestRow();
        $headerRow  = null;

        for ($row = 1; $row <= min($highestRow, 10); $row++) {
            $cellA = strtoupper(trim((string) $sheet->getCell("A{$row}")->getValue()));

            // Accept: PARTICULAR, PARTICULARS, DESCRIPTION, DESCRIPTIONS
            if (str_starts_with($cellA, 'PARTICULAR') || str_starts_with($cellA, 'DESCRIPTION')) {
                $headerRow = $row;
                break;
            }
        }

        if (! $headerRow) {
            return [];
        }

        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $description   = trim((string) $sheet->getCell("A{$row}")->getValue());
            $unit          = trim((string) $sheet->getCell("B{$row}")->getValue());
            $specification = trim((string) $sheet->getCell("C{$row}")->getValue());

            if ($description === '' && $specification === '') {
                continue;
            }

            $specs[] = [
                'description'   => $description,
                'unit'          => $unit !== '' ? $unit : null,
                'specification' => $specification,
            ];
        }

        return $specs;
    }

    private function sanitizeSpecsForProduct($product, array $specs): array
    {
        $prodName = $product->productname ?? '';
        $prodModel = $product->productmodel ?? '';
        $combined = strtoupper($prodName . ' ' . $prodModel);

        $isLead = preg_match('/(?:LEAD\s*ACID|LEAD|\bLA\b)/i', $combined);
        $isLi   = preg_match('/(?:LITHIUM|LI-ION|LIION|\bLI\b)/i', $combined);

        // Extract battery capacity (e.g. 24V, 345AH -> 24 x 345)
        $extractedCapacity = null;
        if (preg_match('/(\d{2,3}\s*[Vv])\s*[,|\-|\/]?\s*(\d{2,3}\s*[Aa][Hh])/i', $prodName, $m)) {
            $volts = preg_replace('/[^\d]/', '', $m[1]);
            $ah    = preg_replace('/[^\d]/', '', $m[2]);
            $extractedCapacity = "{$volts} x {$ah}";
        }

        $nameUpper  = strtoupper($prodName);
        $modelUpper = strtoupper($prodModel);

        $isWC = str_contains($combined, 'WC') || str_contains($combined, 'WIDE CHASSIS');
        $isSS = str_contains($combined, 'SS') && !$isWC;

        // Mast type detection (prioritize productname description over productmodel typo)
        $isMono = false;
        $isFFTL = false;
        $isTele = false;

        if (str_contains($nameUpper, 'MONO') || (preg_match('/1580|1600/', $prodName) && (str_contains($combined, 'ST15') || str_contains($combined, 'ST12')))) {
            $isMono = true;
        } elseif (str_contains($nameUpper, 'FFTL') || str_contains($nameUpper, 'TRIPLEX')) {
            $isFFTL = true;
        } elseif (str_contains($nameUpper, 'TELE')) {
            $isTele = true;
        } elseif (str_contains($modelUpper, 'FFTL') || str_contains($modelUpper, 'TRIPLEX')) {
            $isFFTL = true;
        } elseif (str_contains($modelUpper, 'TELE')) {
            $isTele = true;
        } else {
            $isTele = true;
        }

        // Check width for WC
        $forkWidth = null;
        if (preg_match('/(?:[Xx\- ]|^)(750|720|685|695|570|560|550|540|520)(?:[WwMm\)\-\/\s]|$)/i', $combined, $m)) {
            $forkWidth = $m[1];
        } elseif (str_contains($combined, '750')) {
            $forkWidth = '750';
        } elseif (str_contains($combined, '685')) {
            $forkWidth = '685';
        }

        $sanitized = [];
        foreach ($specs as $spec) {
            $descUpper = strtoupper(trim($spec['description'] ?? ''));
            $specVal = $spec['specification'];

            if ($descUpper === 'MODEL') {
                if ($isMono) {
                    if (str_contains($combined, 'ST15')) {
                        $specVal = 'ST15-MONO';
                    } elseif (str_contains($combined, 'ST12')) {
                        $specVal = 'ST12-MONO';
                    }
                } elseif ($isWC) {
                    $specVal = $isFFTL ? 'ST15WC-FFTL' : 'ST15WC-TELE';
                } elseif ($isSS) {
                    $specVal = $isFFTL ? 'ST15-SS-FFTL' : 'ST15-SS-TELE';
                } elseif (str_contains($combined, 'ST15')) {
                    $specVal = $isFFTL ? 'ST15-FFTL' : 'ST15-TELE';
                } elseif (str_contains($combined, 'ST12')) {
                    $specVal = $isFFTL ? 'ST12-FFTL' : 'ST12-TELE';
                }
            } elseif ($descUpper === 'CAPACITY') {
                if (str_contains($combined, 'ST12') || str_contains($combined, '1200KG')) {
                    $specVal = '1200';
                }
            } elseif ($descUpper === 'BATTERY TYPE') {
                if ($isLead) {
                    $specVal = 'Lead Acid';
                } elseif ($isLi) {
                    $specVal = 'Lithium-Ion';
                }
            } elseif ($descUpper === 'BATTERY CAPACITY') {
                if ($extractedCapacity !== null) {
                    $specVal = $extractedCapacity;
                }
            } elseif ($descUpper === 'FORK WIDTH' || $descUpper === 'FORK WIDTH (B5)') {
                if ($isWC && $forkWidth) {
                    $specVal = $forkWidth;
                } elseif ($isSS) {
                    $specVal = '560';
                }
            } elseif ($isWC) {
                // Wide carriage chassis dimensions
                if ($descUpper === 'PUMP MOTOR') {
                    $specVal = '24V / 2 Kw';
                } elseif ($descUpper === 'LOWERING SPEED UNLADEN') {
                    $specVal = '0.15';
                } elseif ($descUpper === 'NET WEIGHT W/O BATTERY') {
                    $specVal = '1050';
                } elseif ($descUpper === 'NET WEIGHT WITH BATTERY') {
                    $specVal = '1250';
                } elseif ($descUpper === 'TURING RADIUS' || $descUpper === 'TURNING RADIUS') {
                    $specVal = '1865';
                } elseif ($descUpper === 'OVERALL LENGTH') {
                    $specVal = '2240';
                } elseif (str_contains($descUpper, 'AISLE WIDTH')) {
                    $specVal = '2490';
                }
            }

            $sanitized[] = [
                'description'   => $spec['description'],
                'unit'          => $spec['unit'],
                'specification' => $specVal,
            ];
        }

        return $sanitized;
    }
}
