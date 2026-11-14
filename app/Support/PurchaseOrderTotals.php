<?php

namespace App\Support;

class PurchaseOrderTotals
{
    public static function calculate(array $unitPrices, array $quantities, array $lineDiscounts, array $gstRates, float $overallDiscountPercentage): array
    {
        $lines = [];
        $grossSubtotal = 0.0;
        $remainingTaxableTotal = 0.0;

        foreach ($unitPrices as $index => $unitPrice) {
            $gross = round((float) $unitPrice * (float) $quantities[$index], 2);
            $discountPercentage = max(0, min(100, (float) ($lineDiscounts[$index] ?? 0)));
            $discountAmount = round($gross * $discountPercentage / 100, 2);
            $afterLineDiscount = max(0, $gross - $discountAmount);

            $lines[$index] = [
                'unit_price' => (float) $unitPrice,
                'quantity' => (float) $quantities[$index],
                'gross' => $gross,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'after_line_discount' => $afterLineDiscount,
                'gst_percentage' => (float) ($gstRates[$index] ?? 18),
            ];
            $grossSubtotal += $gross;
            $remainingTaxableTotal += $afterLineDiscount;
        }

        $overallDiscountAmount = round($remainingTaxableTotal * $overallDiscountPercentage / 100, 2);
        $allocatedOverallDiscount = 0.0;
        $gstTotal = 0.0;
        $taxableTotal = 0.0;
        $lastKey = array_key_last($lines);

        foreach ($lines as $index => &$line) {
            if ($index === $lastKey) {
                $lineOverallDiscount = round($overallDiscountAmount - $allocatedOverallDiscount, 2);
            } else {
                $lineOverallDiscount = $remainingTaxableTotal > 0
                    ? round($overallDiscountAmount * $line['after_line_discount'] / $remainingTaxableTotal, 2)
                    : 0;
                $allocatedOverallDiscount += $lineOverallDiscount;
            }

            $line['overall_discount_amount'] = $lineOverallDiscount;
            $line['taxable'] = max(0, round($line['after_line_discount'] - $lineOverallDiscount, 2));
            $line['gst_amount'] = round($line['taxable'] * $line['gst_percentage'] / 100, 2);
            $line['total_amount'] = round($line['taxable'] + $line['gst_amount'], 2);
            $taxableTotal += $line['taxable'];
            $gstTotal += $line['gst_amount'];
        }
        unset($line);

        return [
            'lines' => $lines,
            'gross_subtotal' => round($grossSubtotal, 2),
            'taxable_total' => round($taxableTotal, 2),
            'gst_total' => round($gstTotal, 2),
            'overall_discount_amount' => $overallDiscountAmount,
        ];
    }
}
