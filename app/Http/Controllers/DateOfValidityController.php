<?php

namespace App\Http\Controllers;

use App\Models\DateOfValidity;
use Illuminate\Http\Request;

class DateOfValidityController extends Controller
{
    //
    public function dovvv()
    {
        $dovs = DateOfValidity::latest()->get();
        return view('pages.dov', compact('dovs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|max:50'
        ]);

        DateOfValidity::create([
            'value' => $request->value
        ]);

        return back()->with('success', 'Added successfully');
    }

    public function edit($id)
    {
        return DateOfValidity::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'value' => 'required|max:50'
        ]);

        DateOfValidity::where('id', $id)->update([
            'value' => $request->value
        ]);

        return back()->with('success', 'Updated successfully');
    }

    public function delete($id)
    {
        DateOfValidity::where('id', $id)->delete();
        return back()->with('success', 'Deleted successfully');
    }
}
