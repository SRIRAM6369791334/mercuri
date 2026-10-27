<?php

namespace Tests\Unit;

use App\Helpers\QuotationRevisionHelper;
use App\Models\Quotation;
use Tests\TestCase;

class QuotationRevisionNumberingTest extends TestCase
{
    public function test_revision_generation_lifecycle_flow()
    {
        // 1. Initial base quotation
        $base = new Quotation(['quota_id' => 'MSE/2026/9999']);
        $base->id = 9001;

        // Simulate DB lookup: initially only base exists
        // Test parsing
        $parsed = QuotationRevisionHelper::parseRevision($base->quota_id);
        $this->assertEquals(0, $parsed['major']);
        $this->assertEquals(0, $parsed['minor']);

        // First internal edit (not approved, not sent)
        $next = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 0, 1);
        $this->assertEquals('MSE/2026/9999(R0-1)', $next);

        // Second internal edit
        $next2 = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 0, 2);
        $this->assertEquals('MSE/2026/9999(R0-2)', $next2);

        // Third internal edit
        $next3 = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 0, 3);
        $this->assertEquals('MSE/2026/9999(R0-3)', $next3);

        // First external revision (approved / sent to customer and returned)
        $ext1 = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 1, 0);
        $this->assertEquals('MSE/2026/9999(R1)', $ext1);

        // Internal edits under R1
        $ext1_sub1 = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 1, 1);
        $this->assertEquals('MSE/2026/9999(R1-1)', $ext1_sub1);

        $ext1_sub2 = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 1, 2);
        $this->assertEquals('MSE/2026/9999(R1-2)', $ext1_sub2);

        // Second external revision (R1 approved / sent to customer and returned)
        $ext2 = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 2, 0);
        $this->assertEquals('MSE/2026/9999(R2)', $ext2);

        // Internal edits under R2
        $ext2_sub1 = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 2, 1);
        $this->assertEquals('MSE/2026/9999(R2-1)', $ext2_sub1);
    }

    public function test_qualifies_for_external_revision_flag()
    {
        $q = new Quotation();
        $q->sent_to_customer = 1;
        $this->assertTrue(QuotationRevisionHelper::qualifiesForExternalRevision($q));

        $q2 = new Quotation();
        $q2->sent_to_customer = 0;
        $q2->quto_app = 2;
        $q2->dis_app = 2;
        $q2->freight_approval = 0;
        $q2->charge_approval_status = 'Approved';
        $this->assertTrue(QuotationRevisionHelper::qualifiesForExternalRevision($q2));

        // When discount approval is pending (1)
        $q3 = new Quotation();
        $q3->sent_to_customer = 0;
        $q3->quto_app = 0;
        $q3->dis_app = 1;
        $this->assertFalse(QuotationRevisionHelper::qualifiesForExternalRevision($q3));

        // When freight approval is pending (1)
        $q4 = new Quotation();
        $q4->sent_to_customer = 0;
        $q4->freight_approval = 1;
        $this->assertFalse(QuotationRevisionHelper::qualifiesForExternalRevision($q4));

        // When charges pending
        $q5 = new Quotation();
        $q5->sent_to_customer = 0;
        $q5->charge_approval_status = 'Pending Approval';
        $this->assertFalse(QuotationRevisionHelper::qualifiesForExternalRevision($q5));
    }
}
