<?php

namespace App\Http\Controllers;

use App\DataTables\OrfappDataTable;
use App\DataTables\OrfDataTable;
use App\Mail\CsorfStatusMail;
use App\Mail\OrfCreatedMail;
use App\Mail\OrfStatusMail;
use App\Models\Lead;
use App\Models\Orf;
use App\Models\Quotation;
use App\Models\QuotationProduct;
use App\Models\User;
use App\Models\Customer;
use App\Models\OrfAttachment;
use App\Services\QuotationDocumentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrfController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'placeofsupply' => 'required|string',
            'shippingcountry' => 'required',
            'shistate_id' => 'required',
            'shcitylist' => 'required',
            'shippostalcode' => 'required',
            'cusporef' => 'required|string',
            'podate' => 'required|date',
        ]);

        // Convert date format from DD-MM-YYYY to YYYY-MM-DD
        $quotadate = Carbon::createFromFormat('d-m-Y', $request->quotadate)->format('Y-m-d');
        $orfdate = $request->orfdate ? Carbon::parse($request->orfdate)->format('Y-m-d') : null;
        $podate = $request->podate ? Carbon::parse($request->podate)->format('Y-m-d') : null;
        $crddate = $request->crddate ? Carbon::parse($request->crddate)->format('Y-m-d') : null;

        // Handle file uploads and store in public/assets/images/
        // $attcuspoPath = null;
        // $sigdrawPath = null;

        // if ($request->hasFile('attcuspo')) {
        //     $file = $request->file('attcuspo');
        //     $filename = time() . '_attcuspo_' . $file->getClientOriginalName();
        //     $destinationPath = public_path('assets/images/');
        //     $file->move($destinationPath, $filename);
        //     $attcuspoPath = 'assets/images/' . $filename;
        // }

        // if ($request->hasFile('sigdraw')) {
        //     $file = $request->file('sigdraw');
        //     $filename = time() . '_sigdraw_' . $file->getClientOriginalName();
        //     $destinationPath = public_path('assets/images/');
        //     $file->move($destinationPath, $filename);
        //     $sigdrawPath = 'assets/images/' . $filename;
        // }
        $attcuspoFiles = [];
$sigdrawFiles  = [];

/* ---------- CUSTOMER PO FILES ---------- */
if ($request->hasFile('attcuspo')) {
    foreach ($request->file('attcuspo') as $file) {
        $filename = time().'_attcuspo_'.uniqid().'_'.$file->getClientOriginalName();
        $path = $file->move(public_path('assets/images/'), $filename);

        $attcuspoFiles[] = 'assets/images/'.$filename;
    }
}

/* ---------- SIGNED DRAWING FILES ---------- */
if ($request->hasFile('sigdraw')) {
    foreach ($request->file('sigdraw') as $file) {
        $filename = time().'_sigdraw_'.uniqid().'_'.$file->getClientOriginalName();
        $path = $file->move(public_path('assets/images/'), $filename);

        $sigdrawFiles[] = 'assets/images/'.$filename;
    }
}


        // Insert data into the ORF table
        $orf = Orf::create([
            'quoproid' => $request->quoproid,
            'leano' => $request->leano,
            'tranref' => $request->tranref,
            'quono' => $request->quono,
            'custo' => $request->custo,
            'quotadate' => $quotadate,
            'orfref' => $request->orfref,
            'orfdate' => $orfdate,
            'cusporef' => $request->cusporef,
            'podate' => $podate,
            // 'attcuspo' => $attcuspoPath,
            // 'sigdraw' => $sigdrawPath,
            'crddate' => $crddate,
            'specialins' => $request->specialins,
            'specialproduct' => $request->special_product_id ?? null,
        ]);


        foreach ($attcuspoFiles as $file) {
    DB::table('orf_attachments')->insert([
        'orf_id' => $orf->id,
        'type' => 'attcuspo',
        'file_path' => $file,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

foreach ($sigdrawFiles as $file) {
    DB::table('orf_attachments')->insert([
        'orf_id' => $orf->id,
        'type' => 'sigdraw',
        'file_path' => $file,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}


        // Update the quotationproduct table
        DB::table('quotationproduct')
            ->where('id', $request->quoproid)  // Use quoproid as the ID reference
            ->update([
                'placeofsupply' => $request->placeofsupply,
                'shippostalcode' => $request->shippostalcode,
                'shcitylist' => $request->shcitylist,
                'shistate_id' => $request->shistate_id,
                'shippingcountry' => $request->shippingcountry,
                'gstnum' => $request->gstnum,
                'updated_at' => now(),
            ]);

        $superAdminEmails = DB::table('users')
            ->where('role', 'Super Admin')
            ->pluck('email')
            ->toArray();

        $csEmails = DB::table('users')
            ->where('role', 'Cs')
            ->pluck('email')
            ->toArray();

        $recipients = array_merge($superAdminEmails, $csEmails);

        $attachments = OrfAttachment::where('orf_id', $orf->id)->get();

$mailData = [
    'orf_ref' => $request->orfref,
    'quotation_no' => $request->quono,
    'customer' => $request->custo,
    'orf_date' => $orfdate,
    'po_date' => $podate,
    'special_instructions' => $request->specialins,
    'attachments' => $attachments
];


        // ✅ Prepare mail data
        // $mailData = [
        //     'orf_ref' => $request->orfref,
        //     'quotation_no' => $request->quono,
        //     'customer' => $request->custo,
        //     'orf_date' => $orfdate,
        //     'po_date' => $podate,
        //     'special_instructions' => $request->specialins,
        //     'attcuspo' => $attcuspoPath,
        //     'sigdraw' => $sigdrawPath,
        // ];

        // ✅ Send mail
        try {
    Mail::to($recipients)
        ->send(new OrfCreatedMail($mailData));

    return redirect()->route('pages.vieworf')
        ->with('success', 'ORF added successfully and mail sent!');

} catch (\Exception $e) {

    Log::error('Mail sending failed: ' . $e->getMessage());

    return redirect()->route('pages.vieworf')
        ->with('error', 'ORF added but failed to send mail.');
}

        // try {
        //     Mail::to($recipients)
        //         ->send(new OrfCreatedMail($mailData));

        //     return redirect()->route('single.quota', ['leadid' => $request->leano])
        //         ->with('success', 'ORF added successfully and mail sent!');
        // } catch (Exception $e) {
        //     Log::error('Mail sending failed: ' . $e->getMessage());
        //     return redirect()->route('single.quota', ['leadid' => $request->leano])
        //         ->with('error', 'ORF added but failed to send mail.');
        // }
    }

    public function vieworf(OrfDataTable $dataTable)
    {
        return $dataTable->render('pages.vieworf');
    }
    public function vieworfapp(OrfappDataTable $dataTable)
    {
        return $dataTable->render('pages.vieworfapp');
    }

    public function insertStatusorf(Request $request)
    {
        // Find the lead
        $lead = orf::find($request->orfid);

        if ($lead) {
            // Update lead details
            $lead->approval_status = $request->approval_status ?? $lead->approval_status;
            $lead->cs_status = $request->cssta ?? $lead->cs_status;

            if ($request->approval_status == '2') {
                $lead->reject_remark = $request->reject_remark;
            } else {
                $lead->reject_remark = null;  // Clear it if approval is "Yes"
            }

            $lead->save();
            $this->handleOrderApprovalConversion($lead);

            $authUser = auth()->user();  // Authenticated user
            $leadRecord = Lead::where('id', $lead->leano)->first();  // Get lead record
            $assignedUser = User::find($leadRecord->assigned_to);    // Get assigned user

            $statusText = $lead->approval_status == '1' ? 'approved' : 'rejected';
            $subject = "ORF {$statusText} Notification";

            // Email content
            $data = [
                'orf_id' => $lead->orfref,
                'status' => $statusText,
                'reject_remark' => $lead->reject_remark,
                'auth_user' => $authUser->name,
            ];

            // Send email to the authenticated user
            Mail::to($authUser->email)->send(new OrfStatusMail($data));

            // Send email to the assigned user
            if ($assignedUser) {
                Mail::to($assignedUser->email)->send(new OrfStatusMail($data));
            }

            return response()->json(['success' => true, 'message' => 'ORF Approval updated and email sent successfully']);
        }

        return response()->json(['success' => false, 'message' => 'ORF not found']);
    }

    public function updatecsStatusorf(Request $request)
    {
        // Find the lead
        $lead = orf::find($request->orfid);

        if ($lead) {
            // Update lead details
            $lead->cs_status = $request->cs_status ?? $lead->cs_status;
            $lead->dispatchdate = $request->dispatchdate;
            $lead->approval_status = $request->appsta ?? $lead->approval_status;

            if ($request->cs_status == '2') {
                $lead->csreject_remark = $request->csreject_remark;
            } else {
                $lead->csreject_remark = null;  // Clear it if approval is "Yes"
            }

            $lead->save();
            $this->handleOrderApprovalConversion($lead);


            $authUsers = auth()->user();
            $leadRecords = Lead::where('id', $lead->leano)->first();
            $assignedUsers = User::find($leadRecords->assigned_to);

            $statusText = $lead->cs_status == '1' ? 'approved' : 'rejected';

            $data = [
                'orf_id' => $lead->orfref,
                'status' => $statusText,
                'dispatchdate' => $lead->dispatchdate,
                'csreject_remark' => $lead->csreject_remark,
                'auth_user' => $authUsers->name,
            ];

            try {
                // Send email to the authenticated user
                Mail::to($authUsers->email)->send(new CsorfStatusMail($data));

                // Send email to the assigned user
                if ($assignedUsers) {
                    Mail::to($assignedUsers->email)->send(new CsorfStatusMail($data));
                }

                return response()->json(['success' => true, 'message' => 'ORF Approval updated and email sent successfully']);
            } catch (\Exception $e) {
                Log::error('Email sending failed: ' . $e->getMessage());

                return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => false, 'message' => 'ORF not found']);
    }

    // public function vieworfinvoice($id)
    // {
    //     $quotation = QuotationProduct::with(['orfDetails','lead.assignedTo', 'productDetails','freight.freightDetail.category','freight.termsCondition'])
    //         ->where('id', $id)
    //         ->firstOrFail();
    //     // dd($quotation->toArray());
    //     $quotationCount = QuotationProduct::where('leadno', $quotation->leadno)->count();
    //     return view('quotations.orfinvoice', compact('quotation','quotationCount'));
    // }
    public function vieworfinvoice($id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) {
            // Check if $id is an ORF record ID
            $orfRecord = \App\Models\Orf::find($id);
            if ($orfRecord) {
                $quotation = Quotation::find($orfRecord->quoproid) 
                    ?? Quotation::where('id_lead', $orfRecord->leano)->latest('id')->first();
            }
        }

        if (!$quotation) {
            abort(404, 'Quotation or ORF document not found.');
        }

        $data = app(QuotationDocumentService::class)->build((int) $quotation->id);
        $data['orf'] = \App\Models\Orf::where('quoproid', $quotation->id)->first() 
            ?? \App\Models\Orf::where('leano', $data['quotation']->id_lead)->latest('id')->first();

        return view('quotations.orfinvoice', $data);
    }



    public function edit($id)
{
    // $orf = Orf::findOrFail($id);
    $orf = Orf::with('attachments')->findOrFail($id);
    return view('pages.editorf', compact('orf'));
}

// public function update(Request $request, $id)
// {
//     $orf = Orf::findOrFail($id);

//     $request->validate([
//         'quotadate' => 'required|date_format:d-m-Y',
//         'orfdate' => 'nullable|date',
//         'podate' => 'nullable|date',
//         'crddate' => 'nullable|date',
//         'specialins' => 'nullable|string',
//         'attcuspo' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
//         'sigdraw' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
//     ]);

//     // Format dates
//     $quotadate = Carbon::createFromFormat('d-m-Y', $request->quotadate)->format('Y-m-d');
//     $orfdate = $request->orfdate ? Carbon::parse($request->orfdate)->format('Y-m-d') : null;
//     $podate = $request->podate ? Carbon::parse($request->podate)->format('Y-m-d') : null;
//     $crddate = $request->crddate ? Carbon::parse($request->crddate)->format('Y-m-d') : null;

//     // File upload paths
//     if ($request->hasFile('attcuspo')) {
//         $file = $request->file('attcuspo');
//         $filename = time() . '_attcuspo_' . $file->getClientOriginalName();
//         $file->move(public_path('assets/images/'), $filename);
//         $orf->attcuspo = 'assets/images/' . $filename;
//     }

//     if ($request->hasFile('sigdraw')) {
//         $file = $request->file('sigdraw');
//         $filename = time() . '_sigdraw_' . $file->getClientOriginalName();
//         $file->move(public_path('assets/images/'), $filename);
//         $orf->sigdraw = 'assets/images/' . $filename;
//     }

//     // Update ORF fields
//     $orf->quoproid = $request->quoproid;
//     $orf->leano = $request->leano;
//     $orf->tranref = $request->tranref;
//     $orf->quono = $request->quono;
//     $orf->custo = $request->custo;
//     $orf->quotadate = $quotadate;
//     $orf->orfref = $request->orfref;
//     $orf->orfdate = $orfdate;
//     $orf->cusporef = $request->cusporef;
//     $orf->podate = $podate;
//     $orf->crddate = $crddate;
//     $orf->specialins = $request->specialins;
//     $orf->save();

//     // Update quotationproduct table
//     DB::table('quotationproduct')
//         ->where('id', $request->quoproid)
//         ->update([
//             'placeofsupply' => $request->placeofsupply,
//             'shippostalcode' => $request->shippostalcode,
//             'shcitylist' => $request->shcitylist,
//             'shistate_id' => $request->shistate_id,
//             'shippingcountry' => $request->shippingcountry,
//             'gstnum' => $request->gstnum,
//             'updated_at' => now(),
//         ]);

//     return redirect()->route('pages.vieworf', $orf->id)->with('success', 'ORF updated successfully!');
// }

public function update(Request $request, $id)
{
    $orf = Orf::findOrFail($id);

    /* -------------------------------------------------
     | VALIDATION
     ------------------------------------------------- */
    $request->validate([
        'quotadate'   => 'required|date_format:d-m-Y',
        'orfdate'     => 'nullable|date',
        'podate'      => 'nullable|date',
        'crddate'     => 'nullable|date',
        'specialins'  => 'nullable|string',

        // new files
        'attcuspo.*'  => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        'sigdraw.*'   => 'nullable|file|mimes:pdf,jpg,jpeg,png',

        // replacement files
        'replace_attcuspo.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        'replace_sigdraw.*'  => 'nullable|file|mimes:pdf,jpg,jpeg,png',
    ]);

    /* -------------------------------------------------
     | DATE FORMATTING
     ------------------------------------------------- */
    $quotadate = Carbon::createFromFormat('d-m-Y', $request->quotadate)->format('Y-m-d');
    $orfdate   = $request->orfdate ? Carbon::parse($request->orfdate)->format('Y-m-d') : null;
    $podate    = $request->podate ? Carbon::parse($request->podate)->format('Y-m-d') : null;
    $crddate   = $request->crddate ? Carbon::parse($request->crddate)->format('Y-m-d') : null;

    /* -------------------------------------------------
     | UPDATE ORF BASIC DETAILS
     ------------------------------------------------- */
    $orf->update([
        'quoproid'    => $request->quoproid,
        'leano'       => $request->leano,
        'tranref'     => $request->tranref,
        'quono'       => $request->quono,
        'custo'       => $request->custo,
        'quotadate'   => $quotadate,
        'orfref'      => $request->orfref,
        'orfdate'     => $orfdate,
        'cusporef'    => $request->cusporef,
        'podate'      => $podate,
        'crddate'     => $crddate,
        'cs_status'   => 0,
        'approval_status' =>0,
        'specialins'  => $request->specialins,
    ]);

    /* -------------------------------------------------
     | REPLACE EXISTING CUSTOMER PO FILES
     ------------------------------------------------- */
    if ($request->hasFile('replace_attcuspo')) {
        foreach ($request->file('replace_attcuspo') as $attachmentId => $file) {

            $attachment = OrfAttachment::find($attachmentId);
            if (!$attachment) continue;

            // delete old file
            if (file_exists(public_path($attachment->file_path))) {
                unlink(public_path($attachment->file_path));
            }

            $filename = time().'_attcuspo_'.uniqid().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/images'), $filename);

            $attachment->update([
                'file_path' => 'assets/images/'.$filename
            ]);
        }
    }

    /* -------------------------------------------------
     | REPLACE EXISTING SIGNED DRAWING FILES
     ------------------------------------------------- */
    if ($request->hasFile('replace_sigdraw')) {
        foreach ($request->file('replace_sigdraw') as $attachmentId => $file) {

            $attachment = OrfAttachment::find($attachmentId);
            if (!$attachment) continue;

            if (file_exists(public_path($attachment->file_path))) {
                unlink(public_path($attachment->file_path));
            }

            $filename = time().'_sigdraw_'.uniqid().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/images'), $filename);

            $attachment->update([
                'file_path' => 'assets/images/'.$filename
            ]);
        }
    }

    /* -------------------------------------------------
     | ADD NEW CUSTOMER PO FILES
     ------------------------------------------------- */
    if ($request->hasFile('attcuspo')) {
        foreach ($request->file('attcuspo') as $file) {

            $filename = time().'_attcuspo_'.uniqid().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/images'), $filename);

            OrfAttachment::create([
                'orf_id'    => $orf->id,
                'type'      => 'attcuspo',
                'file_path'=> 'assets/images/'.$filename,
            ]);
        }
    }

    /* -------------------------------------------------
     | ADD NEW SIGNED DRAWING FILES
     ------------------------------------------------- */
    if ($request->hasFile('sigdraw')) {
        foreach ($request->file('sigdraw') as $file) {

            $filename = time().'_sigdraw_'.uniqid().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/images'), $filename);

            OrfAttachment::create([
                'orf_id'    => $orf->id,
                'type'      => 'sigdraw',
                'file_path'=> 'assets/images/'.$filename,
            ]);
        }
    }

    /* -------------------------------------------------
     | UPDATE QUOTATION PRODUCT
     ------------------------------------------------- */
    DB::table('quotationproduct')
        ->where('id', $request->quoproid)
        ->update([
            'placeofsupply'   => $request->placeofsupply,
            'shippostalcode'  => $request->shippostalcode,
            'shcitylist'      => $request->shcitylist,
            'shistate_id'     => $request->shistate_id,
            'shippingcountry' => $request->shippingcountry,
            'gstnum'          => $request->gstnum,
            'updated_at'      => now(),
        ]);

    return redirect()
        ->route('pages.vieworf')
        ->with('success', 'ORF updated successfully!');
}


public function getAttachments($orfId, $type)
{
    $files = OrfAttachment::where('orf_id', $orfId)
        ->where('type', $type)
        ->get();

    return response()->json($files);
}
public function deleteAttachment($id)
{
    $file = OrfAttachment::findOrFail($id);

    if (file_exists(public_path($file->file_path))) {
        unlink(public_path($file->file_path));
    }

    $file->delete();

    return response()->json(['success' => true]);
}

    /**
     * When an order is approved by both CS and Sales:
     * 1. Ensure orf po_status is initialized to 'pending'
     * 2. Automatically update Lead status to Converted (343) and set converteddate
     * 3. Sync or create customer record
     */
    protected function handleOrderApprovalConversion(Orf $orf)
    {
        if ($orf->approval_status == '1' && $orf->cs_status == '1') {
            // 1. Ensure po_status is pending if not already released
            if (empty($orf->po_status) || $orf->po_status !== 'released') {
                $orf->po_status = 'pending';
                $orf->save();
            }

            // 2. Auto-convert lead to status 343 (Converted)
            $lead = Lead::find($orf->leano);
            if ($lead) {
                $lead->Leadstatus = 343; // Converted
                if (empty($lead->converteddate)) {
                    $lead->converteddate = now();
                }
                $lead->save();

                // 3. Ensure Customer record exists
                if (!empty($lead->phonumber)) {
                    $existingCustomer = Customer::where('contact_no', $lead->phonumber)->first();
                    if (!$existingCustomer) {
                        $customer = new Customer();
                        $customer->customername = $lead->LeadName;
                        $customer->contact_name = $lead->contactname;
                        $customer->cus_email = $lead->email;
                        $customer->country_code = "76";
                        $customer->contact_no = $lead->phonumber;
                        $customer->department = $lead->department;
                        $customer->designation = $lead->designation;
                        $customer->address = $lead->billaddress;
                        $customer->country = $lead->billingcountry;
                        $customer->city = $lead->citylist;
                        $customer->state = $lead->state_id;
                        $customer->postalcode = $lead->postalcode;
                        $customer->gstno = $lead->gst;
                        $customer->save();

                        $customer->cusid = 'CUS' . str_pad($customer->id, 4, '0', STR_PAD_LEFT);
                        $customer->save();

                        $lead->customeriid = $customer->cusid;
                        $lead->save();
                    } elseif (empty($lead->customeriid)) {
                        $lead->customeriid = $existingCustomer->cusid;
                        $lead->save();
                    }
                }
            }
        }
    }

}
