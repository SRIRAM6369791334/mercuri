<?php

namespace Tests\Unit;

use App\Helpers\QuotationRevisionHelper;
use App\Models\Quotation;
use Tests\TestCase;

class QuotationRevisionNumberingTest extends TestCase
{
    public function test_revision_generation_lifecycle_flow()
    {
        // 1. Initial base quotation starts as (R0)
        $base = new Quotation(['quota_id' => 'MSE/2026/9999(R0)']);
        $base->id = 9001;

        $parsed = QuotationRevisionHelper::parseRevision($base->quota_id);
        $this->assertEquals(0, $parsed['major']);
        $this->assertEquals(0, $parsed['minor']);

        // Internal edits (not approved, not sent) remain at R0
        $internalEdit = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 0);
        $this->assertEquals('MSE/2026/9999(R0)', $internalEdit);

        // First revision created after approval or sending to client becomes R1
        $ext1 = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 1);
        $this->assertEquals('MSE/2026/9999(R1)', $ext1);

        // Internal edits under R1 remain R1 until approved/sent
        $internalUnderR1 = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 1);
        $this->assertEquals('MSE/2026/9999(R1)', $internalUnderR1);

        // Second revision after approval or sending to client becomes R2
        $ext2 = QuotationRevisionHelper::formatQuotaId('MSE/2026/9999', 2);
        $this->assertEquals('MSE/2026/9999(R2)', $ext2);
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

    public function test_generate_base_quotation_no()
    {
        $ctrl = new \App\Http\Controllers\Controller();
        $ref = new \ReflectionMethod($ctrl, 'generateBaseQuotationNo');
        $ref->setAccessible(true);
        $next = $ref->invoke($ctrl);

        $this->assertMatchesRegularExpression('/^MSE\/\d{4}\/\d{4}\(R0\)$/', $next);
    }

    public function test_lifecycle_in_place_update_and_new_revisions()
    {
        // 1. Initial quotation created as (R0)
        $q = Quotation::create([
            'id_lead' => 999999,
            'quota_id' => 'MSE/2026/8888(R0)',
            'revision_major' => 0,
            'revision_minor' => 0,
            'quto_app' => 0, // not approved
            'sent_to_customer' => 0, // not sent
        ]);

        $this->assertFalse(QuotationRevisionHelper::qualifiesForExternalRevision($q));

        // When internal edit occurs on unapproved R0:
        $baseNo = QuotationRevisionHelper::getBaseQuotationNo($q->quota_id);
        $parsed = QuotationRevisionHelper::parseRevision($q->quota_id);
        $normalizedNo = QuotationRevisionHelper::formatQuotaId($baseNo, $parsed['major']);
        $this->assertEquals('MSE/2026/8888(R0)', $normalizedNo);

        // In-place update:
        $q->update(['quota_id' => $normalizedNo, 'subtotal' => 5000]);
        $this->assertEquals(1, Quotation::where('quota_id', 'like', 'MSE/2026/8888%')->count());
        $this->assertEquals('MSE/2026/8888(R0)', $q->fresh()->quota_id);

        // 2. Quotation is approved
        $q->update(['quto_app' => 2, 'dis_app' => 2, 'freight_approval' => 0, 'charge_approval_status' => 'Approved']);
        $this->assertTrue(QuotationRevisionHelper::qualifiesForExternalRevision($q->fresh()));

        // When edit occurs on approved R0:
        $nextRev = QuotationRevisionHelper::getNextRevisionNumber($q->fresh());
        $this->assertEquals(1, $nextRev);
        $newRevQuotaNo = QuotationRevisionHelper::formatQuotaId($baseNo, $nextRev);
        $this->assertEquals('MSE/2026/8888(R1)', $newRevQuotaNo);

        // Create new revision R1:
        $qR1 = Quotation::create([
            'id_lead' => 999999,
            'quota_id' => $newRevQuotaNo,
            'revision_major' => $nextRev,
            'revision_minor' => 0,
            'parent_quotation_id' => $q->id,
            'quto_app' => 0,
            'sent_to_customer' => 0,
        ]);

        $this->assertEquals(2, Quotation::where('quota_id', 'like', 'MSE/2026/8888%')->count());

        // 3. Internal edit on unapproved R1:
        $this->assertFalse(QuotationRevisionHelper::qualifiesForExternalRevision($qR1));
        // It stays R1 and updates in place:
        $qR1->update(['subtotal' => 6000]);
        $this->assertEquals(2, Quotation::where('quota_id', 'like', 'MSE/2026/8888%')->count());
        $this->assertEquals('MSE/2026/8888(R1)', $qR1->fresh()->quota_id);

        // 4. Sent to customer / Approved R1:
        $qR1->update(['sent_to_customer' => 1]);
        $this->assertTrue(QuotationRevisionHelper::qualifiesForExternalRevision($qR1->fresh()));

        $nextRev2 = QuotationRevisionHelper::getNextRevisionNumber($qR1->fresh());
        $this->assertEquals(2, $nextRev2);
        $newRev2QuotaNo = QuotationRevisionHelper::formatQuotaId($baseNo, $nextRev2);
        $this->assertEquals('MSE/2026/8888(R2)', $newRev2QuotaNo);

        // Cleanup test data
        Quotation::where('quota_id', 'like', 'MSE/2026/8888%')->delete();
    }
}
