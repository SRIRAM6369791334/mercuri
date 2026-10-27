<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{

    public function department(){

    $departments = Department::all();
    return view('pages.hr.department', compact('departments'));
}

public function store(Request $request)
{
    Department::create([
        'department'   => $request->designation,
        'logintime'    => $request->starttime,
        'loginbuffer'  => $request->startbuftime,
        'logouttime'   => $request->endtime,
        'logoutbuffer' => $request->endbuftime,
    ]);

    return redirect()->back()->with('success', 'Department added successfully.');
}

public function designationupdate(Request $request)
{
    // Fetch the department record by ID
    $department = Department::findOrFail($request->editid);

    // Update the fields
    $department->department = $request->designation;
    $department->logintime = $request->starttimeu;
    $department->loginbuffer = $request->startbuftimeu;
    $department->logouttime = $request->endtimeu;
    $department->logoutbuffer = $request->endbuftimeu;

    $department->save();

    return redirect()->back()->with('success', 'Department updated successfully.');
}

public function destroy($id)
{
    $department = Department::findOrFail($id);
    $department->delete(); // Use soft delete if you enabled it

    return response()->json([
        'success' => true,
        'message' => 'Department deleted successfully.'
    ]);
}



}
