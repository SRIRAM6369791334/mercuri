<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required|string',
            'userstatus' => 'required|string',
            'password' => 'required|min:6',
        ]);

        $isExist = User::where('email', $request->email)->first();

        if ($isExist) {
            return back()->with('error', 'The email "' . $request->email . '" is already registered. Please use a different email address.')->withInput();
        }

        // Directly create the user
        $user = User::create([
            'name' => $request->name,
            'mobilenumber' => $request->mobilenumber,
            'email' => $request->email,
            'role' => $request->role,
            'userstatus' => $request->userstatus,
            'tl_id' => $request->tl_id ?: null,
            'password' => Hash::make($request->password),
        ]);

        // Assign role using Spatie (ensure the role exists in the form)
        if (Role::where('name', $request->role)->exists()) {
            $user->assignRole($request->role);
            $user->syncRoles([$request->role]);
        }

        return redirect()->back()->with('success', 'User added successfully');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'type' => 'required|string',
            'userstatus' => 'required|string',
        ]);

        $user = User::findOrFail($request->id);

        // Check if email already belongs to another user
        $emailExists = User::where('email', $request->email)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($emailExists) {
            return redirect()->back()->with('error', 'The email "' . $request->email . '" is already registered to another user. Please use a different email address.')->withInput();
        }

        $user->name = $request->name;
        $user->mobilenumber = $request->mobilenumber;
        $user->email = $request->email;
        $user->role = $request->type;
        $user->userstatus = $request->userstatus;
        $user->tl_id = $request->tl_id ?: null;

        $user->save();

        // Sync roles using Spatie (ensure role exists)
        if (Role::where('name', $request->type)->exists()) {
            $user->syncRoles([$request->type]);
        }

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function updatePassword(Request $request)
    {


        $user = User::find($request->id);

        $user->update([
            'password' => Hash::make($request->passworded),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function storerole(Request $request)
    {
        $role = Role::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Role added successfully');

    }

    public function updateRole(Request $request)
    {
        $role = Role::where("id", $request->roleId)->update(['name' => $request->name]);

        if ($role) {
            return response()->json(["status"=>"success"]);
        } else {
            return response()->json(["status"=>"error"]);
        }

    }

    public function deleteRole($roleId)
    {
        $role = Role::find($roleId)->delete();

        if ($role) {
            return redirect()->back()->with('success', 'Role deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Something went wrong');
        }

    }

 public function getUsersByRole(Request $request)
    {
        $role = $request->get('role');

        if (!$role) {
            return response()->json(['users' => []]);
        }

        $users = User::where('role', $role)
                    ->where('userstatus', 'Active')
                    ->where('id', '!=', auth()->id()) // Exclude current user
                    ->get(['id', 'name', 'role']);

        return response()->json(['users' => $users]);
    }

    public function destroy($id)
    {
        if (auth()->user()?->role !== 'Super Admin') {
            return redirect()->back()->with('error', 'Unauthorized. Only Super Admin has permission to delete users.');
        }

        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if (auth()->id() == $id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Check if other users report to this user as Team Leader
        if (User::where('tl_id', $id)->exists()) {
            return redirect()->back()->with('error', 'Cannot delete user: Other users report to this user as Team Leader. Please reassign them first.');
        }

        // Check if user is set in assign_settings
        if (\Illuminate\Support\Facades\Schema::hasTable('assign_settings')) {
            $inAssign = \Illuminate\Support\Facades\DB::table('assign_settings')
                ->where('mhe_special_user_id', $id)
                ->orWhere('pallet_user_id', $id)
                ->orWhere('racking_user_id', $id)
                ->exists();
            if ($inAssign) {
                return redirect()->back()->with('error', 'Cannot delete user: This user is assigned in Default Assignment Settings. Please reassign them first.');
            }
        }

        try {
            // Remove assigned Spatie roles cleanly
            $user->syncRoles([]);
            $user->delete();

            return redirect()->back()->with('success', 'User deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'Cannot delete this user because they have associated records in the system.');
        }
    }

}
