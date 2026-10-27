<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\EmployeeAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AddassetController extends Controller
{

    public function assetdetaillist(){

    $assets = EmployeeAsset::with(['employee', 'asset'])->get();
    return view('pages.hr.employeeassetlist', compact('assets'));
}

public function addnewassetinsert(Request $request)
{
    $data = new Asset;
    $data->asset_name = $request->newasset;
    $data->serial_number = $request->serialno;

    if ($data->save()) {
        return response()->json([
            'id' => $data->id,
            'asset_name' => $data->asset_name,
            'serial_number' => $data->serial_number
        ]);
    } else {
        return response()->json(['error' => 'Asset add failed'], 500);
    }
}

public function gettsupprod()
{
    return response()->json(Asset::select('id', 'asset_name', 'serial_number')->get());
}

public function assetdetailinsert(Request $request)
    {
        $addasset_em = $request->addasset_em; // This value remains the same for all assets

        $assets = $request->input('assets');
        $qty = $request->input('qty');

        $numAssets = count($assets);

        for ($i = 0; $i < $numAssets; $i++) {
            $data = new EmployeeAsset;
            $data->asset_id = $assets[$i];
            $data->quantity = $qty[$i];
            $data->employee_id = $addasset_em; // Assign the same emp_name for each entry
            $data->status = 'Provided';
            $data->assigned_date = today();

            if (!$data->save()) {
                return back()->with('error', 'Asset added failed');
            }
        }

        return back()->with('Success', 'Assets added successfully');
    }

    public function employeeAssetReport($id)
{
    $assets = EmployeeAsset::with(['employee', 'asset'])
        ->where('employee_id', $id)
        ->get()
        ->map(function ($item) {
            return [
                // 'emp_name' => $item->employee->emp_id ?? '',
                // 'emname' => $item->employee->name ?? '',
                'asnam' => $item->asset->asset_name ?? '',
                'a_qty' => $item->quantity,
                'Status' => $item->status,
                'serial_number' => $item->asset->serial_number ?? '', // Include if exists
            ];
        });

    return response()->json($assets);
}

public function edit($id)
{
    $employee = Employee::with(['assets'])->where('id', $id)->first();
    $assets = EmployeeAsset::with('asset')
        ->where('employee_id', $id)
        ->get();

    return view('pages.hr.assets_edit', compact('employee', 'assets'));
}

public function return(Request $request)
{
    $asset = EmployeeAsset::findOrFail($request->asset_id);

    // Reduce quantity (or mark as returned depending on your logic)
    $asset->quantity -= $request->return_qty;
    $asset->status = $request->return_status;
    $asset->save();

    return redirect()->back()->with('success', 'Asset returned successfully.');
}


}
