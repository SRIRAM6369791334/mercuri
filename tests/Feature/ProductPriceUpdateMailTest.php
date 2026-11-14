<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductStock;
use App\Models\Lead;
use App\Models\Design;
use App\Models\User;
use App\Mail\ProductPriceUpdatedMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;

class ProductPriceUpdateMailTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function updating_mhe_special_product_triggers_sales_email_notification()
    {
        // 1. Arrange Users
        $salesUser = User::create([
            'name' => 'Kavitha Sales',
            'email' => 'kavitha_sales@example.com',
            'role' => 'Sales Executive',
            'password' => bcrypt('password'),
        ]);

        $salesManager = User::create([
            'name' => 'Senthil Manager',
            'email' => 'senthil_mgr@example.com',
            'role' => 'Sales Manager',
            'password' => bcrypt('password'),
        ]);

        $designer = User::create([
            'name' => 'Praveen Designer',
            'email' => 'praveen_design@example.com',
            'role' => 'Design Engineer',
            'password' => bcrypt('password'),
        ]);

        try {
            $designer->givePermissionTo('products-edit');
        } catch (\Throwable $e) {}

        // 2. Create MHE Special Category & Product
        $cat = ProductCategory::find(2) ?? ProductCategory::create([
            'category_name' => 'MHE Special',
            'sub_category' => 'special',
        ]);

        $subCat = ProductSubCategory::firstOrCreate([
            'subcategory_name' => 'Hydraulic Goods Lift',
            'cat_id' => $cat->id,
        ]);

        $product = Product::create([
            'productname' => 'Special Hydraulic Goods Lift 3T',
            'productmodel' => 'HGL-3000',
            'Productcategory' => $cat->id,
            'sub_cat_id' => $subCat->id,
            'rate' => 300000.00,
            'hsn' => '8428',
            'gst' => 18,
            'quantity' => 1,
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'total_stock' => 1,
            'available_stock' => 1,
        ]);

        // 3. Create Lead and Design linked to this product
        $lead = Lead::create([
            'LeadName' => 'Ashok Leyland Ltd',
            'lead_id' => 'LEAD8899',
            'category' => $cat->id,
            'assigned_to' => $salesUser->id,
            'Leadstatus' => '343',
        ]);

        $design = Design::create([
            'idlead' => $lead->id,
            'product' => (string) $product->id,
            'category' => $cat->id,
            'quantity' => 1,
            'assigndesign' => $designer->id,
            'design_user' => $designer->id,
            'drawing_approved' => 'pending',
        ]);

        // 4. Act: Design Team updates Product Price in Product Master
        $response = $this->actingAs($designer)->post(route('products.update'), [
            'productId' => $product->id,
            'productname' => 'Special Hydraulic Goods Lift 3T',
            'productmodel' => 'HGL-3000',
            'Productcategory' => $cat->id,
            'product_sub_category_id' => $subCat->id,
            'rate' => '425000.00', // Updated Price!
            'hsn' => '8428',
            'gst' => 18,
            'partno' => 'HGL-3T-001',
            'dispatch_time' => '4-6 Weeks',
            'all_dis' => 5,
            'productdes' => 'Custom 3T goods lift for heavy industrial pallets',
            'quantity' => 1,
        ]);

        $response->assertRedirect(route('products'));
        $this->assertEquals(425000.00, (float) $product->fresh()->rate);

        // 5. Assert: Mail was triggered to Sales Rep and Sales Manager
        Mail::assertSent(ProductPriceUpdatedMail::class, function ($mail) use ($salesUser, $salesManager) {
            return $mail->hasTo('kavitha_sales@example.com') &&
                   $mail->hasTo('senthil_mgr@example.com');
        });
    }

    /** @test */
    public function non_special_product_update_without_price_change_does_not_send_mail()
    {
        $designer = User::create([
            'name' => 'Regular User',
            'email' => 'regular_user@example.com',
            'role' => 'Design Engineer',
            'password' => bcrypt('password'),
        ]);

        try {
            $designer->givePermissionTo('products-edit');
        } catch (\Throwable $e) {}

        $catStandard = ProductCategory::find(1) ?? ProductCategory::create([
            'category_name' => 'Standard Pallet Truck',
            'sub_category' => 'standard',
        ]);

        $product = Product::create([
            'productname' => 'Hand Pallet Truck 2.5T',
            'productmodel' => 'HPT-2500',
            'Productcategory' => $catStandard->id,
            'rate' => 25000.00,
            'hsn' => '8428',
            'gst' => 18,
            'quantity' => 10,
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'total_stock' => 10,
            'available_stock' => 10,
        ]);

        // Updating other fields like description/model without changing price
        $response = $this->actingAs($designer)->post(route('products.update'), [
            'productId' => $product->id,
            'productname' => 'Hand Pallet Truck 2.5T Updated Name',
            'productmodel' => 'HPT-2500-REV',
            'Productcategory' => $catStandard->id,
            'product_sub_category_id' => null,
            'rate' => '25000.00', // Unchanged price
            'hsn' => '8428',
            'gst' => 18,
            'partno' => 'HPT-2500',
            'dispatch_time' => '1 Week',
            'all_dis' => 0,
            'productdes' => 'Minor description update',
            'quantity' => 10,
        ]);

        $response->assertRedirect(route('products'));
        Mail::assertNothingSent();
    }

    /** @test */
    public function email_view_renders_properly_with_data()
    {
        $rendered = view('emails.product_price_updated', [
            'product_name' => 'Scissor Lift 1000kg',
            'product_model' => 'SL-1000',
            'part_no' => 'SL-1000-PART',
            'category_name' => 'MHE Special',
            'sub_category_name' => 'Scissor Lift',
            'old_rate' => 200000.00,
            'new_rate' => 250000.00,
            'gst_percent' => 18,
            'updated_by' => 'Praveen Designer',
            'updated_at' => '30 Oct 2026, 07:15 AM',
            'related_leads' => [
                [
                    'lead_id' => 'LEAD0007',
                    'customer_name' => 'Deva',
                    'sales_rep' => 'Basith',
                    'quota_url' => url('/createquota/7'),
                ]
            ],
        ])->render();

        $this->assertStringContainsString('Scissor Lift 1000kg', $rendered);
        $this->assertStringContainsString('250,000.00', $rendered);
        $this->assertStringContainsString('200,000.00', $rendered);
        $this->assertStringContainsString('LEAD0007', $rendered);
        $this->assertStringContainsString('Deva', $rendered);
        $this->assertStringContainsString('Create Quotation', $rendered);
    }
}
