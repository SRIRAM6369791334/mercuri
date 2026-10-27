<?php

namespace Tests\Unit;

use App\Helpers\QuotationRevisionHelper;
use PHPUnit\Framework\TestCase;

class QuotationRevisionHelperTest extends TestCase
{
    public function test_get_base_quotation_no()
    {
        $this->assertEquals('MSE/2026/0032', QuotationRevisionHelper::getBaseQuotationNo('MSE/2026/0032'));
        $this->assertEquals('MSE/2026/0032', QuotationRevisionHelper::getBaseQuotationNo('MSE/2026/0032(R0-1)'));
        $this->assertEquals('MSE/2026/0032', QuotationRevisionHelper::getBaseQuotationNo('MSE/2026/0032(R1)'));
        $this->assertEquals('MSE/2026/0032', QuotationRevisionHelper::getBaseQuotationNo('MSE/2026/0032(R1-2)'));
        $this->assertEquals('MSE/2026/0032', QuotationRevisionHelper::getBaseQuotationNo('MSE/2026/0032(R15)'));
    }

    public function test_parse_revision()
    {
        $this->assertEquals(['major' => 0, 'minor' => 0], QuotationRevisionHelper::parseRevision('MSE/2026/0032'));
        $this->assertEquals(['major' => 0, 'minor' => 1], QuotationRevisionHelper::parseRevision('MSE/2026/0032(R0-1)'));
        $this->assertEquals(['major' => 0, 'minor' => 5], QuotationRevisionHelper::parseRevision('MSE/2026/0032(R0-5)'));
        $this->assertEquals(['major' => 1, 'minor' => 0], QuotationRevisionHelper::parseRevision('MSE/2026/0032(R1)'));
        $this->assertEquals(['major' => 1, 'minor' => 2], QuotationRevisionHelper::parseRevision('MSE/2026/0032(R1-2)'));
        $this->assertEquals(['major' => 3, 'minor' => 4], QuotationRevisionHelper::parseRevision('MSE/2026/0032(R3-4)'));
        $this->assertEquals(['major' => 0, 'minor' => 2], QuotationRevisionHelper::parseRevision('MSE/2026/0032(2)'));
    }

    public function test_format_quota_id()
    {
        $this->assertEquals('MSE/2026/0032', QuotationRevisionHelper::formatQuotaId('MSE/2026/0032', 0, 0));
        $this->assertEquals('MSE/2026/0032(R0-1)', QuotationRevisionHelper::formatQuotaId('MSE/2026/0032', 0, 1));
        $this->assertEquals('MSE/2026/0032(R1)', QuotationRevisionHelper::formatQuotaId('MSE/2026/0032', 1, 0));
        $this->assertEquals('MSE/2026/0032(R1-2)', QuotationRevisionHelper::formatQuotaId('MSE/2026/0032', 1, 2));
    }
}
