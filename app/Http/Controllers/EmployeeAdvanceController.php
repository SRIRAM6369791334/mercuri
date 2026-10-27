<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use Illuminate\Http\Request;

class EmployeeAdvanceController extends Controller
{
    //
    public function create()
    {
        $employees = Employee::all();
        return view('pages.hr.createadvance', compact('employees'));
    }

    public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'amount' => 'required|numeric|min:1',
        'advance_date' => 'required|date',
    ]);

    EmployeeAdvance::create([
        'employee_id' => $request->employee_id,
        'amount' => $request->amount,
        'reason' => $request->reason,
        'advance_date' => $request->advance_date,
        'status' => 'Given'
    ]);

    return redirect()->route('employee.advance.show', $request->employee_id)
                     ->with('success', 'Advance added successfully.');
}


    public function show($id)
{
    $employee = Employee::findOrFail($id);
    $advances = EmployeeAdvance::where('employee_id', $id)->get();
    $total = $advances->sum('amount');

    return view('pages.hr.employee_advance_show', compact('employee', 'advances', 'total'));
}


    public function listshow()
{
    $advances = EmployeeAdvance::with('employee')
        ->selectRaw('employee_id, SUM(amount) as total_amount')
        ->groupBy('employee_id')
        ->latest()
        ->get();

    return view('pages.hr.employeeadvancelist', compact('advances'));
}

}
