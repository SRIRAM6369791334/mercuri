<?php

namespace App\Helpers;

class IndianNumberHelper
{
    public static function numberToIndianWords($number)
    {
        $no = floor($number);
        $decimal = round($number - $no, 2) * 100;

        $words = [
            0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
            5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
            14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen',
            17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen',
            20 => 'twenty', 30 => 'thirty', 40 => 'forty',
            50 => 'fifty', 60 => 'sixty', 70 => 'seventy',
            80 => 'eighty', 90 => 'ninety'
        ];

        $digits = ['', 'hundred', 'thousand', 'lakh', 'crore'];
        $str = [];
        $i = 0;

        while ($no > 0) {
            $divider = ($i == 1) ? 10 : 100;
            $number = $no % $divider;
            $no = floor($no / $divider);

            if ($number) {
                $plural = (($number > 9 && $i > 1) ? 's' : '');
                $hundred = ($i == 1 && $str[0]) ? ' and ' : '';

                if ($number < 21) {
                    $str[] = $words[$number] . ' ' . $digits[$i] . $plural . $hundred;
                } else {
                    $str[] = $words[floor($number / 10) * 10] . ' ' .
                             $words[$number % 10] . ' ' .
                             $digits[$i] . $plural . $hundred;
                }
            } else {
                $str[] = '';
            }
            $i++;
        }

        return ucfirst(trim(preg_replace('/\s+/', ' ', implode(' ', array_reverse($str))))) . ' only';
    }

    public static function formatIndianCurrency($number, $decimals = 2)
    {
        if ($number === null || $number === '') {
            return '0.00';
        }

        $num = (float) $number;
        $isNegative = $num < 0;
        $num = abs($num);

        $parts = explode('.', number_format($num, $decimals, '.', ''));
        $intPart = $parts[0];
        $decPart = isset($parts[1]) ? '.' . $parts[1] : '';

        if (strlen($intPart) <= 3) {
            $formattedInt = $intPart;
        } else {
            $lastThree = substr($intPart, -3);
            $remaining = substr($intPart, 0, -3);
            $formattedInt = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $remaining) . ',' . $lastThree;
        }

        return ($isNegative ? '-' : '') . $formattedInt . $decPart;
    }
}
