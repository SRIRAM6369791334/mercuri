<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function createPermission(Request $request)
    {
        $permission = new Permission();
        $permission->name = $request->name;
        $permission->guard_name = 'web';

        if ($permission->save()) {
            return redirect()->back()->with('success', 'Permission created successfully');
        } else {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function editPermission(Request $request)
    {
        $permission = Permission::where("id", $request->permissionId)->update(["name" => $request->permissionName]);

        if ($permission) {
            return redirect()->back()->with('success', 'Permission updated successfully');
        } else {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function addPermissionView($roleId)
    {
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->pluck('permission_id')->all();
        return view('pages.users.add_permissions', compact('roleId', 'rolePermissions'));
    }

    public function allocatePermission(Request $request)
    {

        $role = Role::findOrFail($request->roleId);
        $permissions = Permission::whereIn('id', $request->permissionsId)->where('guard_name', 'web')->get();
        $role->syncPermissions($permissions);

        return redirect()->route("roles")->with('success', 'Permissions allocated successfully');
    }

    public function checkRolefFunction(Request $request)
    {
        $role = Role::where("name", $request->role)->first();

        if ($role) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }
}
