<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'Sales Executive']);
        Role::firstOrCreate(['name' => 'Sales Coordinator']);
    }

    /** @test */
    public function updating_user_with_duplicate_email_fails_gracefully_with_error_flash()
    {
        $superAdmin = User::create([
            'name' => 'Super Admin User',
            'email' => 'admin_test@example.com',
            'role' => 'Super Admin',
            'userstatus' => 'Active',
            'password' => bcrypt('password123'),
        ]);
        try {
            $superAdmin->givePermissionTo('users-edit');
        } catch (\Throwable $e) {}

        $existingUser = User::create([
            'name' => 'Existing Sales User',
            'email' => 'existing_sales@example.com',
            'role' => 'Sales Coordinator',
            'userstatus' => 'Active',
            'password' => bcrypt('password123'),
        ]);

        // Attempt to update $superAdmin to have $existingUser's email
        $response = $this->actingAs($superAdmin)->post(route('users.update'), [
            'id' => $superAdmin->id,
            'name' => 'Super Admin Updated',
            'email' => 'existing_sales@example.com', // Duplicate!
            'mobilenumber' => '+91-9988776655',
            'type' => 'Super Admin',
            'userstatus' => 'Active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('already registered', session('error'));

        // Verify the database was NOT changed to the duplicate email
        $superAdmin->refresh();
        $this->assertEquals('admin_test@example.com', $superAdmin->email);
    }

    /** @test */
    public function updating_user_with_their_own_email_succeeds()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'same_email@example.com',
            'role' => 'Super Admin',
            'userstatus' => 'Active',
            'password' => bcrypt('password123'),
        ]);
        try {
            $admin->givePermissionTo('users-edit');
        } catch (\Throwable $e) {}

        // Updating profile keeping same email
        $response = $this->actingAs($admin)->post(route('users.update'), [
            'id' => $admin->id,
            'name' => 'Admin User Renamed',
            'email' => 'same_email@example.com', // Own email
            'mobilenumber' => '+91-9988776655',
            'type' => 'Super Admin',
            'userstatus' => 'Active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertEquals('Admin User Renamed', $admin->name);
        $this->assertEquals('same_email@example.com', $admin->email);
    }

    /** @test */
    public function updating_user_with_fresh_unique_email_succeeds()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'old_email@example.com',
            'role' => 'Super Admin',
            'userstatus' => 'Active',
            'password' => bcrypt('password123'),
        ]);
        try {
            $admin->givePermissionTo('users-edit');
        } catch (\Throwable $e) {}

        $response = $this->actingAs($admin)->post(route('users.update'), [
            'id' => $admin->id,
            'name' => 'Admin User',
            'email' => 'brand_new_email@example.com',
            'mobilenumber' => '+91-9988776655',
            'type' => 'Super Admin',
            'userstatus' => 'Active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertEquals('brand_new_email@example.com', $admin->email);
    }

    /** @test */
    public function creating_user_with_duplicate_email_fails_gracefully()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_create@example.com',
            'role' => 'Super Admin',
            'userstatus' => 'Active',
            'password' => bcrypt('password123'),
        ]);
        try {
            $admin->givePermissionTo('users-create');
        } catch (\Throwable $e) {}

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Duplicate Email User',
            'email' => 'admin_create@example.com', // Duplicate!
            'mobilenumber' => '+91-9988771122',
            'role' => 'Sales Executive',
            'userstatus' => 'Active',
            'password' => 'secret123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function get_request_to_updateuser_and_adduser_redirects_to_users()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_nav@example.com',
            'role' => 'Super Admin',
            'userstatus' => 'Active',
            'password' => bcrypt('password123'),
        ]);

        $responseUpdate = $this->actingAs($admin)->get('/updateuser');
        $responseUpdate->assertRedirect('/users');

        $responseAdd = $this->actingAs($admin)->get('/adduser');
        $responseAdd->assertRedirect('/users');
    }
}
