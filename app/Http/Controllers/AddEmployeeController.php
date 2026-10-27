<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeBank;
use App\Models\EmployeeEmergencyContact;
use App\Models\EmployeeEducation;
use App\Models\EmployeeExperience;
use App\Models\EmployeeDocument;

class AddEmployeeController extends Controller
{

    public function employeelist()
{
    $employees = Employee::latest()->get();
    return view('pages.hr.employeelist', compact('employees'));
}


public function Addemployeeinsert(Request $request)
{

    // dd($request->all());

    $latestEmployee = Employee::orderBy('id', 'desc')->first();
    $nextNumber = $latestEmployee ? ($latestEmployee->id + 1) : 1;
    $emp_id = 'MSE-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

    // Step 1: Insert into `employees`
    $employee = new Employee();
    $employee->emp_id = $emp_id;
    $employee->name = $request->name;
    $employee->secondname = $request->secondname;
    $employee->dob = $request->dob;
    $employee->Qualification = $request->Qualification;
    $employee->mobile_no = $request->mobile_no;
    $employee->email = $request->email;
    $employee->nationality = $request->nationality;
    $employee->doj = $request->doj;
    $employee->gender = $request->gender;
    $employee->oridesignation = $request->oridesignation;
    $employee->salary = $request->basic ?? 0;
    $employee->bloodgroup = $request->bloodgroup;
    $employee->temp_address = $request->temp_address;
    $employee->per_address = $request->per_address;
    $employee->employestat = $request->employestat ?? 0;
    $employee->maritalstatus = $request->maritalstatus ?? 0;

    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/employees'), $filename);
        $employee->photo = $filename;
    }

    $employee->save();

    // ✅ Step 2: Optional Bank Details — Insert only if any bank field is filled
    $bankFields = ['bankname', 'branchname', 'accholdername', 'acc_number', 'acc_no', 'ifsc_code'];
    $hasBankData = false;
    foreach ($bankFields as $field) {
        if (!empty($request->$field)) {
            $hasBankData = true;
            break;
        }
    }

    if ($hasBankData) {
        EmployeeBank::create([
            'employee_id' => $employee->id,
            'bankname' => $request->bankname,
            'branchname' => $request->branchname,
            'accholdername' => $request->accholdername,
            'acc_number' => $request->acc_number,
            'ifsc_code' => $request->acc_no, // IFSC
            'acc_no' => $request->ifsc_code // Swift
        ]);
    }

    // ✅ Step 3: Emergency Contacts
   if ($request->has('f_name')) {
    foreach ($request->f_name as $index => $name) {
        $relation = $request->f_relation[$index] ?? null;
        $mobile = $request->f_mobile[$index] ?? null;
        $email = $request->f_email[$index] ?? null;

        if (!empty($name) || !empty($relation) || !empty($mobile) || !empty($email)) {
            EmployeeEmergencyContact::create([
                'employee_id' => $employee->id,
                'name' => $name ?? '',
                'relation' => $relation ?? '',
                'mobile' => $mobile ?? '',
                'email' => $email ?? '',
            ]);
        }
    }
}



    // ✅ Step 4: Education Details
    $documents = $request->file('document', []); // safely get all files

foreach ($request->level as $index => $level) {
    if (!empty($level)) {
        $doc = null;

        if (isset($documents[$index]) && $documents[$index]->isValid()) {
            $file = $documents[$index];
            $doc = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/documents'), $doc);
        }

        EmployeeEducation::create([
            'employee_id' => $employee->id,
            'level' => $level,
            'degree' => $request->degree[$index] ?? '',
            'university' => $request->university[$index] ?? '',
            'passedout' => $request->passedout[$index] ?? '',
            'document' => $doc,
        ]);
    }
}


    // ✅ Step 5: Experience
    if ($request->has('exp_start_date')) {
    foreach ($request->exp_start_date as $index => $start) {
        if (!empty($start)) {
            EmployeeExperience::create([
                'employee_id' => $employee->id,
                'exp_start_date' => $start,
                'exp_end_date' => $request->exp_end_date[$index] ?? '',
                'period' => $request->period[$index] ?? '',
                'role' => $request->role[$index] ?? '',
                'company' => $request->company[$index] ?? '',
                'empdesignation' => $request->empdesignation[$index] ?? '',
            ]);
        }
    }
}


    // ✅ Step 6: Documents
    $doc = new EmployeeDocument();
    $doc->employee_id = $employee->id;

    if ($request->hasFile('image1')) {
        $aadhaar = $request->file('image1');
        $aadhaarName = 'aadhaar_' . time() . '.' . $aadhaar->getClientOriginalExtension();
        $aadhaar->move(public_path('uploads/documents'), $aadhaarName);
        $doc->aadhaar_file = $aadhaarName;
    }

    if ($request->hasFile('image2')) {
        $pan = $request->file('image2');
        $panName = 'pan_' . time() . '.' . $pan->getClientOriginalExtension();
        $pan->move(public_path('uploads/documents'), $panName);
        $doc->pan_file = $panName;
    }

    $doc->save();

    return redirect()->route('employee.list')->with('success', 'Employee created successfully.');
}

public function edit($id)
{
    $employee = Employee::with(['bank','documents','emergencyContacts', 'educations', 'experiences'])->findOrFail($id); // Eager load relations if any
    $countries = Country::all();
    return view('pages.hr.editemployee', compact('employee', 'countries'));
}

public function editemployeeupdate(Request $request)
{
    $employee = Employee::findOrFail($request->emid);
 // Assuming `id` is sent as hidden input

    // ✅ Update Employee Details
    $employee->name = $request->name;
    $employee->secondname = $request->secondname;
    $employee->dob = $request->dob;
    $employee->Qualification = $request->Qualification;
    $employee->mobile_no = $request->mobile_no;
    $employee->email = $request->email;
    $employee->nationality = $request->nationality;
    $employee->doj = $request->doj;
    $employee->gender = $request->gender;
    $employee->oridesignation = $request->oridesignation;
    $employee->salary = $request->basic ?? 0;
    $employee->bloodgroup = $request->bloodgroup;
    $employee->temp_address = $request->temp_address;
    $employee->per_address = $request->per_address;
    $employee->employestat = $request->employestat ?? 0;
    $employee->maritalstatus = $request->maritalstatus ?? 0;

    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/employees'), $filename);
        $employee->photo = $filename;
    }

    $employee->save();

    // ✅ Update or Create Bank
    $bank = EmployeeBank::firstOrNew(['employee_id' => $employee->id]);
    $bank->bankname = $request->bankname;
    $bank->branchname = $request->branchname;
    $bank->accholdername = $request->accholdername;
    $bank->acc_number = $request->acc_number;
    $bank->ifsc_code = $request->acc_no;
    $bank->acc_no = $request->ifsc_code;
    $bank->save();

    // ✅ Emergency Contacts
    EmployeeEmergencyContact::where('employee_id', $employee->id)->delete();
    if ($request->has('f_name')) {
        foreach ($request->f_name as $index => $name) {
            EmployeeEmergencyContact::create([
                'employee_id' => $employee->id,
                'name' => $name ?? '',
                'relation' => $request->f_relation[$index] ?? '',
                'mobile' => $request->f_mobile[$index] ?? '',
                'email' => $request->f_email[$index] ?? '',
            ]);
        }
    }

    // ✅ Education Details
    EmployeeEducation::where('employee_id', $employee->id)->delete();
    $documents = $request->file('document', []);
    foreach ($request->level as $index => $level) {
        if (!empty($level)) {
            $doc = null;

            if (isset($documents[$index]) && $documents[$index]->isValid()) {
                $file = $documents[$index];
                $doc = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/documents'), $doc);
            }

            EmployeeEducation::create([
                'employee_id' => $employee->id,
                'level' => $level,
                'degree' => $request->degree[$index] ?? '',
                'university' => $request->university[$index] ?? '',
                'passedout' => $request->passedout[$index] ?? '',
                'document' => $doc,
            ]);
        }
    }

    // ✅ Experience
    EmployeeExperience::where('employee_id', $employee->id)->delete();
    if ($request->has('exp_start_date')) {
        foreach ($request->exp_start_date as $index => $start) {
            if (!empty($start)) {
                EmployeeExperience::create([
                    'employee_id' => $employee->id,
                    'exp_start_date' => $start,
                    'exp_end_date' => $request->exp_end_date[$index] ?? '',
                    'period' => $request->period[$index] ?? '',
                    'role' => $request->role[$index] ?? '',
                    'company' => $request->company[$index] ?? '',
                    'empdesignation' => $request->empdesignation[$index] ?? '',
                ]);
            }
        }
    }

    // ✅ Documents
    $doc = EmployeeDocument::firstOrNew(['employee_id' => $employee->id]);

    if ($request->hasFile('image1')) {
        $aadhaar = $request->file('image1');
        $aadhaarName = 'aadhaar_' . time() . '.' . $aadhaar->getClientOriginalExtension();
        $aadhaar->move(public_path('uploads/documents'), $aadhaarName);
        $doc->aadhaar_file = $aadhaarName;
    }

    if ($request->hasFile('image2')) {
        $pan = $request->file('image2');
        $panName = 'pan_' . time() . '.' . $pan->getClientOriginalExtension();
        $pan->move(public_path('uploads/documents'), $panName);
        $doc->pan_file = $panName;
    }

    $doc->save();

    return redirect()->route('employee.list')->with('success', 'Employee updated successfully.');
}


// public function destroy($id)
// {
//     $employee = Employee::findOrFail($id);

//     // Delete employee photo
//     if ($employee->photo && file_exists(public_path('uploads/employees/' . $employee->photo))) {
//         unlink(public_path('uploads/employees/' . $employee->photo));
//     }

//     // Delete related document file paths (Aadhaar, PAN)
//     $document = EmployeeDocument::where('employee_id', $employee->id)->first();
//     if ($document) {
//         if ($document->aadhaar_file && file_exists(public_path('uploads/documents/' . $document->aadhaar_file))) {
//             unlink(public_path('uploads/documents/' . $document->aadhaar_file));
//         }

//         if ($document->pan_file && file_exists(public_path('uploads/documents/' . $document->pan_file))) {
//             unlink(public_path('uploads/documents/' . $document->pan_file));
//         }
//         $document->delete();
//     }

//     // Delete related records from other tables
//     EmployeeBank::where('employee_id', $employee->id)->delete();
//     EmployeeEducation::where('employee_id', $employee->id)->delete();
//     EmployeeEmergencyContact::where('employee_id', $employee->id)->delete();
//     EmployeeExperience::where('employee_id', $employee->id)->delete();

//     // Finally, delete the employee record
//     $employee->delete();

//     return response()->json(['success' => true, 'message' => 'Employee and related data deleted successfully.']);
// }
public function destroy($id)
{
    $employee = Employee::findOrFail($id);

    $employee->delete(); // 🔄 Soft delete

    return response()->json(['success' => true, 'message' => 'Employee deleted successfully.']);
}

public function showEmployeeReport($id)
{
     $employee = Employee::with([
        'bank',
        'emergencyContacts',
        'educations',
        'experiences',
        'documents',
        'assets.asset' // Load asset details from EmployeeAsset
    ])->findOrFail($id);

    return view('pages.hr.employeereport', compact('employee'));
}


}
