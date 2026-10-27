<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            "Super Admin",
            "Admin",
            "Director",
            "Sales Manager",
            "Service Manager",
            "Service Engineer",
            "Finance Manager",
            "HR Manager",
            "Admin Manager",
            "Dispatch Manager",
            "Sales Coordinator",
            "Service Coordinator",
            "Design Engineer",
            "Purchase Manager",
        ];

        foreach ($roles as $data) {
            $role = new Role();
            $role->name = $data;
            $role->guard_name = 'web';
            $role->save();
        }
    }
}
