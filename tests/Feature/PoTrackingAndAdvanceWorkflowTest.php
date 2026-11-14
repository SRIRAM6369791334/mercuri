<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Orf;
use App\Models\Lead;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\ProductCategory;
use App\Models\Product;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoTrackingAndAdvanceWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'purchase-order-display', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'purchase-order-create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'purchase-order-edit', 'guard_name' => 'web']);
    }

    public function test_order_po_tracking_page_renders_with_metrics_and_filters()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('purchase-order-display');

        $response = $this->actingAs($user)->get(route('order-po-tracking'));
        $response->assertStatus(200);
        $response->assertSee('Order PO &amp; Supplier Tracking', false);
        $response->assertSee('Step 1: Sir Approval', false);
        $response->assertSee('Step 2: Finance UTR', false);
        $response->assertSee('Export to Excel', false);
    }

    public function test_create_po_view_renders_quotation_table_layout()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('purchase-order-create');

        $response = $this->actingAs($user)->get('/create-po');
        $response->assertStatus(200);
        $response->assertSee('Mercuriis Issuing Branch', false);
        $response->assertSee('Scope of Supply: Line Items (Quotation Format)', false);
        $response->assertSee('HSN Code', false);
        $response->assertSee('Part No', false);
        $response->assertSee('Advance Payment %', false);

        // Test create-po with orf_id=1 quotation styling & accessories
        $orf = Orf::find(1);
        if ($orf) {
            $orfResponse = $this->actingAs($user)->get('/create-po?orf_id=1');
            $orfResponse->assertStatus(200);
            $orfResponse->assertSee('accessories-wrapper', false);
            $orfResponse->assertSee('Select Accessories:', false);
        }
    }

    public function test_direct_purchase_orders_appear_in_all_and_released_tracking()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('purchase-order-display');
        $vendor = $this->makeVendor();
        $poNo = 'MEC/DIRECT/TEST/' . time();

        PurchaseOrder::create([
            'po_no' => $poNo,
            'vendor_id' => $vendor->id,
            'cat_id' => 1,
            'product_id' => 1,
            'product_qty' => 1,
            'unit_price' => 1000,
            'product_total_price' => 1000,
            'gst_amount' => 180,
            'sub_total' => 1000,
            'total_amount' => 1180,
            'advance_status' => 'not_required',
            'production_milestone' => 'po_issued',
            'lifecycle_status' => 'active',
            'mercuriis_branch' => 'Chennai',
        ]);

        $allResponse = $this->actingAs($user)->get(route('order-po-tracking', ['status' => 'all']));
        $allResponse->assertStatus(200);
        $allResponse->assertSee($poNo);
        $allResponse->assertSee('Direct Procurement');

        $releasedResponse = $this->actingAs($user)->get(route('order-po-tracking', ['status' => 'released']));
        $releasedResponse->assertStatus(200);
        $releasedResponse->assertSee($poNo);
    }

    public function test_advance_request_and_2_step_approval_workflow()
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'Super Admin'])->save();
        $user->givePermissionTo('purchase-order-display');
        $user->givePermissionTo('purchase-order-create');

        $vendor = $this->makeVendor();

        $poNo = 'MEC/TEST/PO/' . time();

        $po = PurchaseOrder::create([
            'po_no' => $poNo,
            'vendor_id' => $vendor->id,
            'cat_id' => 1,
            'product_id' => 1,
            'product_qty' => 2,
            'unit_price' => 50000,
            'product_total_price' => 100000,
            'gst_amount' => 18000,
            'sub_total' => 100000,
            'total_amount' => 118000,
            'advance_percentage' => 20.00,
            'advance_amount' => 23600.00,
            'advance_status' => 'pending_request',
            'mercuriis_branch' => 'Chennai'
        ]);

        // 1. Request Advance
        $reqResponse = $this->actingAs($user)->post(route('po.request-advance'), [
            'po_no' => $poNo
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $reqResponse->assertStatus(200);

        $this->assertEquals('pending_sir_approval', PurchaseOrder::where('po_no', $poNo)->first()->advance_status);

        // 2. Sir Approval
        $sirResponse = $this->actingAs($user)->post(route('po.sir-approval'), [
            'po_no' => $poNo,
            'action' => 'approve',
            'remark' => 'Approved for release by Sir'
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $sirResponse->assertStatus(200);

        $poAfterSir = PurchaseOrder::where('po_no', $poNo)->first();
        $this->assertEquals('approved_by_sir', $poAfterSir->advance_status);
        $this->assertEquals('Approved for release by Sir', $poAfterSir->sir_remark);

        // 3. Finance Release with UTR
        $finResponse = $this->actingAs($user)->post(route('po.finance-release'), [
            'po_no' => $poNo,
            'finance_utr_no' => 'HDFCUTR99882211',
            'payment_mode' => 'NEFT',
            'payment_date' => now()->format('Y-m-d'),
            'remark' => 'Processed via corporate net banking'
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $finResponse->assertStatus(200);

        $poAfterFin = PurchaseOrder::where('po_no', $poNo)->first();
        $this->assertEquals('advance_released', $poAfterFin->advance_status);
        $this->assertEquals('HDFCUTR99882211', $poAfterFin->finance_utr_no);
        $this->assertEquals('in_fabrication', $poAfterFin->production_milestone);

        $duplicateRelease = $this->actingAs($user)->post(route('po.finance-release'), [
            'po_no' => $poNo,
            'finance_utr_no' => 'HDFCUTR99882211',
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $duplicateRelease->assertStatus(409);
        $this->assertDatabaseHas('po_status_history', [
            'po_no' => $poNo,
            'action' => 'finance_released',
            'user_id' => $user->id,
        ]);

        // 4. Milestone & Dispatch Update
        $mileResponse = $this->actingAs($user)->post(route('po.update-milestone'), [
            'po_no' => $poNo,
            'supplier_work_order_no' => 'WO-TEST-5544',
            'supplier_dispatch_date' => now()->addDays(20)->format('Y-m-d'),
            'production_milestone' => 'ready_for_dispatch'
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $mileResponse->assertStatus(200);

        $poAfterMilestone = PurchaseOrder::where('po_no', $poNo)->first();
        $this->assertEquals('WO-TEST-5544', $poAfterMilestone->supplier_work_order_no);
        $this->assertEquals('ready_for_dispatch', $poAfterMilestone->production_milestone);
    }

    public function test_advance_rejection_and_re_request_workflow()
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'Super Admin'])->save();
        $user->givePermissionTo('purchase-order-display');
        $user->givePermissionTo('purchase-order-create');

        $vendor = $this->makeVendor();
        $poNo = 'MEC/REJ/TEST/' . time();

        $po = PurchaseOrder::create([
            'po_no' => $poNo,
            'vendor_id' => $vendor->id,
            'cat_id' => 1,
            'product_id' => 1,
            'product_qty' => 1,
            'unit_price' => 20000,
            'product_total_price' => 20000,
            'gst_amount' => 3600,
            'sub_total' => 20000,
            'total_amount' => 23600,
            'advance_percentage' => 30.00,
            'advance_amount' => 7080.00,
            'advance_status' => 'pending_request', // freshly saved PO with advance
            'mercuriis_branch' => 'Chennai'
        ]);

        // 1. Initial Advance Request from pending_request
        $req1 = $this->actingAs($user)->post(route('po.request-advance'), [
            'po_no' => $poNo
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $req1->assertStatus(200);
        $this->assertEquals('pending_sir_approval', PurchaseOrder::where('po_no', $poNo)->first()->advance_status);

        // 2. Sir Rejects with Reason
        $sirRej = $this->actingAs($user)->post(route('po.sir-approval'), [
            'po_no' => $poNo,
            'action' => 'reject',
            'remark' => 'Advance percentage too high for this vendor'
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $sirRej->assertStatus(200);

        $poRej = PurchaseOrder::where('po_no', $poNo)->first();
        $this->assertEquals('rejected_by_sir', $poRej->advance_status);
        $this->assertEquals('Advance percentage too high for this vendor', $poRej->sir_remark);

        // Verify rejected_sir filter in tracking page
        $filterResp = $this->actingAs($user)->get(route('order-po-tracking', ['status' => 'rejected_sir']));
        $filterResp->assertStatus(200);

        // 3. Srinath Re-requests Advance
        $req2 = $this->actingAs($user)->post(route('po.request-advance'), [
            'po_no' => $poNo
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $req2->assertStatus(200);

        $poReReq = PurchaseOrder::where('po_no', $poNo)->first();
        $this->assertEquals('pending_sir_approval', $poReReq->advance_status);
    }

    public function test_edit_po_view_and_update_lifecycle()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('purchase-order-edit');

        $vendor = $this->makeVendor();
        $poNo = 'MEC/EDIT/TEST/' . time();

        $po = PurchaseOrder::create([
            'po_no' => $poNo,
            'vendor_id' => $vendor->id,
            'cat_id' => 1,
            'product_id' => 1,
            'product_qty' => 1,
            'unit_price' => 30000,
            'product_total_price' => 30000,
            'gst_amount' => 5400,
            'sub_total' => 30000,
            'total_amount' => 35400,
            'advance_percentage' => 30.00,
            'advance_amount' => 10620.00,
            'mercuriis_branch' => 'Chennai',
            'lead_time' => '4 Weeks'
        ]);

        // Render Edit Page
        $editViewResponse = $this->actingAs($user)->get('/edit-po-data/' . urlencode($poNo));
        $editViewResponse->assertStatus(200);
        $editViewResponse->assertSee('Edit Purchase Order: ' . $poNo, false);
        $editViewResponse->assertSee('Scope of Supply: Line Items (Quotation Format)', false);
        $editViewResponse->assertSee('Mercuriis Issuing Branch', false);
        $editViewResponse->assertSee('Advance Payment %', false);

        // Submit PO Edit Form
        $postResponse = $this->actingAs($user)->post('/edit-po', [
            'po_number' => [$poNo],
            'po_id' => [$po->id],
            'catId' => [1],
            'proId' => [1],
            'qty' => [2],
            'unit_price' => [45000],
            'discount' => [10],
            'part_no' => ['PART-XYZ-99'],
            'hsn' => ['84289090'],
            'primary_vendor_id' => $vendor->id,
            'vendorId' => [$vendor->id],
            'shipping_type' => 'warehouse',
            'shipping_address' => 'Mercuriis Coimbatore Branch Delivery',
            'shipping_gst' => '33AATCM0824C1ZQ',
            'supplier_billing_address' => 'Supplier Factory Unit II',
            'supplier_gst' => '33AABCT1234F1Z1',
            'mercuriis_branch' => 'Coimbatore',
            'payment_terms' => '50% Advance & 50% Against Proforma',
            'advance_percentage' => 50.00,
            'lead_time' => '3-4 Weeks',
            'warranty_terms' => '24 Months Comprehensive',
            'installation_scope' => 'In Supplier Scope (Included in Unit Rate)',
            'freight_charges' => 2500,
            'unloading_charges' => 1000,
        ]);

        $postResponse->assertRedirect(route('purchase-order-details-page'));

        // Verify database updates
        $this->assertDatabaseHas('purchase_order_tb', [
            'id' => $po->id,
            'po_no' => $poNo,
            'mercuriis_branch' => 'Coimbatore',
            'product_qty' => 2,
            'unit_price' => 45000,
            'part_no' => 'PART-XYZ-99',
            'advance_percentage' => 50.00,
            'freight_charges' => 2500,
            'unloading_charges' => 1000
        ]);
    }

    public function test_non_management_user_cannot_request_an_advance()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('purchase-order-display');
        $vendor = $this->makeVendor();
        $poNo = 'MEC/ROLE/TEST/' . time();
        PurchaseOrder::create([
            'po_no' => $poNo,
            'vendor_id' => $vendor->id,
            'cat_id' => 1,
            'product_id' => 1,
            'product_qty' => 1,
            'unit_price' => 1000,
            'product_total_price' => 1000,
            'gst_amount' => 180,
            'sub_total' => 1000,
            'total_amount' => 1180,
            'advance_percentage' => 20,
            'advance_amount' => 236,
            'advance_status' => 'pending_request',
            'production_milestone' => 'awaiting_advance',
            'mercuriis_branch' => 'Chennai',
        ]);

        $response = $this->actingAs($user)->post(route('po.request-advance'), [
            'po_no' => $poNo,
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(403);
        $this->assertSame('pending_request', PurchaseOrder::where('po_no', $poNo)->value('advance_status'));
    }

    public function test_direct_purchase_orders_appear_in_tracking()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('purchase-order-display');
        $vendor = $this->makeVendor();
        $poNo = 'MEC/DIRECT/TEST/' . time();
        PurchaseOrder::create([
            'po_no' => $poNo,
            'vendor_id' => $vendor->id,
            'cat_id' => 1,
            'product_id' => 1,
            'product_qty' => 1,
            'unit_price' => 1000,
            'product_total_price' => 1000,
            'gst_amount' => 180,
            'sub_total' => 1000,
            'total_amount' => 1180,
            'advance_status' => 'not_required',
            'production_milestone' => 'po_issued',
            'lifecycle_status' => 'active',
            'mercuriis_branch' => 'Chennai',
        ]);

        $response = $this->actingAs($user)->get(route('order-po-tracking'));

        $response->assertOk()->assertSee($poNo)->assertSee('Direct PO');
    }

    private function makeVendor(): Vendor
    {
        return Vendor::create([
            'company_name' => 'Test Fabricator ' . uniqid(),
            'vendor_id' => 'MIS-TEST-' . uniqid(),
            'alternate_name' => 'Test Fabricator',
            'point_of_contact' => 'Test Contact',
            'contact_number' => '9876543210',
            'email' => 'vendor@example.test',
            'gst_number' => '33AABCT1234F1Z1',
            'address' => 'Test supplier address, Tamil Nadu',
            'country_id' => 76,
            'state_id' => 31,
            'city_id' => 692,
            'vendor_status' => 'active',
            'product_cat_id' => json_encode([1]),
        ]);
    }
}
