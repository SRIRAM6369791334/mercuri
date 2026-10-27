<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionGroups = PermissionGroup::get();

        foreach ($permissionGroups as $data) {
            $permissionDetails = [
                "Create",
                "Edit",
                "Delete",
                "Display",
            ];

            $permissionDashboardDetails = [
                "Order Booking Value Sales",
                "Order Booking Value Spares",
                "Total Billing Value",
                "Total Outstanding Value",
                "Total Payable Value",
                "Stock Value Equipment",
                "Stock Value Spares"
            ];

            if ($data->id == 21) {
                foreach ($permissionDashboardDetails as $permissionDetail) {
            //         $permissions = new Permission();
            //         $permissions->name = strtolower(str_replace(' ', '-', strtolower($permissionDetail)));
            //         $permissions->guard_name = 'web';
            //         $permissions->permission_group_id = $data->id;
            //         $permissions->permission_name = $permissionDetail;
            //         $permissions->save();
            //     }

            // }else{
            //     foreach ($permissionDetails as $permissionDetail) {
            //         $permissions = new Permission();
            //         $permissions->name = strtolower(str_replace(' ', '-', $data->name) . "-" . strtolower($permissionDetail));
            //         $permissions->guard_name = 'web';
            //         $permissions->permission_group_id = $data->id;
            //         $permissions->permission_name = $permissionDetail;
            //         $permissions->save();
               Permission::firstOrCreate([
                        'name' => strtolower(str_replace(' ', '-', strtolower($permissionDetail))),
                        'guard_name' => 'web'
                    ], [
                        'permission_group_id' => $data->id,
                        'permission_name' => $permissionDetail
                    ]);
                }

            } elseif ($data->id == 11) {
                $approvalPermissions = [
                    "orf-approval",
                    "quotation-approval",
                    "discount-approval",
                    "terms-approval"
                ];
                foreach ($approvalPermissions as $permission) {
                    Permission::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => 'web'
                    ], [
                        'permission_group_id' => $data->id,
                        'permission_name' => ucfirst(str_replace('-', ' ', $permission))
                    ]);
                }
            } else {
                foreach ($permissionDetails as $permissionDetail) {
                    Permission::firstOrCreate([
                        'name' => strtolower(str_replace(' ', '-', $data->name) . "-" . strtolower($permissionDetail)),
                        'guard_name' => 'web'
                    ], [
                        'permission_group_id' => $data->id,
                        'permission_name' => $permissionDetail
                    ]);

                }
            }
        }
    }
}
