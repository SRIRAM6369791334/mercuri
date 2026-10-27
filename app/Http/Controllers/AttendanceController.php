<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holidaydb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{

    public function attendancelist(Request $request)
{
    $date = $request->get('date', Carbon::now()->format('Y-m-d'));

    // Get all employees
    $employees = Employee::all();

    // Attach attendance data to each employee for the selected date
    foreach ($employees as $employee) {
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $date)
            ->first();

        $employee->punch_in_time = $attendance->punch_in_time ?? null;
        $employee->punch_out_time = $attendance->punch_out_time ?? null;
        $employee->punch_in_image = $attendance->punch_in_image ?? null;
        $employee->punch_in_location = $attendance->punch_in_location ?? null;
        $employee->worked_hours = $attendance->total_work_duration ?? null;
    }

    return view('pages.hr.attendancelist', compact('employees'));
}

    public function punchIn(Request $request)
{
    $imageName = null;

    if ($request->hasFile('face_image')) {
        $file = $request->file('face_image');
        $imageName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/attendance'), $imageName);
    }

    Attendance::create([
        'employee_id' => $request->employee_id,
        'punch_in_time' => now(),
        'punch_in_image' => $imageName,
        'punch_in_location' => $request->location ?? 'Auto',
    ]);

    return back()->with('success', 'Punch in recorded successfully.');
}


public function addholiday(){

        $addholiday = DB::table('holiday_tb')->get();

    // Pass the $holidays data to your view
    return view('pages.hr.addholiday', ['addholiday' => $addholiday]);
}

public function Addholidayinsert(Request $request)
{
    $dateholi = $request->input('dateholi');
    // $dayholi = $request->input('dayholi');
    $holiname = $request->input('holiname');
  $existingDates = [];
    // Assuming Dailyattendence is your model name
    // foreach ($dateholi as $key => $value) {
    //     $attendance = new Holidaydb();
    //     $attendance->holi_date = $dateholi[$key];
    //     // $attendance->holi_day = $dayholi[$key];
    //     $attendance->holi_name = $holiname[$key];
    //     $attendance->save();
    // }
    // foreach ($dateholi as $key => $value) {
    //     // Check if the holiday date already exists in the database
    //     $existingHoliday = Holidaydb::where('holi_date', $dateholi[$key])->first();

    //     // If the holiday date doesn't exist, insert it
    //     if (!$existingHoliday) {
    //         $attendance = new Holidaydb();
    //         $attendance->holi_date = $dateholi[$key];
    //         $attendance->holi_name = $holiname[$key];
    //         $attendance->save();
    //     }
    // }

    // return redirect()->back()->with('success', 'Holidays added successfully');
    foreach ($dateholi as $key => $value) {
        $existingHoliday = Holidaydb::where('holi_date', $dateholi[$key])->first();

        if ($existingHoliday) {
            // If the date already exists, add it to the list of existing dates
            $existingDates[] = $dateholi[$key];
        } else {
            $attendance = new Holidaydb();
            $attendance->holi_date = $dateholi[$key];
            $attendance->holi_name = $holiname[$key];
            $attendance->save();
        }
    }

    if (!empty($existingDates)) {
        // If there are existing dates, pass them to the view to display the alert
        return redirect()->back()->with('existingDates', $existingDates)->with('success', 'Holidays added successfully');
    } else {
        return redirect()->back()->with('success', 'Holidays added successfully');
    }
}

public function editholidayinsert(Request $request)
{
    $attid = $request->input('editholiid');
    $dateholi = $request->input('dateholied');
    $dayholi = $request->input('dayholied');
    $holiname = $request->input('holinameed');

    // $attendance = Holidaydb::find($attid);
    //     $attendance->holi_date = $dateholi;
    //     $attendance->holi_day = $dayholi;
    //     $attendance->holi_name = $holiname;
    //     $attendance->save();


    // return redirect()->back()->with('success', 'Holidays Updated successfully');

      $existingHoliday = Holidaydb::where('holi_date', $dateholi)->where('id', '!=', $attid)->first();

    // If the updated holiday date doesn't conflict with existing dates, update the holiday
    if (!$existingHoliday) {
        $attendance = Holidaydb::find($attid);
        $attendance->holi_date = $dateholi;
        $attendance->holi_day = $dayholi;
        $attendance->holi_name = $holiname;
        $attendance->save();

        return redirect()->back()->with('success', 'Holiday Updated successfully');
    } else {
        // If the updated holiday date conflicts with existing dates, return with an error message
       return redirect()->back()->with('existingDates', [$dateholi])->with('error', 'Holiday date conflicts with existing holidays');
    }

}

public function deleteholiday($id)
{
    $dfj = Holidaydb::destroy($id);
    return response()->json(['success' => true]);
}

}
