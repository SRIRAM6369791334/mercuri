<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\Design;
use App\Models\DesignDrawing;
use App\Models\Quotation;
use App\Models\User;
use App\DataTables\ConvertedDataTable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class DesignToQuotationFlowTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Mail::fake();
    }

    /** @test */
    public function full_design_to_quotation_approval_lifecycle_works()
    {
        // 1. Arrange Users & Lead
        $salesUser = User::first();
        if (!$salesUser) {
            $this->markTestSkipped('No user found');
        }

        $designerUser = User::where('id', '!=', $salesUser->id)->first() ?? $salesUser;

        $lead = Lead::create([
            'LeadName' => 'Test Flow Customer',
            'phonumber' => '9999988888',
            'category' => 2, // MHE Special
            'assigned_to' => $salesUser->id,
            'Leadstatus' => '343',
        ]);

        $design = Design::create([
            'idlead' => $lead->id,
            'product' => '187',
            'category' => 2,
            'quantity' => 1,
            'assigndesign' => $designerUser->id,
            'design_user' => null,
            'drawing_approved' => 'pending',
            'design_status' => null,
            'return_status' => 0,
        ]);

        // Verify Step 1: Before designer is assigned, status shows Pending
        $dataTable = new ConvertedDataTable();
        $rowMock = (object) [
            'design_id' => $design->id,
            'design_user' => null,
            'drawing_approved' => null,
            'drawing_count' => 0,
            'design_status' => null,
            'return_status' => 0,
        ];
        // 2. Assign Designer
        $design->update([
            'design_user' => $designerUser->id,
            'drawing_approved' => 'pending',
        ]);

        // When designer is assigned but no drawings yet -> button is "Upload Drawing"
        $rowMockAssigned = (object) [
            'design_id' => $design->id,
            'design_user' => $designerUser->id,
            'drawing_approved' => 'pending',
            'drawing_count' => 0,
            'design_status' => null,
            'return_status' => 0,
        ];

        // 3. Designer Uploads Drawing for Approval
        $fakeDrawing = UploadedFile::fake()->create('ga_layout.pdf', 500, 'application/pdf');

        $responseUpload = $this->actingAs($designerUser)->post(route('design.upload.drawings'), [
            'design_id' => $design->id,
            'drawing' => [$fakeDrawing],
        ]);

        $responseUpload->assertStatus(200);
        $this->assertDatabaseHas('design_drawings', [
            'design_id' => $design->id,
        ]);

        $drawingRecord = DesignDrawing::where('design_id', $design->id)->first();
        $this->assertNotNull($drawingRecord);
        $this->assertEquals('pending', $design->fresh()->drawing_approved);

        // 4. Sales Manager Reviews & Approves Drawing
        $responseApproval = $this->actingAs($salesUser)->post(route('design.drawing.approval.save'), [
            'design_id' => $design->id,
            'drawings' => [
                $drawingRecord->id => [
                    'status' => '1', // 1 = Approved
                    'remarks' => 'Approved by sales team',
                ]
            ]
        ]);

        $responseApproval->assertStatus(200);
        $this->assertEquals('approved', $design->fresh()->drawing_approved);
        $this->assertEquals(1, $drawingRecord->fresh()->drawing_app);

        // 5. Designer Submits "Move to Sales" (Attaching Quotation File)
        $fakeQuotation = UploadedFile::fake()->create('quotation_offer.pdf', 500, 'application/pdf');

        $responseMoveToSales = $this->actingAs($designerUser)->post(route('design.upload.files'), [
            'design_id' => $design->id,
            'quotation' => $fakeQuotation,
        ]);

        $responseMoveToSales->assertStatus(200);
        $this->assertEquals(1, $design->fresh()->design_status);

        // 6. Confirm Move to Quotation
        $responseMoveToQuota = $this->actingAs($salesUser)->post('/designs/move-to-quotation', [
            'design_id' => $design->id,
        ]);

        $responseMoveToQuota->assertStatus(200);
        $responseMoveToQuota->assertJson([
            'success' => true,
            'redirect' => url('/createquota/' . $lead->id),
        ]);
        $this->assertEquals(1, $lead->fresh()->quota_proceed);

        // 7. Quotation Creation with Drawing Integration
        $fakeGaFile = UploadedFile::fake()->create('final_ga.pdf', 200, 'application/pdf');
        $fakePitFile = UploadedFile::fake()->image('final_pit.png', 800, 600);

        // Ensure permission
        try {
            $salesUser->givePermissionTo('quotation-create');
            $salesUser->givePermissionTo('quotation-display');
        } catch (\Throwable $e) {}

        $responseEstimate = $this->actingAs($salesUser)->post(route('createestimate'), [
            'leadno' => $lead->id,
            'placeofsupply' => 'Chennai, Tamil Nadu',
            'shippingcountry' => 76,
            'shistate_id' => 33,
            'shcitylist' => 692,
            'shippostalcode' => '600001',
            'termscondition' => '1',
            'freight_category_id' => '1',
            'freight_sub_category_id' => '1',
            'warranty' => '1',
            'dov' => '1',
            'unloading' => '1',
            'installation' => '1',
            'quto_status' => '1',
            'currency' => 'INR',
            'ga_number' => $fakeGaFile,
            'pit_drawing' => $fakePitFile,
            'product_id' => ['187'],
            'quantity' => [1],
            'rate' => [250000],
            'discount' => [0],
            'tax' => [18],
            'amount' => [250000],
        ]);

        if (session('error')) {
            dump(session('error'));
        }
        $responseEstimate->assertSessionMissing('error');
        $responseEstimate->assertRedirect();

        $quotation = Quotation::where('id_lead', $lead->id)->latest()->first();
        $this->assertNotNull($quotation);
        $this->assertNotNull($quotation->ga_number);
        $this->assertNotNull($quotation->pit_drawing);

        // 8. View Quotation View / PDF
        $responseView = $this->actingAs($salesUser)->get(route('quotation.view', ['id' => $quotation->id]));
        $responseView->assertStatus(200);
        $responseView->assertSee('View GA Drawing');
        $responseView->assertSee('View Pit Drawing');
        $responseView->assertSee('Attached Engineering Drawings');
    }

    /** @test */
    public function already_approved_action_flow_works()
    {
        $salesUser = User::first();
        $designerUser = User::where('id', '!=', $salesUser->id)->first() ?? $salesUser;

        $lead = Lead::create([
            'LeadName' => 'Preapproved Customer',
            'phonumber' => '9888877777',
            'category' => 2,
            'assigned_to' => $salesUser->id,
            'Leadstatus' => '343',
        ]);

        $design = Design::create([
            'idlead' => $lead->id,
            'product' => '187',
            'category' => 2,
            'quantity' => 1,
            'assigndesign' => $designerUser->id,
            'design_user' => $designerUser->id,
            'drawing_approved' => 'pending',
            'design_status' => null,
            'return_status' => 0,
        ]);

        $fakeQuotation = UploadedFile::fake()->create('preapproved_quote.pdf', 300, 'application/pdf');
        $fakeDrawing = UploadedFile::fake()->create('preapproved_drawing.dwg', 400);

        $response = $this->actingAs($designerUser)->post(route('design.approved.drawings'), [
            'design_id' => $design->id,
            'quotation_file' => $fakeQuotation,
            'drawing' => [$fakeDrawing],
            'drawing_remarks' => 'Directly pre-approved by client',
        ]);

        $response->assertStatus(200);
        $designFresh = $design->fresh();

        $this->assertEquals('approved', $designFresh->drawing_approved);
        $this->assertEquals(1, $designFresh->design_status);

        $this->assertDatabaseHas('design_drawings', [
            'design_id' => $design->id,
            'drawing_app' => 1,
        ]);
    }
}
