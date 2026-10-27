<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class QuotationHelper
{
    /* -------------------------------------------------
     | EXISTING: TOTALS EXTRACTION (KEEP AS IS)
     ------------------------------------------------- */
    public static function readQuotationTotals($filePath)
    {
        if (!file_exists($filePath)) {
            return self::emptyResult();
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['xls', 'xlsx', 'csv'])) {
            return self::emptyResult();
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
        } catch (ReaderException $e) {
            return self::emptyResult();
        }

        $results = self::emptyResult();

        foreach ($sheet->getRowIterator() as $row) {

            $rowValues = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowValues[] = $cell->getCalculatedValue();
            }

            foreach ($rowValues as $cellValue) {

                if (!is_string($cellValue)) continue;

                $label = strtolower(trim($cellValue));
                $numericValue = self::getLastNumericValue($rowValues);

                if (str_contains($label, 'basic total')) {
                    $results['basic_total'] = $numericValue;
                }
                if (str_contains($label, 'installation')) {
                    $results['installation_charges'] = $numericValue;
                }
                if (str_contains($label, 'freight')) {
                    $results['freight_charges'] = $numericValue;
                }
                if ($label === 'total') {
                    $results['total'] = $numericValue;
                }
                if (str_contains($label, 'gst')) {
                    $results['gst'] = $numericValue;
                }
                if (str_contains($label, 'grand total')) {
                    $results['grand_total'] = $numericValue;
                }
            }
        }

        return $results;
    }

    /* -------------------------------------------------
     | NEW: PARSE EXCEL → QUOTATION DATA (PRINT SAFE)
     ------------------------------------------------- */
    public static function parseQuotationExcel($filePath)
    {
        if (!file_exists($filePath)) return null;

        try {
            $sheet = IOFactory::load($filePath)->getActiveSheet();
        } catch (\Exception $e) {
            return null;
        }

        $data = [
            'header' => null,
            'rows'   => [],
            'totals' => [],
        ];

        $current = null;

        foreach ($sheet->getRowIterator() as $row) {

            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = trim((string) $cell->getCalculatedValue());
            }

            // 🔴 Red Header
            if (!empty($cells[1]) && stripos($cells[1], 'total pallet') !== false) {
                $data['header'] = $cells[1];
                continue;
            }

            // 🟦 New Item Start
            if (isset($cells[0]) && is_numeric($cells[0])) {
                $current = [
                    'sl'    => $cells[0],
                    'title' => $cells[1],
                    'lines' => [],
                    'qty'   => '',
                    'unit'  => '',
                    'rate'  => '',
                    'total' => '',
                ];
                $data['rows'][] = &$current;
                continue;
            }

            if (!$current) continue;

            // Specs / descriptions
            if (!empty($cells[1])) {
                $current['lines'][] = $cells[1];
            }

            // Qty / Rate / Total (last columns)
            if (!empty($cells[3])) $current['qty'] = $cells[3];
            if (!empty($cells[4])) $current['unit'] = $cells[4];
            if (!empty($cells[5])) $current['total'] = $cells[5];

            // Totals section
            if (stripos($cells[0], 'basic total') !== false) {
                $data['totals']['basic'] = $cells[5];
            }
            if (stripos($cells[0], 'freight') !== false) {
                $data['totals']['freight'] = $cells[5];
            }
            if (stripos($cells[0], 'gst') !== false) {
                $data['totals']['gst'] = $cells[5];
            }
            if (stripos($cells[0], 'grand total') !== false) {
                $data['totals']['grand'] = $cells[5];
            }
        }

        return $data;
    }

    /* -------------------------------------------------
     | RENDER EXCEL AS HTML WITH DYNAMIC TOTALS
     ------------------------------------------------- */
//     public static function renderExcelAsHtml(string $excelPath, ?object $quotation = null): ?string
//     {
//         if (!file_exists($excelPath)) {
//             return null;
//         }

//         try {
//             $spreadsheet = IOFactory::load($excelPath);
//             $sheet = $spreadsheet->getActiveSheet();
//         } catch (\Exception $e) {
//             return null;
//         }

//         $maxRow = $sheet->getHighestDataRow();
//         $maxCol = $sheet->getHighestDataColumn();
//         $maxColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);

//         // Track merged cells
//         $mergedCells = [];
//         foreach ($sheet->getMergeCells() as $mergedRange) {
//             $mergedCells[] = $mergedRange;
//         }

//         $html = '<table class="excel-table" style="width: 100%; border-collapse: collapse; font-family: Calibri, Arial, sans-serif; font-size: 11pt;">';

//         // Track cells to skip (already merged)
//         $skipCells = [];

//         // Track row positions
//         $basicTotalRow = null;
//         $installationRow = null;
//         $freightRow = null;
//         $totalRow = null;
//         $gstRow = null;
//         $grandTotalRow = null;

//         for ($row = 1; $row <= $maxRow; $row++) {

//     $rowText = '';

//     // 🔍 Read ALL columns in this row
//     for ($col = 1; $col <= $maxColIndex; $col++) {
//         $value = $sheet
//             ->getCellByColumnAndRow($col, $row)
//             ->getCalculatedValue();

//         if (is_string($value)) {
//             $rowText .= ' ' . strtolower(trim($value));
//         }
//     }

//     if (str_contains($rowText, 'basic total')) {
//         $basicTotalRow = $row;
//     }

//     if (str_contains($rowText, 'freight')) {
//         $freightRow = $row;
//     }

//     if ($rowText === ' total ' || str_contains($rowText, ' total ')) {
//         if (!str_contains($rowText, 'basic') && !str_contains($rowText, 'grand')) {
//             $totalRow = $row;
//         }
//     }

//     if (str_contains($rowText, 'gst')) {
//         $gstRow = $row;
//     }

//     if (str_contains($rowText, 'grand total')) {
//         $grandTotalRow = $row;
//     }
// }


        // First pass: identify all totals rows
        // for ($row = 1; $row <= $maxRow; $row++) {
        //     $cellA = strtolower(trim((string)$sheet->getCell('A' . $row)->getCalculatedValue()));
        //     $cellB = strtolower(trim((string)$sheet->getCell('B' . $row)->getCalculatedValue()));
        //     $combined = $cellA . ' ' . $cellB;

        //     if (stripos($combined, 'basic total') !== false) {
        //         $basicTotalRow = $row;
        //     }
        //     if (stripos($combined, 'installation') !== false) {
        //         $installationRow = $row;
        //     }
        //     if (stripos($combined, 'freight') !== false) {
        //         $freightRow = $row;
        //     }
        //     // Check for "Total" row (not Basic Total or Grand Total)
        //     if ($cellA === 'total' || $cellB === 'total') {
        //         if (!stripos($combined, 'basic') && !stripos($combined, 'grand')) {
        //             $totalRow = $row;
        //         }
        //     }
        //     if (stripos($combined, 'gst') !== false || stripos($combined, 'add gst') !== false) {
        //         $gstRow = $row;
        //     }
        //     if (stripos($combined, 'grand total') !== false) {
        //         $grandTotalRow = $row;
        //     }
        // }

        // // Render rows
        // for ($row = 1; $row <= $maxRow; $row++) {

        //     // Skip rows if quotation data is provided
        //     if ($quotation) {
        //         // Skip Freight, Total (old), GST (old), Grand Total (old)
        //         // Keep Installation row but skip it later
        //         $skipRows = array_filter([
        //             $freightRow,
        //             $totalRow,
        //             $gstRow,
        //             $grandTotalRow
        //         ]);



        //         if (in_array($row, $skipRows)) {
        //             continue;
        //         }
        //     }

        //     $html .= '<tr>';

        //     for ($col = 1; $col <= $maxColIndex; $col++) {
        //         $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;

        //         // Skip if this cell is part of a merged range (but not the starting cell)
        //         if (in_array($cellCoordinate, $skipCells)) {
        //             continue;
        //         }

        //         $cell = $sheet->getCell($cellCoordinate);
        //         $value = $cell->getCalculatedValue();

        //         // Replace Basic Total value if quotation data provided
        //         // if ($quotation && $basicTotalRow && $row === $basicTotalRow && $col === $maxColIndex) {
        //         //     $value = number_format($quotation->basic_total, 2);
        //         // }


        //         // Format the value
        //         if ($value instanceof \DateTime) {
        //             $value = $value->format('Y-m-d');
        //         } elseif (is_numeric($value) && $cell->getStyle()->getNumberFormat()->getFormatCode() !== 'General') {
        //             // Apply number formatting if needed
        //             $value = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::toFormattedString(
        //                 $value,
        //                 $cell->getStyle()->getNumberFormat()->getFormatCode()
        //             );
        //         }

        //         $value = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');

        //         // Check if this cell is the start of a merged range
        //         $colspan = 1;
        //         $rowspan = 1;
        //         foreach ($mergedCells as $mergedRange) {
        //             if (self::isCellStartOfMerge($cellCoordinate, $mergedRange)) {
        //                 list($colspan, $rowspan) = self::getMergeSpan($mergedRange);

        //                 // Add cells to skip
        //                 list($startCol, $startRow, $endCol, $endRow) = self::parseMergeRange($mergedRange);
        //                 for ($r = $startRow; $r <= $endRow; $r++) {
        //                     for ($c = $startCol; $c <= $endCol; $c++) {
        //                         $skipCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c) . $r;
        //                         if ($skipCoord !== $cellCoordinate) {
        //                             $skipCells[] = $skipCoord;
        //                         }
        //                     }
        //                 }
        //                 break;
        //             }
        //         }

        //         // Get cell styling
        //         $style = $cell->getStyle();
        //         $cssStyles = self::getCellStyles($style);

        //         // Build cell attributes
        //         $attrs = '';
        //         if ($colspan > 1) $attrs .= ' colspan="' . $colspan . '"';
        //         if ($rowspan > 1) $attrs .= ' rowspan="' . $rowspan . '"';
        //         if ($cssStyles) $attrs .= ' style="' . $cssStyles . '"';

        //         $html .= '<td' . $attrs . '>' . $value . '</td>';
        //     }

        //     $html .= '</tr>';

            // Insert new rows after Basic Total
    //         if ($quotation && $basicTotalRow && $row === $basicTotalRow) {


    //          if ($quotation->reqdis_amt > 0) {
    //     $html .= self::generateTotalRow(
    //         $sheet,
    //         $basicTotalRow,
    //         $maxColIndex,
    //         'Discount @ ' . number_format($quotation->req_dis, 2) . '%',
    //         $quotation->reqdis_amt,
    //         false,
    //         false,
    //         true
    //     );
    // }

    // /* 2️⃣ TOTAL AFTER DISCOUNT */
    // $taxableTotal = $quotation->basic_total - $quotation->reqdis_amt;

    // $html .= self::generateTotalRow(
    //     $sheet,
    //     $basicTotalRow,
    //     $maxColIndex,
    //     'Total',
    //     $taxableTotal,
    //     false,
    //     true
    // );

    // /* 3️⃣ GST (ON DISCOUNTED TOTAL) */
    // $html .= self::generateTotalRow(
    //     $sheet,
    //     $basicTotalRow,
    //     $maxColIndex,
    //     'Add GST @18%',
    //     $quotation->tax_total
    // );

    // /* 4️⃣ GRAND TOTAL */
    // $html .= self::generateTotalRow(
    //     $sheet,
    //     $basicTotalRow,
    //     $maxColIndex,
    //     'GRAND TOTAL',
    //     $quotation->grand_total,
    //     true
    // );
                // 1. Add Discount row (if discount exists and is greater than 0)
                // if (isset($quotation->req_dis) && $quotation->req_dis > 0) {
                //     $discountLabel = 'Discount @ ' . number_format($quotation->req_dis, 2) . '%';
                //     $discountAmount = isset($quotation->reqdis_amt) ? $quotation->reqdis_amt : 0;

                //     $html .= self::generateTotalRow(
                //         $sheet,
                //         $basicTotalRow,
                //         $maxColIndex,
                //         $discountLabel,
                //         $discountAmount,
                //         false,
                //         false,
                //         true // isDiscount - will format with negative sign
                //     );
                // }

                // // 2. Add Total row (Basic - Discount)
                // $subtotal = $quotation->basic_total - (isset($quotation->reqdis_amt) ? $quotation->reqdis_amt : 0);
                // $html .= self::generateTotalRow(
                //     $sheet,
                //     $totalRow ?? $basicTotalRow,
                //     $maxColIndex,
                //     'Total',
                //     $subtotal,
                //     false,
                //     true // bold
                // );

                // // 3. Add GST row
                // $gstAmount = isset($quotation->tax_total) ? $quotation->tax_total : 0;
                // $html .= self::generateTotalRow(
                //     $sheet,
                //     $gstRow ?? $basicTotalRow,
                //     $maxColIndex,
                //     'Add GST @18%',
                //     $gstAmount
                // );

                // // 4. Add Grand Total row
                // $grandTotal = isset($quotation->grand_total) ? $quotation->grand_total : 0;
                // $html .= self::generateTotalRow(
                //     $sheet,
                //     $grandTotalRow ?? $basicTotalRow,
                //     $maxColIndex,
                //     'GRAND TOTAL',
                //     $grandTotal,
                //     true // isGrandTotal
                // );
            // }
    //     }

    //     $html .= '</table>';

    //     return $html;
    // }

 public static function renderExcelAsHtml(string $excelPath, ?object $quotation = null): ?string
{
    if (!file_exists($excelPath)) return null;

    try {
        $spreadsheet = IOFactory::load($excelPath);
        $sheet = $spreadsheet->getActiveSheet();
    } catch (\Exception $e) {
        return null;
    }

    $maxRow = $sheet->getHighestDataRow();
    $maxCol = $sheet->getHighestDataColumn();
    $maxColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);

    /* -------------------------------------------------
     | FIND IMPORTANT ROWS
     ------------------------------------------------- */
    $basicTotalRow = null;
    $discountRow = null;
    $installationRow = null;
    $freightRow = null;
    $totalRow = null;
    $gstRow = null;
    $grandTotalRow = null;

    for ($row = 1; $row <= $maxRow; $row++) {

        $rowText = '';

        for ($col = 1; $col <= $maxColIndex; $col++) {
            $val = $sheet->getCell([$col, $row])->getCalculatedValue();
            if (is_string($val)) {
                $rowText .= ' ' . strtolower(trim($val));
            }
        }

        if (str_contains($rowText, 'basic total')) {
            $basicTotalRow = $row;
            $discountRow   = $row + 1; // 🔥 FORCE NEXT ROW AS DISCOUNT
        }

        if (str_contains($rowText, 'installation')) $installationRow = $row;
        if (str_contains($rowText, 'freight'))      $freightRow = $row;

        if (
    str_contains($rowText, ' total') &&
    !str_contains($rowText, 'basic total') &&
    !str_contains($rowText, 'grand total')
) {
    $totalRow = $row;
}



        if (str_contains($rowText, 'gst'))         $gstRow = $row;
        if (str_contains($rowText, 'grand total')) $grandTotalRow = $row;
    }

    /* -------------------------------------------------
     | HTML RENDER
     ------------------------------------------------- */
    $mergedCells = $sheet->getMergeCells();
    $skipCells = [];

    $html = '<table style="width:100%; border-collapse:collapse; font-family:Calibri; font-size:11pt;">';

    for ($row = 1; $row <= $maxRow; $row++) {

        $html .= '<tr>';

        for ($col = 1; $col <= $maxColIndex; $col++) {

            $coord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
            if (in_array($coord, $skipCells)) continue;

            $cell  = $sheet->getCell($coord);
            $value = $cell->getCalculatedValue();

            /* -------------------------------------------------
             | INLINE VALUE REPLACEMENT
             ------------------------------------------------- */
            if ($quotation) {

                // BASIC TOTAL
                if ($row === $basicTotalRow && $col === $maxColIndex) {
                    $value = number_format($quotation->basic_total, 2);
                }

                // ✅ DISCOUNT ROW (LABEL + AMOUNT)
                if ($row === $discountRow) {
                    if ($col === $maxColIndex - 1) {
                        $value = 'Discount @ ' . number_format($quotation->req_dis, 2) . '%';
                    }
                    if ($col === $maxColIndex) {
                        // $value = '- ' . number_format($quotation->reqdis_amt, 2);
                        $value = number_format($quotation->reqdis_amt, 2);
                    }
                }

                // INSTALLATION CHARGES
                if ($row === $installationRow && $col === $maxColIndex) {
                    $value = number_format($quotation->installation_charges ?? 0, 2);
                }

                // FREIGHT CHARGES
                if ($row === $freightRow && $col === $maxColIndex) {
                    $value = number_format($quotation->freight_charges ?? 0, 2);
                }

                // TOTAL AFTER DISCOUNT
                // if ($row === $totalRow && $col === $maxColIndex) {
                //     $value = number_format(
                //         $quotation->basic_total - $quotation->reqdis_amt,
                //         2
                //     );
                // }
                // ✅ TOTAL = BASIC − DISCOUNT + INSTALLATION + FREIGHT
if ($row === $totalRow && $col === $maxColIndex) {

    $total =
        ($quotation->basic_total ?? 0)
        - ($quotation->reqdis_amt ?? 0)
        + ($quotation->installation_charges ?? 0)
        + ($quotation->freight_charges ?? 0);

    $value = number_format($total, 2);
}


                // GST
                if ($row === $gstRow && $col === $maxColIndex) {
                    $value = number_format($quotation->tax_total, 2);
                }

                // GRAND TOTAL
                if ($row === $grandTotalRow && $col === $maxColIndex) {
                    $value = number_format($quotation->grand_total, 2);
                }
            }

            $value = htmlspecialchars($value ?? '', ENT_QUOTES);

            /* -------------------------------------------------
             | MERGED CELLS HANDLING
             ------------------------------------------------- */
            $colspan = 1;
            $rowspan = 1;

            foreach ($mergedCells as $range) {
                if (str_starts_with($range, $coord . ':')) {
                    [$colspan, $rowspan] = self::getMergeSpan($range);

                    [$sc, $sr, $ec, $er] = self::parseMergeRange($range);
                    for ($r = $sr; $r <= $er; $r++) {
                        for ($c = $sc; $c <= $ec; $c++) {
                            $skip = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c) . $r;
                            if ($skip !== $coord) $skipCells[] = $skip;
                        }
                    }
                    break;
                }
            }

            $style = self::getCellStyles($cell->getStyle());
            $html .= '<td colspan="'.$colspan.'" rowspan="'.$rowspan.'" style="'.$style.'">'.$value.'</td>';
        }

        $html .= '</tr>';
    }

    $html .= '</table>';
    return $html;
}





    private static function generateTotalRow(
    $sheet,
    $referenceRow,
    $maxColIndex,
    $label,
    $amount,
    $isGrandTotal = false,
    $isBold = false,
    $isDiscount = false
): string {

    $html = '<tr>';

    // Get reference style
    try {
        $style = $sheet->getCell('A' . $referenceRow)->getStyle();
    } catch (\Exception $e) {
        $style = null;
    }

    // 1️⃣ EMPTY LEFT COLUMNS
    for ($col = 1; $col <= $maxColIndex - 2; $col++) {
        $css = $style ? self::getCellStyles($style) : '';
        $html .= '<td style="' . $css . '"></td>';
    }

    // 2️⃣ LABEL COLUMN (SECOND LAST)
    $labelStyle = $style ? self::getCellStyles($style) : '';
    if ($isBold || $isGrandTotal) $labelStyle .= '; font-weight:bold';
    if ($isGrandTotal) $labelStyle .= '; background:#F0F0F0';

    $html .= '<td style="' . $labelStyle . '">' . htmlspecialchars($label) . '</td>';

    // 3️⃣ AMOUNT COLUMN (LAST)
    $amountStyle = $labelStyle . '; text-align:right';

    $formatted = number_format(abs($amount), 2);
    if ($isDiscount || $amount < 0) {
        $formatted = '- ' . $formatted;
    }

    $html .= '<td style="' . $amountStyle . '">' . $formatted . '</td>';

    $html .= '</tr>';
    return $html;
}

    /* -------------------------------------------------
     | HELPER: GENERATE TOTAL ROW
     ------------------------------------------------- */
    // private static function generateTotalRow(
    //     $sheet,
    //     $referenceRow,
    //     $maxColIndex,
    //     $label,
    //     $amount,
    //     $isGrandTotal = false,
    //     $isBold = false,
    //     $isDiscount = false
    // ): string {
    //     $html = '<tr>';

    //     // Get reference cell style
    //     try {
    //         $referenceCell = $sheet->getCell('A' . $referenceRow);
    //         $style = $referenceCell->getStyle();
    //     } catch (\Exception $e) {
    //         $style = null;
    //     }

    //     for ($col = 1; $col <= $maxColIndex; $col++) {
    //         $cssStyles = $style ? self::getCellStyles($style) : 'padding: 4px 8px; border: 1px solid #000';

    //         // Add extra styling for Grand Total or Bold rows
    //         if ($isGrandTotal || $isBold) {
    //             $cssStyles .= '; font-weight: bold';
    //         }
    //         if ($isGrandTotal) {
    //             $cssStyles .= '; background-color: #F0F0F0';
    //         }

    //         if ($col === 1) {
    //             // First column - empty
    //             $html .= '<td style="' . $cssStyles . '"></td>';
    //         } elseif ($col === 2) {
    //             // Second column - Label (merged across middle columns)
    //             $html .= '<td colspan="' . ($maxColIndex - 2) . '" style="' . $cssStyles . '">' . htmlspecialchars($label) . '</td>';
    //             break; // Skip remaining columns as we used colspan
    //         }
    //     }

    //     // Last column - amount
    //     $cssStyles = $style ? self::getCellStyles($style) : 'padding: 4px 8px; border: 1px solid #000';
    //     if ($isGrandTotal || $isBold) {
    //         $cssStyles .= '; font-weight: bold';
    //     }
    //     if ($isGrandTotal) {
    //         $cssStyles .= '; background-color: #F0F0F0';
    //     }

    //     // Format amount
    //     $formattedAmount = number_format(abs($amount), 2);

    //     // For discount, always show with minus sign
    //     if ($isDiscount) {
    //         $formattedAmount = '- ' . $formattedAmount;
    //     } elseif ($amount < 0) {
    //         $formattedAmount = '- ' . $formattedAmount;
    //     }

    //     $html .= '<td style="' . $cssStyles . ' text-align: right;">' . $formattedAmount . '</td>';

    //     $html .= '</tr>';
    //     return $html;
    // }

    /* -------------------------------------------------
     | CONVERT EXCEL TO PDF
     ------------------------------------------------- */
    public static function convertExcelToPdf(string $excelPath, string $pdfPath): bool
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelPath);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setFitToWidth(1)
            ->setFitToHeight(1);

        $sheet->getPageMargins()
            ->setTop(0.4)
            ->setBottom(0.4)
            ->setLeft(0.4)
            ->setRight(0.4);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        $writer->save($pdfPath);

        return true;
    }

    /* -------------------------------------------------
     | HELPER FUNCTIONS FOR HTML RENDERING
     ------------------------------------------------- */

    private static function isCellStartOfMerge(string $cellCoordinate, string $mergedRange): bool
    {
        $parts = explode(':', $mergedRange);
        return $parts[0] === $cellCoordinate;
    }

    private static function getMergeSpan(string $mergedRange): array
    {
        list($start, $end) = explode(':', $mergedRange);

        preg_match('/([A-Z]+)(\d+)/', $start, $startMatches);
        preg_match('/([A-Z]+)(\d+)/', $end, $endMatches);

        $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startMatches[1]);
        $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($endMatches[1]);
        $startRow = (int)$startMatches[2];
        $endRow = (int)$endMatches[2];

        $colspan = $endCol - $startCol + 1;
        $rowspan = $endRow - $startRow + 1;

        return [$colspan, $rowspan];
    }

    private static function parseMergeRange(string $mergedRange): array
    {
        list($start, $end) = explode(':', $mergedRange);

        preg_match('/([A-Z]+)(\d+)/', $start, $startMatches);
        preg_match('/([A-Z]+)(\d+)/', $end, $endMatches);

        $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startMatches[1]);
        $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($endMatches[1]);
        $startRow = (int)$startMatches[2];
        $endRow = (int)$endMatches[2];

        return [$startCol, $startRow, $endCol, $endRow];
    }

    private static function getCellStyles($style): string
    {
        $css = [];

        // Font styling
        $font = $style->getFont();
        if ($font->getBold()) {
            $css[] = 'font-weight: bold';
        }
        if ($font->getItalic()) {
            $css[] = 'font-style: italic';
        }
        if ($font->getSize()) {
            $css[] = 'font-size: ' . $font->getSize() . 'pt';
        }

        // Font color
        $fontColor = $font->getColor();
        if ($fontColor && $fontColor->getRGB() && $fontColor->getRGB() !== '000000') {
            $css[] = 'color: #' . $fontColor->getRGB();
        }

        // Background color
        $fill = $style->getFill();
        if ($fill->getFillType() === Fill::FILL_SOLID) {
            $bgColor = $fill->getStartColor();
            if ($bgColor && $bgColor->getRGB() && !in_array($bgColor->getRGB(), ['FFFFFFFF', 'FFFFFF', '00000000'])) {
                $css[] = 'background-color: #' . $bgColor->getRGB();
            }
        }

        // Borders
        $borders = $style->getBorders();
        $borderStyles = [];

        foreach (['top', 'right', 'bottom', 'left'] as $side) {
            $methodName = 'get' . ucfirst($side);
            $border = $borders->$methodName();

            if ($border->getBorderStyle() !== Border::BORDER_NONE) {
                $borderColor = $border->getColor();
                $color = $borderColor && $borderColor->getRGB() ? '#' . $borderColor->getRGB() : '#000000';
                $width = self::getBorderWidth($border->getBorderStyle());
                $borderStyles[] = "border-{$side}: {$width}px solid {$color}";
            }
        }

        $css = array_merge($css, $borderStyles);

        // Alignment
        $alignment = $style->getAlignment();

        $horizontal = $alignment->getHorizontal();
        if ($horizontal === Alignment::HORIZONTAL_CENTER) {
            $css[] = 'text-align: center';
        } elseif ($horizontal === Alignment::HORIZONTAL_RIGHT) {
            $css[] = 'text-align: right';
        } elseif ($horizontal === Alignment::HORIZONTAL_LEFT) {
            $css[] = 'text-align: left';
        }

        $vertical = $alignment->getVertical();
        if ($vertical === Alignment::VERTICAL_CENTER) {
            $css[] = 'vertical-align: middle';
        } elseif ($vertical === Alignment::VERTICAL_TOP) {
            $css[] = 'vertical-align: top';
        } elseif ($vertical === Alignment::VERTICAL_BOTTOM) {
            $css[] = 'vertical-align: bottom';
        }

        // Padding
        $css[] = 'padding: 4px 8px';

        return implode('; ', $css);
    }

    private static function getBorderWidth(string $borderStyle): int
    {
        switch ($borderStyle) {
            case Border::BORDER_THICK:
            case Border::BORDER_MEDIUM:
                return 2;
            case Border::BORDER_THIN:
            default:
                return 1;
        }
    }

    /* -------------------------------------------------
     | EXISTING HELPERS
     ------------------------------------------------- */
    private static function getLastNumericValue(array $row)
    {
        $number = null;
        foreach ($row as $value) {
            if (is_numeric($value)) {
                $number = $value;
            }
        }
        return $number;
    }

    private static function emptyResult()
    {
        return [
            'basic_total' => null,
            'installation_charges' => null,
            'freight_charges' => null,
            'total' => null,
            'gst' => null,
            'grand_total' => null,
        ];
    }
}
