<?php

namespace Tests\Unit;

use App\Helpers\QuotationChargeHelper;
use PHPUnit\Framework\TestCase;

class QuotationChargeHelperTest extends TestCase
{
    public function test_it_returns_fixed_charge_for_known_products(): void
    {
        $this->assertSame([15000, 15000], QuotationChargeHelper::resolveFixedCharges(['Scissors Lift']));
        $this->assertSame([15000, 15000], QuotationChargeHelper::resolveFixedCharges(['Deck Leveler']));
        $this->assertSame([15000, 15000], QuotationChargeHelper::resolveFixedCharges(['Goods Lift']));
    }

    public function test_it_returns_null_for_other_products(): void
    {
        $this->assertNull(QuotationChargeHelper::resolveFixedCharges(['Standard Lift']));
        $this->assertNull(QuotationChargeHelper::resolveFixedCharges([]));
    }
}
