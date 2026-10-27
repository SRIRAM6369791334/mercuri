<?php

namespace App\Http\Controllers;

use App\Models\DesignSpecificationMaster;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class DesignSpecificationMasterController extends Controller
{

    public function create()
    {
        // $categories = ProductCategory::where('sub_category', 'special')
        //     ->whereIn('type', ['mhe', 'rack', 'pallet'])
        //     ->get();

        // return view('pages.specifications', compact('categories'));
        $categories = ProductCategory::where('sub_category', 'special')
        ->whereIn('type', ['mhe', 'rack', 'pallet'])
        ->get();

    $specs = DesignSpecificationMaster::with('category')
        ->orderBy('category_id')
        ->orderBy('type')
        ->orderBy('spec_name')
        ->get()
        ->groupBy(['category_id', 'type']);

    return view('pages.specifications', compact('categories', 'specs'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'type'        => 'required|in:mhe,rack,pallet',
            'spec_name'   => 'required|array',
            'spec_name.*' => 'required|string'
        ]);

        foreach ($request->spec_name as $spec) {
            DesignSpecificationMaster::create([
                'category_id' => $request->category_id,
                'type'        => $request->type,
                'spec_name'   => $spec,
            ]);
        }

        return back()->with('success', 'Specifications added successfully');
    }

    public function edit($id)
    {
        $spec = DesignSpecificationMaster::with('category')->findOrFail($id);

        return view('pages.design_specification_master_edit', compact('spec'));
    }

public function update(Request $request, $id)
{
    $request->validate([
        'spec_name' => 'required|string'
    ]);

    $spec = DesignSpecificationMaster::findOrFail($id);
    $spec->update([
        'spec_name' => $request->spec_name
    ]);

    return redirect()
        ->route('design.spec.master.create')
        ->with('success', 'Specification updated successfully');
}

public function destroy($id)
{
    DesignSpecificationMaster::findOrFail($id)->delete();

    return back()->with('success', 'Specification deleted successfully');
}

}
