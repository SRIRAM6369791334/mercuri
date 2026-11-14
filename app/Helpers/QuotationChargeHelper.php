<?php

namespace App\Helpers;

class QuotationChargeHelper
{
    public static function resolveFixedCharges(array $products): ?array
    {
        if (empty($products)) {
            return null;
        }

        $known = ['Scissors Lift', 'Deck Leveler', 'Goods Lift'];
        foreach ($products as $product) {
            if (in_array($product, $known, true)) {
                return [15000, 15000];
            }
        }

        return null;
    }
}
