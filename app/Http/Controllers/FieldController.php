<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;

class FieldController extends Controller {
    public function FieldIndex() {
        try {
            $category = DB::table( 'fieldcategories' )->get();
            $subcategory = DB::table( 'fieldsubcategories' )->get();
            return view( 'pages.fieldcustomization', compact( 'category', 'subcategory' ) );
        } catch ( \Throwable $th ) {
            Log::error( $th );
        }
    }


    public function savedetails(Request $request)
    {
        $category = $request->category;
        $subcategory = $request->subcategory;
        $fieldname= $request->fieldname;
        $fieldtype = $request->fieldtype;
        $mandatory = $request->mandatory;

        $dataentry = str_replace(' ', '', $fieldname);

        $duplicatecheck = DB::table('fields')
        ->select('*')
        ->where('fieldnamewithoutspace','=',$dataentry)
        ->where('categoryid','=',$category)
        ->where('subcategoryid','=',$subcategory)
        ->get();

        if($duplicatecheck->count() == 0)
        {
            $id = DB::table('fields')
                ->insertGetId([
                "categoryid"=>$category,
                "subcategoryid"=>$subcategory,
                "fieldname"=>$fieldname,
                "fieldtype"=>$fieldtype,
                "mandatory"=>$mandatory,
                "fieldnamewithoutspace"=>$dataentry
            ]);

            $orderno = $request->orderno;


            if($fieldtype == 7)
            {
                foreach($orderno as $key => $or)
                {
                    $ordern = $request->orderno[$key];
                    $optionname = $request->optionname[$key];

                    DB::table('dropdowndatas')
                    ->insert([
                        "formid"=>$id,
                        "dropdowndata"=>$optionname,
                        "orderno"=>$ordern,
                    ]);
                }
            }

            if($category == 1)
            {
                $dataentry = str_replace(' ', '', $fieldname);
                Schema::table('leads', function (Blueprint $table) use ($dataentry) {
                    $table->string($dataentry)->nullable();
                });
            }

            if($category == 2)
            {
                $dataentry = str_replace(' ', '', $fieldname);
                Schema::table('products', function (Blueprint $table) use ($dataentry) {
                    $table->string($dataentry)->nullable();
                });
            }

            if($category == 3)
            {
                $dataentry = str_replace(' ', '', $fieldname);
                Schema::table('enquiry', function (Blueprint $table) use ($dataentry) {
                    $table->string($dataentry)->nullable();
                });
            }

            if($category == 4)
            {
                $dataentry = str_replace(' ', '', $fieldname);
                Schema::table('designs', function (Blueprint $table) use ($dataentry) {
                    $table->string($dataentry)->nullable();
                });
            }

            return back()->with('message','Field Created Successfully..');
        }
        else
        {
            return back()->with('message','Already Field Created..');
        }
    }

    public function viewForm(){
        try {
            $formcategorie = DB::table('fieldcategories')->get();
            // ->where('id', '1')
            return view('pages.viewform',compact('formcategorie'));
        } catch (\Throwable $th) {
            Log::error( $th );
        }
    }

    public function updatestatus(Request $request)
    {
        $formstatus = $request->formstatus;
        $formid = $request->formid;

        DB::table('fields')
        ->where('id',$formid)
        ->update([
            "visibility"=>$formstatus,
            "singlepageview"=>$formstatus
        ]);

        return back()->with('message','Status Changed');
    }


    public function editForm($id){
        try {
            $row = Field::find($id);
            $category = DB::table('fieldcategories')->get();
            $subcategory = DB::table('fieldsubcategories')->get();
            return view('pages.editform',compact('row','category','subcategory'));
        } catch (\Throwable $th) {
            Log::error( $th );
        }
    }


    public function updateformdetails(Request $request)
    {
        $category = $request->category;
        $subcategory = $request->subcategory;
        $fieldname= $request->fieldname;
        $fieldtype = $request->fieldtype;
        $mandatory = $request->mandatory;
        $formid = $request->id;


        $id = DB::table('fields')
        ->where('id','=',$formid)
        ->update([
            "categoryid"=>$category,
            "subcategoryid"=>$subcategory,
            "fieldname"=>$fieldname,
            "fieldtype"=>$fieldtype,
            "mandatory"=>$mandatory
        ]);

        // DB::table('dropdowndatas')
        // ->select('*')
        // ->where('formid','=',$formid)
        // ->delete();

        // $orderno = $request->orderno;

        if ($fieldtype == 7) {
            // **Step 1: Get existing order numbers for the form**
            $existingOrderNumbers = DB::table('dropdowndatas')
                ->where('formid', $formid)
                ->pluck('orderno')
                ->toArray();

            // **Step 2: Collect new order numbers from request**
            $newOrderNumbers = $request->orderno ?? [];

            // **Step 3: Find records that need to be deleted**
            $orderNumbersToDelete = array_diff($existingOrderNumbers, $newOrderNumbers);

            // **Step 4: Delete records that are missing in the new request**
            if (!empty($orderNumbersToDelete)) {
                DB::table('dropdowndatas')
                    ->where('formid', $formid)
                    ->whereIn('orderno', $orderNumbersToDelete)
                    ->delete();
            }

            // **Step 5: Insert or update remaining records**
            foreach ($request->orderno as $key => $ordern) {
                $optionname = $request->optionname[$key];
                $mhe_rack = $request->mhe_rack[$key];
                $pro_catid = $request->pro_catid[$key];

                $existingData = DB::table('dropdowndatas')
                    ->where('formid', $formid)
                    ->where('orderno', $ordern)
                    ->first();

                if ($existingData) {
                    // Update existing record
                    DB::table('dropdowndatas')
                        ->where('id', $existingData->id)
                        ->update([
                            "dropdowndata" => $optionname,
                            "mhe_rack" => $mhe_rack,
                            "pro_catid" => $pro_catid,
                        ]);
                } else {
                    // Insert new record
                    DB::table('dropdowndatas')->insert([
                        "formid" => $formid,
                        "orderno" => $ordern,
                        "dropdowndata" => $optionname,
                        "mhe_rack" => $mhe_rack,
                        "pro_catid" => $pro_catid,
                    ]);
                }
            }
        }
        // if($fieldtype == 7)
        // {
        //     foreach($orderno as $key => $or)
        //     {
        //         $ordern = $request->orderno[$key];
        //         $optionname = $request->optionname[$key];

        //         DB::table('dropdowndatas')
        //         ->insert([
        //             "formid"=>$formid,
        //             "dropdowndata"=>$optionname,
        //             "orderno"=>$ordern,
        //         ]);
        //     }
        // }

        return back()->with('message','Form Updated Successfully...');
    }


    public function deletecolumn($id,$categoryid,$subcatid,$formtext)
    {


        DB::table('fields')
        ->where('fieldnamewithoutspace','=',$formtext)
        ->where('categoryid','=',$categoryid)
        ->delete();

        if($categoryid == 1)
        {
            if (Schema::hasColumn('leads', $formtext)) {
                Schema::table('enquiry', function ($table) use ($formtext) {
                    $table->dropColumn($formtext);
                });
            }
        }

        if($categoryid == 2)
        {
            if (Schema::hasColumn('products', $formtext)) {
                Schema::table('products', function ($table) use ($formtext) {
                    $table->dropColumn($formtext);
                });
            }
        }

        if($categoryid == 3)
        {
            if (Schema::hasColumn('renewals', $formtext)) {
                Schema::table('renewals', function ($table) use ($formtext) {
                    $table->dropColumn($formtext);
                });
            }
        }

        if($categoryid == 4)
        {
            if (Schema::hasColumn('designs', $formtext)) {
                Schema::table('designs', function ($table) use ($formtext) {
                    $table->dropColumn($formtext);
                });
            }
        }



    }
}
