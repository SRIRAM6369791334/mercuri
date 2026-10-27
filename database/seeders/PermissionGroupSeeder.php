<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionGroupsList = [
            "Users",
            "Product Categories",
            "Products",
            "Customer",
            "Terms and Condition",
            "Leads",
            "Design",
            "Quotation",
            "ORF",
            "Reports",
            "Approvals",
            "ORF Approval",
            "Vendor Master",
            "Vendors",
            "Purchase Order",
            "Purchase Entry",
            "Product Stock",
            "Roles and Permissions",
            "Roles",
            "Permissions",
            "Dashboard Details"
        ];

        foreach($permissionGroupsList as $data)
        {
            $permissionGroups = new PermissionGroup();
            $permissionGroups->name = $data;
            $permissionGroups->save();
        }
    }
}
