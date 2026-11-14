<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\Design;
use App\Models\DesignDrawing;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationDrawingAttachmentTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function models_have_drawing_fields_in_fillable()
    {
        $quotation = new Quotation();
        $this->assertContains('ga_number', $quotation->getFillable());
        $this->assertContains('pit_drawing', $quotation->getFillable());

        $design = new Design();
        $this->assertContains('ganumber', $design->getFillable());
        $this->assertContains('pit_drawing', $design->getFillable());

        $drawing = new DesignDrawing();
        $this->assertContains('drawing_type', $drawing->getFillable());
    }

    /** @test */
    public function quotation_persists_ga_number_and_pit_drawing()
    {
        $quotation = Quotation::create([
            'quota_id' => 'TEST/2026/9999',
            'ga_number' => 'assets/images/test_ga_drawing.pdf',
            'pit_drawing' => 'assets/images/test_pit_drawing.png',
            'subtotal' => 100000,
            'tax_total' => 18000,
            'grand_total' => 118000,
        ]);

        $this->assertNotNull($quotation->id);
        $this->assertEquals('assets/images/test_ga_drawing.pdf', $quotation->ga_number);
        $this->assertEquals('assets/images/test_pit_drawing.png', $quotation->pit_drawing);
    }
}
