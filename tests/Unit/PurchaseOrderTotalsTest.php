<?php

namespace Tests\Unit;

use App\Support\PurchaseOrderTotals;
use PHPUnit\Framework\TestCase;

class PurchaseOrderTotalsTest extends TestCase
{
    public function test_line_discount_precedes_overall_discount_and_gst(): void
    {
        $totals = PurchaseOrderTotals::calculate(
            [1000, 500],
            [1, 1],
            [10, 0],
            [18, 5],
            20
        );

        $this->assertSame(1500.0, $totals['gross_subtotal']);
        $this->assertSame(380.0, $totals['overall_discount_amount'] + $totals['lines'][0]['discount_amount']);
        $this->assertSame(1120.0, $totals['taxable_total']);
        $this->assertSame(149.6, $totals['gst_total']);
        $this->assertSame(720.0, $totals['lines'][0]['taxable']);
        $this->assertSame(400.0, $totals['lines'][1]['taxable']);
    }

    public function test_discount_allocation_rounds_without_losing_total_value(): void
    {
        $totals = PurchaseOrderTotals::calculate(
            [0.01, 1000],
            [1, 1],
            [0, 0],
            [18, 18],
            33.33
        );

        $this->assertSame($totals['overall_discount_amount'], round(
            $totals['lines'][0]['overall_discount_amount'] + $totals['lines'][1]['overall_discount_amount'],
            2
        ));
        $this->assertEqualsWithDelta(1000.01 - $totals['overall_discount_amount'], $totals['taxable_total'], 0.001);
    }
}
