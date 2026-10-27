<?php

namespace App\Http\Controllers;

use App\Models\FreightCategory;
use App\Models\FreightSubCategory;
use App\Models\QuotationInstallationScope;
use App\Models\QuotationOrcOption;
use App\Models\QuotationStatus;
use App\Models\QuotationUnloadingOption;
// use Dotenv\Validator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
    public function viewde()
    {
        $designations = DB::table('dropdowndatas')
            ->where('formid', 80)
            ->orderBy('orderno')
            ->get();

        $departments = DB::table('dropdowndatas')
            ->where('formid', 81)
            ->orderBy('orderno')
            ->get();

        $freightCategories = FreightCategory::orderBy('id')->get();
        $freightSubCategories = FreightSubCategory::leftJoin('freight_category', 'freight_sub_category.freight_cat_id', '=', 'freight_category.id')
            ->select('freight_sub_category.*', 'freight_category.categoty as freight_category_name')
            ->orderBy('freight_sub_category.id')
            ->get();
        $unloadingOptions = QuotationUnloadingOption::orderBy('sort_order')->orderBy('name')->get();
        $installationScopes = QuotationInstallationScope::orderBy('sort_order')->orderBy('name')->get();
        $orcOptions = QuotationOrcOption::orderBy('sort_order')->orderBy('name')->get();
        $quotationStatuses = QuotationStatus::orderBy('name')->get();

        return view('pages.masters.desig_depart', compact(
            'designations',
            'departments',
            'freightCategories',
            'freightSubCategories',
            'unloadingOptions',
            'installationScopes',
            'orcOptions',
            'quotationStatuses'
        ));
    }

//   public function store(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'formid' => 'required',
//         'name' => 'required|string|max:30',
//         'orderno' => [
//             'required',
//             'integer',
//             function ($attribute, $value, $fail) use ($request) {
//                 $exists = DB::table('dropdowndatas')
//                     ->where('formid', $request->formid)
//                     ->where('orderno', $value)
//                     ->exists();

//                 if ($exists) {
//                     $fail('Order number already exists.');
//                 }
//             }
//         ],
//     ]);

//     if ($validator->fails()) {
//         return back()
//             ->withErrors($validator)
//             ->withInput();
//     }

//     DB::table('dropdowndatas')->insert([
//         'formid' => $request->formid,
//         'dropdowndata' => $request->name,
//         'orderno' => $request->orderno,
//     ]);

//     return back()->with('success', 'Created successfully');
// }


//     public function edit($id)
//     {
//         return DB::table('dropdowndatas')->find($id);
//     }

//     public function update(Request $request, $id)
// {
//     $validator = Validator::make($request->all(), [
//         'name' => 'required|string|max:30',
//         'orderno' => [
//             'required',
//             'integer',
//             function ($attribute, $value, $fail) use ($request, $id) {
//                 $exists = DB::table('dropdowndatas')
//                     ->where('formid', $request->formid)
//                     ->where('orderno', $value)
//                     ->where('id', '!=', $id)
//                     ->exists();

//                 if ($exists) {
//                     $fail('Order number already exists.');
//                 }
//             }
//         ],
//     ]);

//     if ($validator->fails()) {
//         return back()
//             ->withErrors($validator)
//             ->withInput();
//     }

//     DB::table('dropdowndatas')->where('id', $id)->update([
//         'dropdowndata' => $request->name,
//         'orderno' => $request->orderno,
//     ]);

//     return back()->with('success', 'Updated successfully');
// }

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'formid' => 'required',
        'name' => 'required|string|max:30',
    ]);

    if ($validator->fails()) {
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        return back()->withErrors($validator)->withInput();
    }

    // Get next order number based on formid
    $nextOrder = DB::table('dropdowndatas')
        ->where('formid', $request->formid)
        ->max('orderno');

    $nextOrder = $nextOrder ? $nextOrder + 1 : 1;

    $id = DB::table('dropdowndatas')->insertGetId([
        'formid' => $request->formid,
        'dropdowndata' => $request->name,
        'orderno' => $nextOrder,
    ]);

    if ($request->ajax() || $request->expectsJson()) {
        return response()->json([
            'message' => 'Created successfully',
            'success' => true,
            'data' => [
                'id' => $id,
                'formid' => (int) $request->formid,
                'name' => $request->name,
                'orderno' => $nextOrder,
            ],
        ]);
    }

    return back()->with('success', 'Created successfully');
}

    public function edit($id)
    {
        return DB::table('dropdowndatas')->find($id);
    }


    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:30',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    DB::table('dropdowndatas')
        ->where('id', $id)
        ->update([
            'dropdowndata' => $request->name,
        ]);

    return back()->with('success', 'Updated successfully');
}
    public function destroy($id)
    {
        DB::table('dropdowndatas')->where('id', $id)->delete();
        return back()->with('success', 'Deleted successfully');
    }

    public function storeQuotationField(Request $request)
    {
        $config = $this->quotationFieldConfig($request->type);

        $rules = [
            'type' => 'required|string',
            'name' => 'required|string|max:100',
        ];

        if ($request->type === 'freight_sub_category') {
            $rules['freight_cat_id'] = 'required|exists:freight_category,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [$config['name_column'] => $request->name];

        if ($request->type === 'freight_sub_category') {
            $data['freight_cat_id'] = $request->freight_cat_id;
        }

        if ($config['uses_sort_order'] ?? false) {
            $data['sort_order'] = $config['model']::max('sort_order') + 1;
        }

        if ($config['uses_active_flag'] ?? false) {
            $data['is_active'] = true;
        }

        $config['model']::create($data);

        return back()->with('success', $config['label'] . ' created successfully');
    }

    public function editQuotationField($type, $id)
    {
        $config = $this->quotationFieldConfig($type);
        $record = $config['model']::findOrFail($id);

        return response()->json([
            'id' => $record->id,
            'name' => $record->{$config['name_column']},
            'freight_cat_id' => $record->freight_cat_id ?? null,
        ]);
    }

    public function updateQuotationField(Request $request, $type, $id)
    {
        $config = $this->quotationFieldConfig($type);

        $rules = ['name' => 'required|string|max:100'];

        if ($type === 'freight_sub_category') {
            $rules['freight_cat_id'] = 'required|exists:freight_category,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $record = $config['model']::findOrFail($id);
        $record->{$config['name_column']} = $request->name;

        if ($type === 'freight_sub_category') {
            $record->freight_cat_id = $request->freight_cat_id;
        }

        $record->save();

        return back()->with('success', $config['label'] . ' updated successfully');
    }

    public function destroyQuotationField($type, $id)
    {
        $config = $this->quotationFieldConfig($type);
        $config['model']::findOrFail($id)->delete();

        return back()->with('success', $config['label'] . ' deleted successfully');
    }

    private function quotationFieldConfig($type)
    {
        $configs = [
            'freight_category' => [
                'label' => 'Freight/Transportation',
                'model' => FreightCategory::class,
                'name_column' => 'categoty',
            ],
            'freight_sub_category' => [
                'label' => 'Included or Extra',
                'model' => FreightSubCategory::class,
                'name_column' => 'sub_category',
            ],
            'unloading' => [
                'label' => 'Unloading at site',
                'model' => QuotationUnloadingOption::class,
                'name_column' => 'name',
                'uses_sort_order' => true,
                'uses_active_flag' => true,
            ],
            'installation' => [
                'label' => 'Installation Scope',
                'model' => QuotationInstallationScope::class,
                'name_column' => 'name',
                'uses_sort_order' => true,
                'uses_active_flag' => true,
            ],
            'orc' => [
                'label' => 'ORC',
                'model' => QuotationOrcOption::class,
                'name_column' => 'name',
                'uses_sort_order' => true,
                'uses_active_flag' => true,
            ],
            'quotation_status' => [
                'label' => 'Quotation Status',
                'model' => QuotationStatus::class,
                'name_column' => 'name',
            ],
        ];

        abort_unless(isset($configs[$type]), 404);

        return $configs[$type];
    }

    public function getDesignations()
    {
        $designations = DB::table('dropdowndatas')
            ->where('formid', 80)
            ->orderBy('orderno')
            ->get();
        
        return response()->json($designations);
    }

    public function getDepartments()
    {
        $departments = DB::table('dropdowndatas')
            ->where('formid', 81)
            ->orderBy('orderno')
            ->get();
        
        return response()->json($departments);
    }
}
