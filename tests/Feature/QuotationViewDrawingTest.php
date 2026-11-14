<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationViewDrawingTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function quotation_view_renders_successfully_with_drawings()
    {
        $user = User::whereHas('permissions', function($q) {
            $q->where('name', 'quotation-display');
        })->orWhereHas('roles.permissions', function($q) {
            $q->where('name', 'quotation-display');
        })->first();

        if (!$user) {
            $user = User::first();
            try {
                $user?->givePermissionTo('quotation-display');
            } catch (\Throwable $e) {}
        }

        $quotation = Quotation::where('id', 153)->first();
        if (!$quotation) {
            $quotation = Quotation::latest()->first();
        }

        if (!$quotation) {
            $this->markTestSkipped('No quotation found');
        }

        $quotation->update([
            'ga_number' => 'assets/images/sample_ga.pdf',
            'pit_drawing' => 'assets/images/sample_pit.png',
        ]);

        $response = $this->actingAs($user)->get('/quotation/view/' . $quotation->id);

        $response->assertStatus(200);
        $response->assertSee('View GA Drawing');
        $response->assertSee('View Pit Drawing');
        $response->assertSee('Attached Engineering Drawings');
    }
}
