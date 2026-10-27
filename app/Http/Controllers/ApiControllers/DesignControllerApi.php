<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Lead;
use App\Models\LeadDesign;
use App\Models\LeadProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Quotation;
use App\Models\User;
use App\Models\Design;
use App\Mail\MoveQuotationMail;
use App\Mail\DesignUploadedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;



class DesignControllerApi extends Controller
{


    public function designleadData($userId)
    {
        // dd($userId);
        $leads = Lead::select('leads.*', 'users.name as assignedto', 'dropdowndatas.dropdowndata')
            ->where('Leadstatus', '=', '343')
            ->leftJoin('users', 'leads.assigned_to', '=', 'users.id')
            ->leftJoin('dropdowndatas', 'leads.Leadsource', '=', 'dropdowndatas.id')
            ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
            ->where('product_categories_tb.sub_category', 'special')
            ->where('assigned_to', '=', $userId)
            ->where(function ($query) {
                $query->whereNotIn('leads.quota_proceed', [1, 0])
                    ->orWhereNull('leads.quota_proceed');
            })
            ->orderBy('leads.id', 'desc')
            ->get();
        return ResponseHelper::successResponseData('Success', 'Leads retrieved successfully', $leads);
    }

    public function storedesign(Request $request)
    {

        $request->validate([
            'drawing.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'offer.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $userid = $request->userid;
        $authUser = User::find($userid);
        $authUserEmail = $authUser->email;
        $authUserRole = $authUser->role;
        $leadSalesManagerEmail = null;

        $assigneeto = null;


        if ($authUserRole === 'Design') {

            // Get assigned_to from leads table
            $lead = Lead::find($request->leadiid);
            $assigneeto = $lead?->assigned_to ?? null;
        } else {

            $design = Design::where('idlead', $request->leadiid)
                ->whereNotNull('assigndesign')  // Filter out null values
                ->latest('id')                  // Sort by latest ID
                ->first();
            $assigneeto = $design?->assigndesign ?? null;
        }

        $reverse = ($authUserRole === 'Design' || $userid == $assigneeto) ? 1 : 0;



        $leadId = Lead::find($request->leadiid)?->lead_id ?? 'N/A';
        $productName = Product::find($request->proname)?->productname ?? 'N/A';
        $categoryName = ProductCategory::find($request->catename)?->category_name ?? 'N/A';
        // dd($request);
        $commonData = [
            'leadiid' => $request->leadiid,
            'catename' => $request->catename,
            'proname' => $request->proname,
            'design' => $request->design,
            'assigneeto' => $assigneeto,
            'assignee' => $userid,
            'reverse' => $reverse,
            // 'reverse' => (auth()->id() == ($request->assigneeto ?? null)) ? 1 : 0,
        ];

        $drawingFiles = $request->file('drawing');
        $offerFiles = $request->file('offer');
        $basicsupplies = $request->basicsupply;

        $uploadedFiles = [];

        // If basicsupply is provided (Design Login)
        if ($basicsupplies && is_array($basicsupplies)) {

            foreach ($basicsupplies as $index => $basicsupply) {
                $data = $commonData;

                // Handle Drawing File
                if (isset($drawingFiles[$index])) {
                    $drawingFile = $drawingFiles[$index];
                    $drawingFilename = time() . '_drawing_' . $drawingFile->getClientOriginalName();
                    $drawingFile->move(public_path('assets/images/'), $drawingFilename);
                    $data['drawing'] = 'assets/images/' . $drawingFilename;
                    $uploadedFiles[] = $data['drawing'];
                }

                // Handle Offer File
                if (isset($offerFiles[$index])) {
                    $offerFile = $offerFiles[$index];
                    $offerFilename = time() . '_offer_' . $offerFile->getClientOriginalName();
                    $offerFile->move(public_path('assets/images/'), $offerFilename);
                    $data['offer'] = 'assets/images/' . $offerFilename;
                    $uploadedFiles[] = $data['offer'];
                }

                // Assign Other Inputs
                $data['basicsupply'] = $basicsupply;
                $data['freightvalue'] = $request->freightvalue[$index] ?? null;
                $data['installvalue'] = $request->installvalue[$index] ?? null;
                $data['pricevalue'] = $request->pricevalue[$index] ?? null;

                LeadDesign::create($data);
            }
        }
        // If only GA Drawing is provided (Non-Design Login)
        elseif ($drawingFiles) {

            $leadData = $commonData;
            $drawingFilename = time() . '_drawing_' . $drawingFiles->getClientOriginalName();
            $drawingFiles->move(public_path('assets/images/'), $drawingFilename);
            $leadData['drawing'] = 'assets/images/' . $drawingFilename;
            $uploadedFiles[] = $leadData['drawing'];

            LeadDesign::create($leadData);

            // foreach ($drawingFiles as $index => $data) {
            //     $leadData = $commonData;
            //     $drawingFilename = time() . '_drawing_' . $data->getClientOriginalName();
            //     $data->move(public_path('assets/images/'), $drawingFilename);
            //     $leadData['drawing'] = 'assets/images/' . $drawingFilename;
            //     $uploadedFiles[] = $leadData['drawing'];



            //     LeadDesign::create(["leadiid"=>"14"]);
            // }
        }



        if ($authUserRole === 'Design') {
            $lead = Lead::find($request->leadiid);
            if ($lead && $lead->assigned_to) {
                $salesManager = User::find($lead->assigned_to);
                $leadSalesManagerEmail = $salesManager?->email;
            }
        }

        $mailData = [];

        if ($basicsupplies && is_array($basicsupplies)) {
            foreach ($basicsupplies as $index => $basicsupply) {
                $mailData[] = [
                    'leadiid' => $leadId,           // Actual Lead ID
                    'catename' => $categoryName,    // Category Name
                    'proname' => $productName,      // Product Name
                    'design' => strip_tags($request->design),
                    'assigneeto' => $assigneeto,
                    'assignee' => $userid,
                    'reverse' => $reverse,
                    'basicsupply' => $basicsupply,
                    'freightvalue' => $request->freightvalue[$index] ?? 'N/A',
                    'installvalue' => $request->installvalue[$index] ?? 'N/A',
                    'pricevalue' => $request->pricevalue[$index] ?? 'N/A',
                ];
            }
        } else {
            // For GA drawing without basicsupply
            $mailData[] = [
                'leadiid' => $leadId,
                'catename' => $categoryName,
                'proname' => $productName,
                'design' => strip_tags($request->design),
                'assigneeto' => $assigneeto,
                'assignee' => $userid,
                'reverse' => $reverse,
            ];
        }


        // Send Mail to Auth User (Compulsory)
        Mail::to($authUserEmail)
            ->send(new DesignUploadedMail($mailData, $uploadedFiles));

        // Send Mail to Lead's Sales Manager if Auth User is 'design'
        if ($authUserRole === 'Design' && $leadSalesManagerEmail) {
            Mail::to($leadSalesManagerEmail)
                ->send(new DesignUploadedMail($mailData, $uploadedFiles));
        }

        return ResponseHelper::successResponse("Renewal Meeting added Successfully");
    }


    public function designapproval(Request $request)
    {
        // dd($request);
        // Validate the request
        $request->validate([
            'quota_doc' => 'nullable|file|mimes:jpg,png,pdf,doc,docx|max:2048', // Adjust file types & size as needed
            // 'quota_proceed' => 'required',
            // 'idle' => 'required|exists:leads,id',
        ]);

        DB::beginTransaction();

        try {
            // Handle File Upload
            if ($request->hasFile('quota_doc')) {
                $file = $request->file('quota_doc');
                $filename = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('assets/images/');
                $file->move($destinationPath, $filename);
                $filePath = 'assets/images/' . $filename;
            } else {
                $filePath = null;
            }

            // Update the `leads` table
            Lead::where('id', $request->idle)->update([
                'quota_proceed' => $request->quota_proceed,
                'quota_doc' => $filePath,
            ]);

            // Fetch data from `designs` table where `idle` matches
            $designs = Design::where('idlead', $request->idle)->get();

            $lead = Lead::find($request->idle);
            $leadId = $lead ? $lead->lead_id : 'N/A';

            $products = [];
            $categories = [];
            $quantities = [];

            // Insert data into `quotations` table
            foreach ($designs as $design) {

                $product = Product::find($design->product);
                $category = ProductCategory::find($design->category);

                $products[] = $product ? $product->productname : 'N/A';
                $categories[] = $category ? $category->category_name : 'N/A';
                $quantities[] = $design->quantity;

                Quotation::create([
                    'id_lead' => $design->idlead,
                    'id_customer' => $design->idcustomer,
                    'products' => $design->product,
                    'categorys' => $design->category,
                    'ga_number' => $design->ganumber,
                    'quantitys' => $design->quantity,
                    'quota_id' => 'QUTO/' . date('Y') . '/' . str_pad(Quotation::max('id') + 1, 4, '0', STR_PAD_LEFT)
                ]);
            }

            DB::commit();
            // $authUserEmail = Auth::user()->email;

            // $mailData = [
            //     'leadid' => $leadId,
            //     'products' => $products,
            //     'categorys' => $categories,
            //     'quantitys' => $quantities,
            //     'quota_doc' => $filePath
            // ];

            // Mail::to($authUserEmail)
            //     ->send(new MoveQuotationMail($mailData));

            // return redirect()->route('pages.viewconverted')->with('success', 'Data successfully updated and inserted.');
            return ResponseHelper::successResponse("Data successfully updated and inserted.");
        } catch (\Exception $e) {
            DB::rollBack();
            // return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
            return ResponseHelper::successResponse('Error: ' . $e->getMessage());
        }
    }
}
