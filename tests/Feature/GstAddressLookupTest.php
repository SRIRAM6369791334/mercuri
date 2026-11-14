<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Vendor;

class GstAddressLookupTest extends TestCase
{
    public function test_gst_lookup_returns_mercuriis_branches_for_own_gst()
    {
        $user = User::first();
        $response = $this->actingAs($user)->getJson('/api/get-addresses-by-gst/33AATCM0824C1ZQ');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'found' => true,
                'count' => 2,
            ]);

        $data = $response->json();
        $this->assertCount(2, $data['addresses']);
        $this->assertEquals('Chennai', $data['addresses'][0]['city']);
        $this->assertEquals('Coimbatore', $data['addresses'][1]['city']);
    }

    public function test_gst_lookup_validates_short_gst()
    {
        $user = User::first();
        $response = $this->actingAs($user)->getJson('/api/get-addresses-by-gst/123');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'found' => false,
                'count' => 0,
            ]);
    }

    public function test_gst_lookup_returns_addresses_with_ids_for_customer()
    {
        $user = User::first();
        $response = $this->actingAs($user)->getJson('/api/get-addresses-by-gst/33AAACS4909F1ZN');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'found' => true,
            ]);

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, $data['count']);
        $first = $data['addresses'][0];
        $this->assertArrayHasKey('label', $first);
        $this->assertArrayHasKey('address', $first);
        $this->assertArrayHasKey('city_id', $first);
        $this->assertArrayHasKey('state_id', $first);
        $this->assertArrayHasKey('postalcode', $first);
    }
}
