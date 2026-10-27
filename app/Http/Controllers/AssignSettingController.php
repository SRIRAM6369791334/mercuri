<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssignSetting;
use App\Models\User;

class AssignSettingController extends Controller
{
    //
    public function edit($id = null)
    {
        $assignSetting = AssignSetting::first();
        $users = User::orderBy('name')->get();

        return view('pages.assignsettings', compact('assignSetting', 'users'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mhe_special_user_id' => 'nullable|exists:users,id',
            'racking_user_id'     => 'nullable|exists:users,id',
            'pallet_user_id'      => 'nullable|exists:users,id',
        ]);

        AssignSetting::updateOrCreate(
            ['id' => 1],
            $request->only([
                'mhe_special_user_id',
                'racking_user_id',
                'pallet_user_id'
            ])
        );

        return back()->with('success', 'Assignment settings updated successfully');
    }
}
