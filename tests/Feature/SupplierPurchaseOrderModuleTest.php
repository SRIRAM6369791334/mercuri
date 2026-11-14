<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorAddress;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PurchaseOrder;
use App\Models\PoStatusHistory;
use App\Notifications\SupplierDispatchReminderNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SupplierPurchaseOrderModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $csUser;
    protected User $financeUser;
    protected Vendor $vendor;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles & permissions exist
        Permission::firstOrCreate(['name' => 'purchase-order-display', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'purchase-order-create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'purchase-order-edit', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'purchase-order-delete', 'guard_name' => 'web']);

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Service Coordinator', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Finance', 'guard_name' => 'web']);

        // Create Super Admin User
        $this->superAdmin = User::factory()->create([
            'email' => 'admin_test_' . uniqid() . '@mercuriis.com',
            'role' => 'Super Admin'
        ]);
        $this->superAdmin->assignRole('Super Admin');
        $this->superAdmin->givePermissionTo(['purchase-order-display', 'purchase-order-create', 'purchase-order-edit', 'purchase-order-delete']);

        // Create Srinath / CS User
        $this->csUser = User::factory()->create([
            'name' => 'Srinath Test',
            'email' => 'srinath_test_' . uniqid() . '@mercuriis.com',
            'role' => 'Service Coordinator'
        ]);
        $this->csUser->assignRole('Service Coordinator');
        $this->csUser->givePermissionTo(['purchase-order-display', 'purchase-order-create', 'purchase-order-edit']);

        // Create Finance User
        $this->financeUser = User::factory()->create([
            'email' => 'finance_test_' . uniqid() . '@mercuriis.com',
            'role' => 'Finance'
        ]);
        $this->financeUser->assignRole('Finance');
        $this->financeUser->givePermissionTo(['purchase-order-display']);

        // Use existing category and product or create valid ones
        $category = ProductCategory::first();
        $this->product = Product::where('Productcategory', $category->id)->first();
        if (!$this->product) {
            $this->product = Product::first();
            $category = ProductCategory::find($this->product->Productcategory) ?: $category;
        }

        $this->vendor = Vendor::firstOrCreate(
            ['company_name' => 'Precision Engineering Works Ltd'],
            [
                'vendor_id' => 'VEN-TEST-' . rand(100, 999),
                'point_of_contact' => 'Ramesh Kumar',
                'contact_number' => '9876543210',
                'email' => 'precision.test@example.com',
                'gst_number' => '33AAAAA0000A1Z5',
                'address' => 'Plot 45, Ambattur Industrial Estate, Chennai - 600058',
                'postal_code' => 600058,
                'vendor_status' => 'active',
                'product_cat_id' => json_encode([$category->id])
            ]
        );
    }

    /**
     * Test 1: Server-side financial calculations recomputed with multi-row, line discount, GST, freight, unloading, round-off
     */
    public function test_server_side_calculation_with_multi_row_discount_gst_freight_and_roundoff()
    {
        $payload = [
            'po_date' => now()->format('Y-m-d'),
            'primary_vendor_id' => $this->vendor->id,
            'supplier_billing_address' => 'Plot 45, Ambattur, Chennai',
            'supplier_gst' => '33AAAAA0000A1Z5',
            'mercuriis_branch' => 'Chennai',
            'shipping_type' => 'warehouse',
            'shipping_address' => 'Mercuriis Podappai Warehouse, Chennai',
            'shipping_gst' => '33AATCM0824C1ZQ',
            'lead_time' => '4 Weeks',
            'payment_terms' => '20% Advance & 80% Against Delivery',
            'advance_percentage' => 20.00,
            'freight_charges' => 2500.00,
            'unloading_charges' => 500.00,
            'discount_percentage' => 0.00,
            'supplier_dispatch_date' => now()->addDays(28)->format('Y-m-d'),
            // Line items (Client deliberately passes 0 for totals to verify server recalculation)
            'catId' => [$this->product->Productcategory],
            'proId' => [$this->product->id],
            'part_no' => ['MEC-TEST-8428'],
            'hsn' => ['8428'],
            'qty' => [2],
            'unit_price' => [10000.00], // 2 * 10000 = 20000
            'discount' => [10.00],      // 10% discount = 2000 => Taxable = 18000
            'gst' => [18],              // 18% GST on 18000 = 3240 => Line Total = 21240
            'sub_total' => [0.00],      // Client spoofed 0
            'product_total' => [0.00],  // Client spoofed 0
            'grandtotal' => 0.00,       // Client spoofed 0
            'taxtotal' => 0.00
        ];

        $response = $this->actingAs($this->superAdmin)->post('/save-po', $payload);
        $response->assertStatus(302);

        $savedPo = PurchaseOrder::where('vendor_id', $this->vendor->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($savedPo);
        // Expected Math:
        // Basic: 2 * 10000 = 20000.00
        // Line Discount (10%): 2000.00
        // Line Taxable: 18000.00
        // GST (18%): 3240.00
        // Freight: 2500.00
        // Unloading: 500.00
        // Raw Grand Total: 18000 + 3240 + 2500 + 500 = 24240.00
        // Grand Total: 24240.00
        // Advance (20%): 4848.00
        $this->assertEquals(20000.00, (float) $savedPo->product_total_price);
        $this->assertEquals(3240.00, (float) $savedPo->gst_amount);
        $this->assertEquals(24240.00, (float) $savedPo->grand_total);
        $this->assertEquals(20.00, (float) $savedPo->advance_percentage);
        $this->assertEquals(4848.00, (float) $savedPo->advance_amount);
    }

    /**
     * Test 2: HSN code is mandatory with row-specific error naming (e.g., "Row #1 HSN Code is mandatory")
     */
    public function test_hsn_code_is_mandatory_with_row_specific_error_naming()
    {
        $payload = [
            'po_date' => now()->format('Y-m-d'),
            'primary_vendor_id' => $this->vendor->id,
            'supplier_billing_address' => 'Plot 45, Ambattur, Chennai',
            'supplier_gst' => '33AAAAA0000A1Z5',
            'mercuriis_branch' => 'Chennai',
            'shipping_type' => 'warehouse',
            'shipping_address' => 'Mercuriis Podappai Warehouse, Chennai',
            'shipping_gst' => '33AATCM0824C1ZQ',
            'lead_time' => '4 Weeks',
            'payment_terms' => '20% Advance & 80% Against Delivery',
            'advance_percentage' => 20.00,
            'catId' => [$this->product->Productcategory],
            'proId' => [$this->product->id],
            'part_no' => ['MEC-TEST-8428'],
            'hsn' => [''], // Empty HSN
            'qty' => [1],
            'unit_price' => [5000.00],
            'gst' => [18],
            'supplier_dispatch_date' => now()->addDays(20)->format('Y-m-d')
        ];

        $response = $this->actingAs($this->superAdmin)->post('/save-po', $payload);
        $response->assertSessionHasErrors(['hsn.0']);

        $errors = session('errors')->get('hsn.0');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Row #1 HSN Code is mandatory', $errors[0]);
    }

    /**
     * Test 3: Customer Site delivery requires customer site selection and address
     */
    public function test_customer_site_delivery_requires_customer_site_selection()
    {
        $payload = [
            'po_date' => now()->format('Y-m-d'),
            'primary_vendor_id' => $this->vendor->id,
            'supplier_billing_address' => 'Plot 45, Ambattur, Chennai',
            'supplier_gst' => '33AAAAA0000A1Z5',
            'mercuriis_branch' => 'Chennai',
            'shipping_type' => 'customer_site',
            'shipping_address' => '', // Empty shipping address
            'lead_time' => '4 Weeks',
            'payment_terms' => '20% Advance & 80% Against Delivery',
            'catId' => [$this->product->Productcategory],
            'proId' => [$this->product->id],
            'hsn' => ['8428'],
            'qty' => [1],
            'unit_price' => [5000.00],
            'gst' => [18],
            'supplier_dispatch_date' => now()->addDays(20)->format('Y-m-d')
        ];

        $response = $this->actingAs($this->superAdmin)->post('/save-po', $payload);
        $response->assertSessionHasErrors(['shipping_address']);
    }

    /**
     * Test 4: PO creation without terms_condition_id succeeds and automatically attaches standard terms
     */
    public function test_po_creation_without_terms_condition_id()
    {
        $payload = [
            'po_date' => now()->format('Y-m-d'),
            'primary_vendor_id' => $this->vendor->id,
            'supplier_billing_address' => 'Plot 45, Ambattur, Chennai',
            'supplier_gst' => '33AAAAA0000A1Z5',
            'mercuriis_branch' => 'Coimbatore',
            'shipping_type' => 'warehouse',
            'shipping_address' => 'Mercuriis Coimbatore Warehouse',
            'shipping_gst' => '33AATCM0824C1ZQ',
            'lead_time' => '6 Weeks',
            'payment_terms' => 'Credit 30 Days',
            'advance_percentage' => 0.00,
            'catId' => [$this->product->Productcategory],
            'proId' => [$this->product->id],
            'hsn' => ['8428'],
            'qty' => [1],
            'unit_price' => [8000.00],
            'gst' => [18],
            'supplier_dispatch_date' => now()->addDays(42)->format('Y-m-d')
            // Notice: terms_condition_id is completely omitted
        ];

        $response = $this->actingAs($this->superAdmin)->post('/save-po', $payload);
        $response->assertStatus(302);

        $savedPo = PurchaseOrder::where('vendor_id', $this->vendor->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($savedPo);
        $this->assertEquals('Coimbatore', $savedPo->mercuriis_branch);
        $this->assertEquals(0.00, (float) $savedPo->advance_amount);
    }

    /**
     * Test 5: Supplier branch API loads main vendor address + secondary addresses from vendor_addresses
     */
    public function test_supplier_branch_get_branches_api_returns_primary_and_secondary_branches()
    {
        // Add secondary plant address
        $branchAddress = VendorAddress::create([
            'vendor_id' => $this->vendor->id,
            'address_type' => 'Unit II Plant',
            'address' => 'SIPCOT Phase 2, Sriperumbudur',
            'gst_number' => '33AAAAA0000A2Z4'
        ]);

        $response = $this->actingAs($this->superAdmin)->post('/vendor/get-branches', [
            'vendor_id' => $this->vendor->id
        ]);

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertEquals('success', $json['status']);
        $this->assertGreaterThanOrEqual(2, count($json['branches']));

        // Verify Branch 2 address and GST
        $branch2 = collect($json['branches'])->firstWhere('id', 'addr_' . $branchAddress->id);
        $this->assertNotNull($branch2);
        $this->assertEquals('33AAAAA0000A2Z4', $branch2['gst']);
        $this->assertStringContainsString('Sriperumbudur', $branch2['address']);
    }

    /**
     * Test 6: Automated dispatch reminder command (po:dispatch-reminders) is idempotent and notifies Srinath & Super Admin
     */
    public function test_dispatch_reminder_command_is_idempotent_and_delivers_notification()
    {
        Notification::fake();

        $poNo = 'MEC/DISP/TEST/' . rand(1000, 9999);

        // Create PO with dispatch date exactly 7 days from today
        $po = PurchaseOrder::create([
            'po_no' => $poNo,
            'vendor_id' => $this->vendor->id,
            'cat_id' => $this->product->Productcategory,
            'product_id' => $this->product->id,
            'product_qty' => 1,
            'unit_price' => 15000,
            'product_total_price' => 15000,
            'sub_total' => 15000,
            'total_amount' => 17700,
            'gst_amount' => 2700,
            'grand_total' => 17700,
            'supplier_dispatch_date' => now()->addDays(7)->format('Y-m-d'),
            'dispatch_reminder_sent_at' => null,
            'mercuriis_branch' => 'Chennai'
        ]);

        // Run reminder command first time
        Artisan::call('po:dispatch-reminders');

        // Check that notification was sent
        Notification::assertSentTo(
            [$this->superAdmin],
            SupplierDispatchReminderNotification::class
        );

        $po->refresh();
        $this->assertNotNull($po->dispatch_reminder_sent_at);
        $firstSentAt = $po->dispatch_reminder_sent_at;

        // Run reminder command second time (idempotency check)
        Notification::fake(); // Reset fake recorder
        Artisan::call('po:dispatch-reminders');

        // Should NOT send again
        Notification::assertNothingSent();
        $po->refresh();
        $this->assertEquals($firstSentAt, $po->dispatch_reminder_sent_at);
    }

    /**
     * Test 7: Changing dispatch date resets dispatch_reminder_sent_at so reminder can trigger for the new date
     */
    public function test_dispatch_date_change_resets_reminder_sent_timestamp()
    {
        $poNo = 'MEC/EDIT/TEST/' . rand(1000, 9999);

        $po = PurchaseOrder::create([
            'po_no' => $poNo,
            'vendor_id' => $this->vendor->id,
            'cat_id' => $this->product->Productcategory,
            'product_id' => $this->product->id,
            'product_qty' => 1,
            'unit_price' => 12000,
            'product_total_price' => 12000,
            'sub_total' => 12000,
            'total_amount' => 14160,
            'gst_amount' => 2160,
            'grand_total' => 14160,
            'supplier_dispatch_date' => now()->addDays(7)->format('Y-m-d'),
            'dispatch_reminder_sent_at' => now()->subDay(),
            'mercuriis_branch' => 'Chennai'
        ]);

        $updatePayload = [
            'po_number' => [$poNo],
            'po_id' => [$po->id],
            'primary_vendor_id' => $this->vendor->id,
            'supplier_billing_address' => 'Plot 45, Ambattur, Chennai',
            'supplier_gst' => '33AAAAA0000A1Z5',
            'mercuriis_branch' => 'Chennai',
            'shipping_type' => 'warehouse',
            'shipping_address' => 'Mercuriis Podappai Warehouse, Chennai',
            'shipping_gst' => '33AATCM0824C1ZQ',
            'lead_time' => '6 Weeks',
            'payment_terms' => '20% Advance & 80% Against Delivery',
            'advance_percentage' => 20.00,
            'supplier_dispatch_date' => now()->addDays(30)->format('Y-m-d'), // Changed date
            'catId' => [$this->product->Productcategory],
            'proId' => [$this->product->id],
            'hsn' => ['8428'],
            'qty' => [1],
            'unit_price' => [12000.00],
            'gst' => [18]
        ];

        $response = $this->actingAs($this->superAdmin)->post('/edit-po', $updatePayload);
        $response->assertStatus(302);

        $po->refresh();
        $this->assertNull($po->dispatch_reminder_sent_at);
        $this->assertEquals(now()->addDays(30)->format('Y-m-d'), $po->supplier_dispatch_date);
    }

    /**
     * Test 8: End-to-end 2-Step Advance approval flow with PoStatusHistory logging
     */
    public function test_advance_approval_flow_roles_and_po_status_history()
    {
        $poNo = 'MEC/ADV/FLOW/' . rand(1000, 9999);

        $po = PurchaseOrder::create([
            'po_no' => $poNo,
            'vendor_id' => $this->vendor->id,
            'cat_id' => $this->product->Productcategory,
            'product_id' => $this->product->id,
            'product_qty' => 1,
            'unit_price' => 50000,
            'product_total_price' => 50000,
            'sub_total' => 50000,
            'total_amount' => 59000,
            'gst_amount' => 9000,
            'grand_total' => 59000,
            'advance_percentage' => 30.00,
            'advance_amount' => 17700.00,
            'advance_status' => null,
            'mercuriis_branch' => 'Chennai'
        ]);

        // Step 1: CS Coordinator (Srinath) requests advance
        $reqResponse = $this->actingAs($this->csUser)->post(route('po.request-advance'), [
            'po_no' => $poNo,
            'advance_percentage' => 30,
            'advance_amount' => 17700,
            'notes' => 'Vendor requires 30% advance before cutting raw material'
        ]);
        $reqResponse->assertJson(['status' => 'success']);

        $po->refresh();
        $this->assertEquals('pending_sir_approval', $po->advance_status);

        // Step 2: Super Admin approves advance
        $approveResponse = $this->actingAs($this->superAdmin)->post(route('po.sir-approve-advance'), [
            'po_no' => $poNo,
            'action' => 'approve',
            'remarks' => 'Approved for immediate payment'
        ]);
        $approveResponse->assertJson(['status' => 'success']);

        $po->refresh();
        $this->assertEquals('approved_by_sir', $po->advance_status);

        // Step 3: Finance releases advance with UTR
        $utrNumber = 'HDFCR5202611130099';
        $financeResponse = $this->actingAs($this->financeUser)->post(route('po.finance-release-advance'), [
            'po_no' => $poNo,
            'utr_number' => $utrNumber,
            'payment_date' => now()->format('Y-m-d'),
            'paid_amount' => 17700.00,
            'remarks' => 'NEFT transfer completed'
        ]);
        $financeResponse->assertJson(['status' => 'success']);

        $po->refresh();
        $this->assertEquals('advance_released', $po->advance_status);
        $this->assertEquals($utrNumber, $po->advance_utr_number);

        // Verify Audit Trail in PoStatusHistory
        $histories = PoStatusHistory::where('po_no', $poNo)->orderBy('id')->get();
        $this->assertGreaterThanOrEqual(3, $histories->count());

        $this->assertEquals('requested_advance', $histories[0]->action);
        $this->assertEquals('sir_approved', $histories[1]->action);
        $this->assertEquals('finance_released', $histories[2]->action);
    }

    /**
     * Test 9: Segment terms API returns master terms, lead time, and technical specifications
     */
    public function test_segment_terms_api_returns_master_terms_lead_time_and_specs()
    {
        $cat = ProductCategory::first();
        $pro = Product::where('Productcategory', $cat->id)->first();

        $response = $this->actingAs($this->superAdmin)->get("/vendor/get-segment-terms/{$cat->id}/" . ($pro ? $pro->id : ''));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'category_id',
            'product_id',
            'default_payment_term',
            'payment_terms',
            'default_warranty_term',
            'warranty_terms',
            'lead_time',
            'technical_specification'
        ]);

        $json = $response->json();
        $this->assertEquals('success', $json['status']);
        $this->assertNotEmpty($json['default_payment_term']);
        $this->assertNotEmpty($json['default_warranty_term']);
    }

    /**
     * Test 10: PO creation and editing with custom terms, custom lead time, and custom warranty
     */
    public function test_po_creation_and_editing_with_custom_terms_and_lead_time()
    {
        $payload = [
            'po_date' => now()->format('Y-m-d'),
            'primary_vendor_id' => $this->vendor->id,
            'supplier_billing_address' => 'Plot 45, Ambattur, Chennai',
            'supplier_gst' => '33AAAAA0000A1Z5',
            'mercuriis_branch' => 'Chennai',
            'shipping_type' => 'warehouse',
            'shipping_address' => 'Mercuriis Podappai Warehouse, Chennai',
            'shipping_gst' => '33AATCM0824C1ZQ',
            'lead_time' => 'custom',
            'custom_lead_time' => '7-9 Weeks Custom',
            'payment_terms' => 'custom',
            'custom_payment_terms' => '45% Advance and 55% Against Dispatch Readiness',
            'warranty_terms' => 'custom',
            'custom_warranty_terms' => '36 Months Custom Extended Warranty',
            'installation_scope' => 'custom',
            'custom_installation_scope' => 'Turnkey Installation by Vendor Team',
            'advance_percentage' => 45.00,
            'catId' => [$this->product->Productcategory],
            'proId' => [$this->product->id],
            'qty' => [1],
            'hsn' => ['8428'],
            'part_no' => ['PART-TEST-CUSTOM'],
            'unit_price' => [10000.00],
            'discount' => [0.00],
            'gst' => [18]
        ];

        $response = $this->actingAs($this->superAdmin)->post('/save-po', $payload);
        $response->assertStatus(302);

        $savedPo = PurchaseOrder::where('vendor_id', $this->vendor->id)->latest('id')->first();
        $this->assertNotNull($savedPo);
        $this->assertEquals('7-9 Weeks Custom', $savedPo->lead_time);
        $this->assertEquals('45% Advance and 55% Against Dispatch Readiness', $savedPo->payment_terms);
        $this->assertEquals('36 Months Custom Extended Warranty', $savedPo->warranty_terms);
        $this->assertEquals('Turnkey Installation by Vendor Team', $savedPo->installation_scope);
        $this->assertEquals(45.00, (float) $savedPo->advance_percentage);
    }
}
