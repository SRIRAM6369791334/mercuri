<?php

namespace App\Http\Controllers;

use App\DataTables\ConvertedDataTable;
use App\DataTables\LeadDataTable;
use App\DataTables\LostDataTable;
use App\DataTables\QuotationDataTable;
use App\Mail\DesignUploadedMail;
use App\Mail\LeadCreatedMail;
use App\Mail\MoveQuotationMail;
use App\Mail\MoveQutoMail;
use App\Mail\QuotationApprovedMail;
use App\Mail\QuotationCreatedMail;
use App\Mail\QuotationMail;
use App\Services\QuotationDocumentService;
use App\Models\AdminSetting;
use App\Models\Country;
use App\Models\Customer;
use App\Models\DateOfValidity;
use App\Models\Design;
use App\Models\DesignInput;
use App\Models\DesignDrawing;
use App\Models\DesignSpecification;
use App\Models\DesignSpecificationMaster;
use App\Models\Dropdowndata;
use App\Models\Field;
use App\Models\Freight;
use App\Models\FreightCategory;
use App\Models\FreightDetail;
use App\Models\FreightSubCategory;
use App\Models\Lead;
use App\Models\LeadDesign;
use App\Models\LeadProduct;
use App\Models\Mailsetting;
use App\Models\Orf;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\Quotation;
use App\Models\QuotationInstallationScope;
use App\Models\QuotationOrcOption;
use App\Models\QuotationProduct;
use App\Models\QuotationStatus;
use App\Models\QuotationUnloadingOption;
use App\Models\Term;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private function validateEnquiryDetail(Request $request): void
    {
        if ($request->filled('enquirydetail')) {
            $request->validate([
                'enquirydetail' => [
                    'nullable',
                    'string',
                    'max:100',
                ],
            ]);
        }
    }

    private function getSegmentTerms(?int $categoryId, int $termType)
    {
        return Term::query()
            ->where('term_approve', 0)
            ->where('whichterm', 1)
            ->where('termtype', $termType)
            ->where(function ($query) use ($categoryId) {
                if ($categoryId) {
                    $query->where('product_category_id', $categoryId)
                        ->orWhereNull('product_category_id');

                    return;
                }

                $query->whereNull('product_category_id');
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->orderByRaw('CASE WHEN product_category_id = ? THEN 0 ELSE 1 END', [$categoryId]);
            })
            ->orderBy('id');
    }

    private function appendCurrentTerm(Collection $terms, ?int $termId): Collection
    {
        if ($termId && !$terms->contains('id', $termId)) {
            $currentTerm = Term::find($termId);
            if ($currentTerm) {
                $terms->push($currentTerm);
            }
        }

        return $this->deduplicateTerms($terms, $termId);
    }

    private function deduplicateTerms(Collection $terms, ?int $selectedTermId = null): Collection
    {
        $grouped = $terms->groupBy(function ($item) {
            return strtolower(str_replace([' ', ';'], ['', ''], $item->content ?? ''));
        });

        $uniqueTerms = new Collection();

        foreach ($grouped as $group) {
            if ($group->count() === 1) {
                $uniqueTerms->push($group->first());
            } else {
                $chosen = $group->firstWhere('id', $selectedTermId)
                    ?? $group->firstWhere('term_approve', 0)
                    ?? $group->first();
                $uniqueTerms->push($chosen);
            }
        }

        return $uniqueTerms->values();
    }

    private function resolveSegmentTermId(Lead $lead, $termId, int $termType, string $fieldName): int
    {
        if ($termId === 'new') {
            $contentField = $termType === 1 ? 'newtermscondition' : 'newwarcondition';
            $contentLabel = $termType === 1 ? 'payment term' : 'warranty term';
            $content = trim(preg_replace('/\s+/', ' ', (string) request($contentField)));

            if ($content === '') {
                throw ValidationException::withMessages([
                    $contentField => "Please enter the new {$contentLabel}.",
                ]);
            }

            // Check if matching term already exists for this segment or global
            $normalizedSearch = strtolower(str_replace([' ', ';'], ['', ''], $content));
            $existingTerm = Term::where('whichterm', 1)
                ->where('termtype', $termType)
                ->where(function ($query) use ($lead) {
                    if ($lead->category) {
                        $query->where('product_category_id', $lead->category)
                            ->orWhereNull('product_category_id');
                    } else {
                        $query->whereNull('product_category_id');
                    }
                })
                ->where(function ($q) use ($content, $normalizedSearch) {
                    $q->whereRaw('TRIM(LOWER(content)) = ?', [strtolower($content)])
                      ->orWhereRaw('TRIM(LOWER(REPLACE(REPLACE(content, " ", ""), ";", ""))) = ?', [$normalizedSearch]);
                })
                ->orderByRaw('CASE WHEN product_category_id = ? THEN 0 ELSE 1 END', [$lead->category ?: 0])
                ->orderBy('term_approve', 'asc')
                ->first();

            if ($existingTerm) {
                return (int) $existingTerm->id;
            }

            return (int) Term::create([
                'whichterm' => 1,
                'termtype' => $termType,
                'content' => $content,
                'product_category_id' => $lead->category,
                'term_approve' => 1,
            ])->id;
        }

        // 1. Try to find in standard segment terms
        $term = $this->getSegmentTerms($lead->category, $termType)
            ->where('id', $termId)
            ->first();

        // 2. Fallback: if not in standard segment terms (e.g. custom terms with term_approve=1 or pre-existing terms), check Term table
        if (!$term) {
            $term = Term::where('id', $termId)
                ->where(function ($query) use ($termType) {
                    $query->where('termtype', $termType)
                        ->orWhereNull('termtype');
                })
                ->first();
        }

        // 3. Fallback: check by ID in terms table regardless
        if (!$term) {
            $term = Term::find($termId);
        }

        if (!$term) {
            throw ValidationException::withMessages([
                $fieldName => 'Selected term is not allowed for this segment.',
            ]);
        }

        return (int) $term->id;
    }

    private function categoryRequiresDesign(?ProductCategory $category): bool
    {
        if (!$category) {
            return false;
        }

        return $category->sub_category === 'special';
    }

    private function resolveCategoryDesignAssignee(?ProductCategory $category, $requestedAssignee = null): ?int
    {
        if ($requestedAssignee) {
            return (int) $requestedAssignee;
        }

        if (!$category) {
            return null;
        }

        $assignSetting = \App\Models\AssignSetting::first();

        return match ($category->type) {
            'mhe' => $assignSetting?->mhe_special_user_id,
            'rack' => $assignSetting?->racking_user_id,
            'pallet' => $assignSetting?->pallet_user_id,
            default => null,
        };
    }


    // public function saveleads(Request $request)
    // {

    //     $data = $request->except('_token', 'newCustomerName');

    //     if ($request->LeadName === 'new' && $request->newCustomerName) {
    //         // Store the entered new customer name
    //         $data['LeadName'] = $request->newCustomerName;
    //     } else {
    //         // Store the selected existing customer name
    //         $data['LeadName'] = $request->LeadName;
    //     }

    //     if ($request->assigned_to != '') {
    //         $assignto = $request->assigned_to;
    //     } else {
    //         $assignto = Auth::user()->id;
    //     }

    //     // Insert the data into the leads table
    //     // Lead::create($data);
    //     $lead = Lead::create($data);
    //     $lastInsertId = $lead->id;

    //     $leadproduct = DB::table('leadproducts')
    //         ->insertGetId([
    //             "singleproduct" => $request->products,
    //             "leadid" => $lastInsertId,
    //             "statuslead" => $request->Leadstatus,
    //             "enquirydate" => $request->Entrydate,
    //         ]);
    //     $maxValue = $lastInsertId;
    //     $invID = $maxValue;
    //     $invID = str_pad($invID, 4, '0', STR_PAD_LEFT);
    //     $leadID = "LEAD" . $invID;

    //     DB::table('leads')
    //         ->where('id', $lastInsertId)
    //         ->update([
    //             "lead_id" => $leadID,
    //             "assigned_to" => $assignto,

    //         ]);

    //     $leadrt = Lead::with(['assignedTo', 'leadstatus'])->find($lastInsertId);

    //     $adminEmail = 'nvikram.sts@gmail.com'; // Replace with your admin email
    //     $assignedUserEmail = User::find($assignto)->email ?? null;

    //     Mail::to($adminEmail)->send(new LeadCreatedMail($leadrt));

    //     if ($assignedUserEmail) {
    //         Mail::to($assignedUserEmail)->send(new LeadCreatedMail($leadrt));
    //     }

    //     return redirect()->route('pages.leads')->with('success', 'Lead saved successfully!');
    // }

    public function saveleads(Request $request)
    {
        $this->validateEnquiryDetail($request);

        $data = $request->except('_token', 'newCustomerName', 'quantity', 'ga_number', 'assigndesign');

        if ($request->LeadName === 'new' && $request->newCustomerName) {
            $data['LeadName'] = $request->newCustomerName;
        } else {
            $data['LeadName'] = $request->LeadName;
        }

        $assignto = $request->assigned_to ?: Auth::user()->id;

        $lead = Lead::create($data);
        $lastInsertId = $lead->id;

        // Insert leadproduct
        DB::table('leadproducts')->insert([
            "singleproduct" => $request->products,
            "leadid" => $lastInsertId,
            "statuslead" => $request->Leadstatus,
            "enquirydate" => $request->Entrydate,
        ]);

        // Generate lead ID
        $leadID = "LEAD" . str_pad($lastInsertId, 4, '0', STR_PAD_LEFT);

        DB::table('leads')->where('id', $lastInsertId)->update([
            "lead_id" => $leadID,
            "assigned_to" => $assignto,
        ]);

        $lead = Lead::with(['assignedTo', 'leadstatus'])->find($lastInsertId);

        // If status is 343 (CONVERTED), do follow-up operations
        if ((string) $request->Leadstatus === '343') {

            // Check existing customer
            $existingCustomer = Customer::where('contact_no', $lead->phonumber)->first();
            $customerId = null;

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

                $customerId = $customer->cusid;
            } else {
                $customerId = $existingCustomer->cusid;
            }

            // Update lead with customer ID
            $lead->customeriid = $customerId;
            $lead->save();

            // Handle file upload
            $gaFilePath = null;
            if ($request->hasFile('ga_number')) {
                $file = $request->file('ga_number');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/images/'), $filename);
                $gaFilePath = 'assets/images/' . $filename;
            }

            $selectedCategory = ProductCategory::find($request->category);
            $isSpecialCategory = $this->categoryRequiresDesign($selectedCategory);
            $designAssignee = $this->resolveCategoryDesignAssignee($selectedCategory, $request->assigndesign);

            if ($isSpecialCategory) {
                $design = Design::create([
                    'idlead' => $lead->id,
                    'idcustomer' => $customerId,
                    'product' => $request->products,
                    'category' => $request->category,
                    'ganumber' => $gaFilePath ?? null,
                    'quantity' => $request->quantity,
                    'assigndesign' => $designAssignee,
                ]);
                $this->sendDesignEmail($design);
            } else {
                $quotation = Quotation::create([
                    'id_lead' => $lead->id,
                    'id_customer' => $customerId,
                    'products' => $request->products,
                    'categorys' => $request->category,
                    'ga_number' => $gaFilePath ?? null,
                    'quantitys' => $request->quantity,
                    'quota_id' => 'MSE/' . date('Y') . '/' . str_pad(Quotation::max('id') + 1, 4, '0', STR_PAD_LEFT),
                ]);
                $lead->quota_proceed = 1;
                $lead->save();

                $this->sendQuotationEmail($quotation);
            }

            // Reduce product stock
            $product = Product::find($request->products);
            if ($product) {
                $product->reduceStock($request->products, $request->quantity, "Lead won for this product");
            }
        }

        // Send mail
        $adminEmail = 'nvikram.sts@gmail.com';
        $assignedUserEmail = User::find($assignto)->email ?? null;

        Mail::to($adminEmail)->send(new LeadCreatedMail($lead));
        if ($assignedUserEmail) {
            Mail::to($assignedUserEmail)->send(new LeadCreatedMail($lead));
        }

        return redirect()->route('pages.leads')->with('success', 'Lead saved successfully!');
    }



    public function singleLead($leadid)
    {
        // Fetch lead details using the ID
        $lead = Lead::find($leadid);

        // Check if lead exists
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found');
        }

        $leadProducts = DB::table('leadproducts')
            ->where('leadid', $leadid)
            ->get();

        // Pass lead data to the view
        return view('pages.singlelead', compact('lead', 'leadProducts'));
    }

    public function singlequota($leadid, Request $request)
    {
        // Fetch lead details using the ID
        $lead = Lead::find($leadid);

        // Check if lead exists
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found');
        }

        if ($request->has('final')) {
            return redirect($request->final);
        }


        $leadProducts = DB::table('leadproducts')
            ->where('leadid', $leadid)
            ->get();

        // Pass lead data to the view
        return view('pages.singlequota', compact('lead', 'leadProducts'));
    }

    public function createquota($leadid)
    {
        // Fetch lead details using the ID
        $lead = Lead::find($leadid);

        // Check if lead exists
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found');
        }

        $leadProducts = DB::table('leadproducts')
            ->where('leadid', $leadid)
            ->get();
        $quotations = DB::table('quotations')
            ->where('id_lead', $leadid)
            ->first();
        $dovs = DateOfValidity::latest()->get();
        $paymentTerms = $this->deduplicateTerms($this->getSegmentTerms($lead->category, 1)->get());
        $warrantyTerms = $this->deduplicateTerms($this->getSegmentTerms($lead->category, 2)->get());

        $settings = AdminSetting::first();

        // Pass lead data to the view
        return view('pages.createestimate', compact('lead', 'leadProducts', 'quotations', 'settings', 'dovs', 'paymentTerms', 'warrantyTerms'));
    }

    public function editquota($quotaId)
    {

        $quotationProductDetails = Quotation::where('id', $quotaId)->first();
        $freightDetails = Freight::with('freightDetail')->where('quotationno', $quotaId)->first();

        $leadid = $quotationProductDetails->id_lead;

        // Fetch lead details using the ID
        $lead = Lead::find($leadid);

        // Check if lead exists
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found');
        }

        $leadProducts = DB::table('leadproducts')
            ->where('leadid', $leadid)
            ->get();
        // $quotations = DB::table('quotationproduct')
        //     ->where('leadno', $leadid)
        //     ->first();
        $quotations = Quotation::where('id', $quotaId)
            ->firstOrFail();

        $quotationProducts = QuotationProduct::with('accessories')->where('quotationno', $quotations->quota_id)->get();
        $categories = ProductCategory::all();
        $dovs = DateOfValidity::latest()->get();
        $paymentTerms = $this->appendCurrentTerm(
            $this->getSegmentTerms($lead->category, 1)->get(),
            optional($freightDetails)->termscondition
        );
        $warrantyTerms = $this->appendCurrentTerm(
            $this->getSegmentTerms($lead->category, 2)->get(),
            optional($freightDetails)->warranty
        );
        // $products = Products::all();



        $settings = AdminSetting::first();

        // Pass lead data to the view
        return view('pages.quotation.edit_estimate', compact('lead', 'categories', 'leadProducts', 'quotations', 'quotationProducts', 'quotationProductDetails', 'freightDetails', 'settings', 'dovs', 'paymentTerms', 'warrantyTerms'));
    }



    public function updateStatus(Request $request)
    {

        // Update lead table
        Lead::where('id', $request->leadid)->update([
            'Leadstatus' => $request->statuslead,

        ]);

        // Update leadproducts table (only if productid is provided)
        if (!empty($request->productid)) {
            LeadProduct::where('id', $request->productid)->update([
                'statuslead' => $request->statuslead,
                'coverteddate' => $request->converteddate,
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully.');
    }


    public function Leadedit($leadid)
    {
        // Fetch lead details using the ID
        $row = Lead::find($leadid);

        // Check if lead exists
        if (!$row) {
            return redirect()->back()->with('error', 'Lead not found');
        }

        // Pass lead data to the view
        return view('pages.lead-edit', compact('row'));
    }


    public function updatelead(Request $request)
    {
        $this->validateEnquiryDetail($request);

        $id = $request->id;
        if ($request->assigned_to != '') {
            $assignto = $request->assigned_to;
        } else {
            $assignto = Auth::user()->id;
        }
        $data = $request->except('_token');

        $lead = Lead::find($id);


        DB::table('leads')
            ->where('id', $id)
            ->update([
                "assigned_to" => $assignto,

            ]);

        DB::table('leadproducts')
            ->where('leadid', $id)
            ->update([
                "singleproduct" => $request->products,
                "statuslead" => $request->Leadstatus,
                "enquirydate" => $request->Entrydate,

            ]);

        // $updatedata = $lead->update($data);
        $updatedata = $lead->update($data);

        if ($request->ajax()) {
            if ($updatedata) {
                return response()->json(['success' => true, 'message' => 'Lead updated successfully']);
            } else {
                return response()->json(['error' => 'Failed to update lead']);
            }
        }

        // For normal form submission (non-AJAX fallback)
        return redirect()->route('pages.leads')->with('message', 'Lead Updated Successfully...');

        // return redirect()->back()->with('message','Lead Updated Successfully...');
    }

    public function softDelete($id)
    {
        $lead = Lead::find($id)->delete();

        if ($lead) {

            return response()->json(['success' => true, 'message' => 'Lead deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Lead not found.']);
    }

    public function index(LeadDataTable $dataTable)
    {
        return $dataTable->render('pages.leads');
    }
    public function viewlost(LostDataTable $dataTable)
    {
        return $dataTable->render('pages.losts');
    }

    public function viewconvert(ConvertedDataTable $dataTable)
    {
        return $dataTable->render('pages.viewconverted');
    }

    public function viewquota(QuotationDataTable $dataTable)
    {
        return $dataTable->render('pages.viewquotation');
    }
    public function updateClosureDate(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'closure_date' => 'required|date',
        ]);

        Lead::where('id', $request->lead_id)->update([
            'closure_date' => $request->closure_date
        ]);

        return response()->json(['status' => 'success']);
    }

    // public function updateQutoStatus(Request $request)
    // {
    //     $request->validate([
    //         'lead_id' => 'required|exists:leads,id',
    //         'quto_status' => 'required|exists:quotation_status,id',
    //     ]);

    //     $lead = Lead::find($request->lead_id);
    //     $lead->quto_status = $request->quto_status;
    //     $lead->qutostatus_date = now();
    //     $lead->save();

    //     return response()->json(['message' => 'Quotation status updated.']);
    // }

    public function updateQutoStatus(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'quto_status' => 'required|exists:quotation_status,id',
            'quotastatus_remarks' => 'required_if:quto_status,3,4,5'
        ], [
            'quotastatus_remarks.required_if' =>
                'Remarks are mandatory for Hold, Lost, or Postponed status.'
        ]);

        $lead = Lead::findOrFail($request->lead_id);
        $lead->quto_status = $request->quto_status;
        $lead->quotastatus_remarks = $request->quotastatus_remarks;
        $lead->qutostatus_date = now();
        $lead->save();

        return response()->json([
            'message' => 'Quotation status updated successfully.'
        ]);
    }



    public function saveleadsproduct(Request $request)
    {
        $statuslead = $request->statuslead;
        $singleproduct = $request->singleproduct;
        $leadid = $request->leadid;
        $enquirydate = $request->enquirydate;
        $coverteddate = $request->coverteddate;

        $productid = DB::table('leadproducts')
            ->insert([
                "statuslead" => $statuslead,
                "singleproduct" => $singleproduct,
                "leadid" => $leadid,
                "enquirydate" => $enquirydate,
                "coverteddate" => $coverteddate,
            ]);

        return response()->json([
            'status' => '200',
            'message' => 'Product Added SuccessFully'
        ]);
    }

    public function getfandfreports(Request $request)
    {
        $state_id = $request->input('state_id');  // Make sure the variable matches the one from AJAX

        // Fetch districts based on state_id
        $districts = DB::table('city_list')->where('state_code', $state_id)
            ->select('id', 'city_name')->get();

        // Build the HTML for the city options
        $districtOptions = '<option value="">-- Choose City --</option>';
        foreach ($districts as $district) {
            $districtOptions .= "<option value='" . $district->id . "'>" . $district->city_name . "</option>";
        }

        return response()->json([
            'citylist' => $districtOptions  // Return the options as a response
        ]);
    }

    public function shifandfreports(Request $request)
    {
        $state_id = $request->input('state_id');  // Make sure the variable matches the one from AJAX

        // Fetch districts based on state_id
        $districts = DB::table('city_list')->where('state_code', $state_id)
            ->select('id', 'city_name')->get();

        // Build the HTML for the city options
        $districtOptions = '<option value="">-- Choose City --</option>';
        foreach ($districts as $district) {
            $districtOptions .= "<option value='" . $district->id . "'>" . $district->city_name . "</option>";
        }

        return response()->json([
            'shcitylist' => $districtOptions  // Return the options as a response
        ]);
    }

    public function getfandfrepor(Request $request)
    {
        $state = $request->input('state');  // Make sure the variable matches the one from AJAX

        // Fetch districts based on state_id
        $districts = DB::table('city_list')->where('state_code', $state)
            ->select('id', 'city_name')->get();

        // Build the HTML for the city options
        $districtOptions = '<option value="">-- Choose City --</option>';
        foreach ($districts as $district) {
            $districtOptions .= "<option value='" . $district->id . "'>" . $district->city_name . "</option>";
        }

        return response()->json([
            'city' => $districtOptions  // Return the options as a response
        ]);
    }


    public function getfandfrep(Request $request)
    {
        $category = $request->input('category');
        $subCategory = $request->input('subCategory');

        // Fetch products based on category and subCategory
        $products = DB::table('products')->where('Productcategory', $category)->where('sub_cat_id', $subCategory)->select('id', 'productname')->get();

        // Build the HTML for the product options
        $productOptions = '';
        foreach ($products as $produ) {
            $productOptions .= "<option value='" . $produ->id . "'>" . e($produ->productname) . "</option>";
        }

        return response()->json([
            'products' => $productOptions
        ]);
    }

    public function getProductGroupFunction(Request $request)
    {
        $category = $request->input('category');

        $subCatData = ProductSubCategory::where('cat_id', $category)
            ->orderBy('subcategory_name', 'asc')
            ->select('id', 'subcategory_name')
            ->get();

        // Defense-in-depth: Deduplicate in collection by normalized name so UI never displays duplicate options
        $uniqueSubs = $subCatData->unique(function ($item) {
            return strtolower(trim($item->subcategory_name));
        });

        // Build the HTML for the options
        $data = '<option value="">-- Choose Group --</option>';
        foreach ($uniqueSubs as $item) {
            $data .= "<option value='" . $item->id . "'>" . e($item->subcategory_name) . "</option>";
        }

        return response()->json([
            'data' => $data
        ]);
    }


    public function storedesign(Request $request)
    {
        $request->validate([
            'design' => 'required|string',
            'drawing.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'offer.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ], [
            'design.required' => 'Customized Design Field is required.',
        ]);


        $authUser = auth()->user();
        $authUserEmail = $authUser->email;
        $authUserRole = $authUser->role;  // Assuming you have a 'role' column
        $leadSalesManagerEmail = null;

        $assigneeto = null;

        if ($authUserRole === 'Design Engineer') {
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
        $reverse = ($authUserRole === 'Design Engineer' || auth()->id() == $assigneeto) ? 1 : 0;

        $leadId = Lead::find($request->leadiid)?->lead_id ?? 'N/A';
        $productName = Product::find($request->proname)?->productname ?? 'N/A';
        $categoryName = ProductCategory::find($request->catename)?->category_name ?? 'N/A';

        $commonData = [
            'leadiid' => $request->leadiid,
            'catename' => $request->catename,
            'proname' => $request->proname,
            'design' => $request->design,
            'assigneeto' => $assigneeto,
            'assignee' => auth()->id(),
            'reverse' => $reverse,
            // 'reverse' => (auth()->id() == ($request->assigneeto ?? null)) ? 1 : 0,
        ];

        $drawingFiles = $request->file('drawing');
        $offerFiles = $request->file('offer');
        $basicsupplies = $request->basicsupply;

        $uploadedFiles = [];

        // If basicsupply is provided (Design Login)
        // if ($basicsupplies && is_array($basicsupplies)) {
        //     foreach ($basicsupplies as $index => $basicsupply) {
        //         $data = $commonData;

        //         // Handle Drawing File
        //         if (isset($drawingFiles[$index])) {
        //             $drawingFile = $drawingFiles[$index];
        //             $drawingFilename = time() . '_drawing_' . $drawingFile->getClientOriginalName();
        //             $drawingFile->move(public_path('assets/images/'), $drawingFilename);
        //             $data['drawing'] = 'assets/images/' . $drawingFilename;
        //             $uploadedFiles[] = $data['drawing'];
        //         }

        //         // Handle Offer File
        //         if (isset($offerFiles[$index])) {
        //             $offerFile = $offerFiles[$index];
        //             $offerFilename = time() . '_offer_' . $offerFile->getClientOriginalName();
        //             $offerFile->move(public_path('assets/images/'), $offerFilename);
        //             $data['offer'] = 'assets/images/' . $offerFilename;
        //             $uploadedFiles[] = $data['offer'];
        //         }

        //         // Assign Other Inputs
        //         $data['basicsupply'] = $basicsupply;
        //         $data['freightvalue'] = $request->freightvalue[$index] ?? null;
        //         $data['installvalue'] = $request->installvalue[$index] ?? null;
        //         $data['pricevalue'] = $request->pricevalue[$index] ?? null;

        //         LeadDesign::create($data);
        //     }
        // }
        // // If only GA Drawing is provided (Non-Design Login)
        // elseif ($drawingFiles) {
        //     foreach ($drawingFiles as $index => $drawingFile) {
        //         $data = $commonData;
        //         $drawingFilename = time() . '_drawing_' . $drawingFile->getClientOriginalName();
        //         $drawingFile->move(public_path('assets/images/'), $drawingFilename);
        //         $data['drawing'] = 'assets/images/' . $drawingFilename;
        //         $uploadedFiles[] = $data['drawing'];

        //         LeadDesign::create($data);
        //     }
        // }
        if ($basicsupplies && is_array($basicsupplies)) {
            foreach ($basicsupplies as $index => $basicsupply) {
                $data = $commonData;

                if (isset($drawingFiles[$index])) {
                    $drawingFile = $drawingFiles[$index];
                    $drawingFilename = time() . '_drawing_' . $drawingFile->getClientOriginalName();
                    $drawingFile->move(public_path('assets/images/'), $drawingFilename);
                    $data['drawing'] = 'assets/images/' . $drawingFilename;
                    $uploadedFiles[] = $data['drawing'];
                }

                if (isset($offerFiles[$index])) {
                    $offerFile = $offerFiles[$index];
                    $offerFilename = time() . '_offer_' . $offerFile->getClientOriginalName();
                    $offerFile->move(public_path('assets/images/'), $offerFilename);
                    $data['offer'] = 'assets/images/' . $offerFilename;
                    $uploadedFiles[] = $data['offer'];
                }

                $data['basicsupply'] = $basicsupply;
                $data['freightvalue'] = $request->freightvalue[$index] ?? null;
                $data['installvalue'] = $request->installvalue[$index] ?? null;
                $data['pricevalue'] = $request->pricevalue[$index] ?? null;

                LeadDesign::create($data);
            }
        } elseif ($drawingFiles) {
            foreach ($drawingFiles as $index => $drawingFile) {
                $data = $commonData;

                $drawingFilename = time() . '_drawing_' . $drawingFile->getClientOriginalName();
                $drawingFile->move(public_path('assets/images/'), $drawingFilename);
                $data['drawing'] = 'assets/images/' . $drawingFilename;
                $uploadedFiles[] = $data['drawing'];

                LeadDesign::create($data);
            }
        } else {
            // ✅ New case: Only design text, no files or basicsupply
            $data = $commonData;
            LeadDesign::create($data);
        }
        Design::updateOrCreate(
            // ['idlead' => $request->leadiid],
            ['product' => $request->proname]
        );


        if ($authUserRole === 'Design Engineer') {
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
                    'assignee' => auth()->id(),
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
                'assignee' => auth()->id(),
                'reverse' => $reverse,
            ];
        }


        // Send Mail to Auth User (Compulsory)
        Mail::to($authUserEmail)
            ->send(new DesignUploadedMail($mailData, $uploadedFiles));

        // Send Mail to Lead's Sales Manager if Auth User is 'design'
        if ($authUserRole === 'Design Engineer' && $leadSalesManagerEmail) {
            Mail::to($leadSalesManagerEmail)
                ->send(new DesignUploadedMail($mailData, $uploadedFiles));
        }

        return redirect()->back()->with('success', 'Designs saved successfully!');
    }



    // public function updateStatusdf(Request $request)
    // {
    //     // Find the lead
    //     $lead = Lead::find($request->lead_id);

    //     if ($lead) {
    //         // Update lead details
    //         $lead->Leadstatus = $request->status;
    //         $lead->category = $request->category;
    //         $lead->products = $request->products;
    //         // $lead->remarks = $request->remarks;
    //         $lead->save();

    //         // Check if status is 'CONVERTED'
    //         if ($request->status == '343') {
    //             // Check if the mobile number already exists in the customer table
    //             $existingCustomer = Customer::where('contact_no', $lead->phonumber)->first();

    //             if (!$existingCustomer) {
    //                 // Insert data into customer table only if the customer does not exist
    //                 $customer = new Customer();
    //                 $customer->customername = $lead->LeadName;
    //                 $customer->contact_name = $lead->contactname;
    //                 $customer->cus_email = $lead->email;
    //                 $customer->country_code = "76";
    //                 $customer->contact_no = $lead->phonumber;
    //                 $customer->department = $lead->department;
    //                 $customer->designation = $lead->designation;
    //                 $customer->address = $lead->billaddress;
    //                 $customer->country = $lead->billingcountry;
    //                 $customer->city = $lead->citylist;
    //                 $customer->state = $lead->state_id;
    //                 $customer->postalcode = $lead->postalcode;
    //                 $customer->gstno = $lead->gst;
    //                 $customer->save();

    //                 // Generate cusid after saving customer
    //                 $customer->cusid = 'CUS' . str_pad($customer->id, 4, '0', STR_PAD_LEFT);
    //                 $customer->save();

    //                 // Update the 'customeriid' in the leads table
    //                 $lead->customeriid = $customer->cusid;
    //                 $lead->save();
    //             } else {
    //                 // If the customer already exists, update the lead with the existing customer ID
    //                 $lead->customeriid = $existingCustomer->cusid;
    //                 $lead->save();
    //             }
    //         }

    //         return response()->json(['success' => true, 'message' => 'Lead and products updated successfully']);
    //     }

    //     return response()->json(['success' => false, 'message' => 'Lead not found']);
    // }
    public function leupdateStatusdf(Request $request)
    {
        $lead = Lead::find($request->lead_id);

        if ($lead) {
            $lead->Leadstatus = $request->status;
            $lead->remarks = $request->remarks;
            $lead->save();

            return response()->json([
                'success' => true,
                'redirect_url' => url('/viewleads')
            ]);
        }

        return response()->json([
            'success' => false
        ], 404);
    }

    public function updateStatusdf(Request $request)
    {
        // Find the lead
        $lead = Lead::find($request->lead_id);

        if ($lead) {

            $gaFilePath = null;
            if ($request->hasFile('ga_number')) {
                $file = $request->file('ga_number');
                $filename = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('assets/images/');
                $file->move($destinationPath, $filename);
                $gaFilePath = 'assets/images/' . $filename;
            }
            $productInput = $request->products;
            if (is_array($productInput)) {
                $productIds = array_values(array_filter($productInput));
                $productString = implode(',', $productIds);
            } else {
                $productIds = !empty($productInput) ? array_filter(explode(',', $productInput)) : [];
                $productString = $productInput;
            }

            // Update lead details
            $lead->Leadstatus = $request->status;
            $lead->category = $request->category;
            $lead->products = $productString;
            $lead->subcategory_id = $request->sub_category_id;
            $lead->remarks = $request->remarks;
            $lead->ganumber = $gaFilePath ?? $lead->ganumber;
            $lead->converteddate = $request->converteddate;

            if ($request->status != '343') {
                $lead->status_updatedate = now(); // or use Carbon::now() if not using Laravel helpers
            }

            $lead->save();

            // Sync leadproducts table
            if (!empty($productIds)) {
                DB::table('leadproducts')->where('leadid', $lead->id)->delete();
                foreach ($productIds as $pId) {
                    if (!empty($pId)) {
                        DB::table('leadproducts')->insert([
                            "singleproduct" => $pId,
                            "leadid" => $lead->id,
                            "statuslead" => $request->status,
                            "enquirydate" => $request->converteddate ?? date('Y-m-d'),
                            "coverteddate" => $request->converteddate ?? date('Y-m-d'),
                            "created_at" => now(),
                            "updated_at" => now(),
                        ]);
                    }
                }
            }

            $customerId = null;

            // Check if status is 'CONVERTED'
            if ($request->status == '343') {
                // Check if the mobile number already exists in the customer table
                $existingCustomer = Customer::where('contact_no', $lead->phonumber)->first();

                if (!$existingCustomer) {
                    // Insert data into customer table only if the customer does not exist
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

                    // Generate cusid after saving customer
                    $customer->cusid = 'CUS' . str_pad($customer->id, 4, '0', STR_PAD_LEFT);
                    $customer->save();

                    $customerId = $customer->cusid;
                } else {
                    $customerId = $existingCustomer->cusid;
                }

                // Update the 'customeriid' in the leads table
                $lead->customeriid = $customerId;
                $lead->save();
            }

            if (empty($request->category)) {
                return response()->json(['success' => true, 'message' => 'Lead status updated successfully']);
            }

            $selectedCategory = ProductCategory::find($request->category);
            $categoriesForDesign = $this->categoryRequiresDesign($selectedCategory);
            $designAssignee = $this->resolveCategoryDesignAssignee($selectedCategory, $request->assigndesign);

            // if (in_array($request->category, $categoriesForDesign)) {
            if ($categoriesForDesign) {

                //    $designInputPath = null;

                // if ($request->hasFile('design_input')) {
//     $file = $request->file('design_input');
//     $filename = time() . '_design_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
//     $destinationPath = public_path('assets/design_inputs/');

                //     // ensure directory exists
//     if (!file_exists($destinationPath)) {
//         mkdir($destinationPath, 0755, true);
//     }

                //     $file->move($destinationPath, $filename);
//     $designInputPath = 'assets/design_inputs/' . $filename;
// }



                // dd($request->assigndesign);
                // Insert into `designs` table
                $design = Design::create([
                    'idlead' => $lead->id,
                    'idcustomer' => $customerId,
                    'product' => $productString,
                    'category' => $request->category,
                    'ganumber' => $gaFilePath ?? null,
                    'quantity' => $request->quantity,
                    'assigndesign' => $designAssignee,
                    // 'design_input'  => $designInputPath,
                ]);

                $lead->quota_proceed = null;
                $lead->save();

                if ($request->has('spec_ids') && $request->has('spec_values')) {

                    foreach ($request->spec_ids as $i => $specId) {

                        $value = $request->spec_values[$i] ?? null;

                        if (!empty($value)) {

                            DesignSpecification::create([
                                'lead_id' => $lead->id,
                                'design_id' => $design->id,
                                'specification' => $specId,      // FK to design_specification_masters (optional but recommended)
                                'spec_value' => $value,
                            ]);
                        }
                    }
                }
                if ($request->hasFile('design_input')) {

                    foreach ($request->file('design_input') as $file) {

                        if (!$file)
                            continue;

                        $filename = time() . '_design_' . uniqid() . '_' .
                            preg_replace('/\s+/', '_', $file->getClientOriginalName());

                        $destinationPath = public_path('assets/design_inputs/');

                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }

                        $file->move($destinationPath, $filename);

                        DesignInput::create([
                            'design_id' => $design->id,
                            'file_path' => 'assets/design_inputs/' . $filename,
                        ]);
                    }
                }

                //                 if ($request->filled('design_specifications')) {
//     foreach ($request->design_specifications as $spec) {
//         if (!empty($spec)) {
//             DesignSpecification::create([
//                 'lead_id'   => $lead->id,
//                 'design_id'=> $design->id,
//                 'specification' => $spec,
//             ]);
//         }
//     }
// }

                // Send email for design
                $this->sendDesignEmail($design);


                // return response()->json([
                //     'success' => true,
                //     'message' => 'Design added successfully',
                //     'redirect_url' => route('pages.viewconverted', [
                //         'next' => route('single.quota', ['leadid' => $lead->id])
                //     ])
                // ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Design added successfully',
                    'redirect_url' => route('pages.viewconverted')
                ]);

            } else {

                $exists = Quotation::where('id_lead', $lead->id)->exists();

                if ($exists) {

                    // 🔁 Already quotation exists → redirect to view
                    // return response()->json([
                    //     'success' => true,
                    //     'message' => 'Quotation already exists',
                    //     'redirect_url' => route('single.quota', ['leadid' => $lead->id])
                    // ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Quotation already exists',
                        'redirect_url' => url('/createquota/' . $lead->id)
                    ]);

                } else {

                    //      $lastBaseQuotation = Quotation::where('quota_id', 'NOT LIKE', '%(R%')
                    //     ->orderByDesc('id')
                    //     ->first();

                    // if ($lastBaseQuotation) {
                    //     $lastNumber = (int) substr($lastBaseQuotation->quota_id, -4);
                    //     $nextNumber = $lastNumber + 1;
                    // } else {
                    //     $nextNumber = 1;
                    // }

                    // $quotaId = 'MSE/' . date('Y') . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                    //             // Insert into `quotations` table
                    //             $quotation = Quotation::create([
                    //                 'id_lead' => $lead->id,
                    //                 'id_customer' => $customerId,
                    //                 'products' => $request->products,
                    //                 'categorys' => $request->category,
                    //                 'ga_number' => $gaFilePath ?? null,
                    //                 'quantitys' => $request->quantity,
                    //                 'quota_id' => $quotaId
                    //             ]);


                    $lead->quota_proceed = 1;
                    $lead->save();

                    // Send email for quotation
                    // $this->sendQuotationEmail($quotation);
                    // return response()->json([
                    //     'success' => true,
                    //     'message' => 'Quotation added successfully',
                    //     'redirect_url' => route('pages.viewquotation', [
                    //         'next' => route('single.quota', ['leadid' => $lead->id]),
                    //         'final' => route('create.quota', ['leadid' => $lead->id])
                    //     ])
                    // ]);
                    $this->sendQuotationEmail($lead);
                    return response()->json([
                        'success' => true,
                        'message' => 'Proceed to quotation',
                        'redirect_url' => url('/createquota/' . $lead->id)
                    ]);
                }
            }

            $product = Product::findOrFail($request->products);
            if ($product) {
                $product->reduceStock($request->products, $request->quantity, "Lead won for this product");
            }

            return response()->json(['success' => true, 'message' => 'Lead and products updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Lead not found']);
    }

    // Send email for Design
    // Send email for Design
    private function sendDesignEmail($design)
    {

        $authUser = auth()->user();

        // Fetch lead to get lead_id (NOT id)
        $lead = Lead::find($design->idlead);

        $categoryName = ProductCategory::find($design->category)->category_name ?? 'N/A';

        // Fetch specifications
        $specifications = DesignSpecification::where('design_id', $design->id)->get();

        // Fetch design inputs
        $designInputs = DesignInput::where('design_id', $design->id)->get();

        $data = [
            'lead_id' => $lead->lead_id ?? $lead->id, // ✅ actual lead_id
            'category' => $categoryName,
            'specifications' => $specifications,
        ];

        // Collect email recipients
        $emails = [];

        if ($authUser && $authUser->email) {
            $emails[] = $authUser->email;
        }

        // Assigned designer
        if (!empty($design->assigndesign)) {
            $designer = User::find($design->assigndesign);
            if ($designer && $designer->email) {
                $emails[] = $designer->email;
            }
        }

        // Remove duplicates
        $emails = array_unique($emails);

        Mail::send('emails.design', $data, function ($message) use ($emails, $designInputs) {

            $message->to($emails)
                ->subject('Design Assigned');

            // Attach design input files
            foreach ($designInputs as $file) {
                $fullPath = public_path($file->file_path);
                if (file_exists($fullPath)) {
                    $message->attach($fullPath);
                }
            }
        });
        // $authUser = auth()->user();

        // // Fetch names based on IDs
        // $productName = Product::find($design->product)->productname ?? 'N/A';
        // $categoryName = ProductCategory::find($design->category)->category_name ?? 'N/A';
        // $assignedUserName = User::find($design->assigndesign)->name ?? 'N/A';

        // $data = [
        //     'title' => 'Lead Convert and Move to Design',
        //     'message' => "New Lead assign for Design",
        //     'product' => $productName,   // Display product name
        //     'category' => $categoryName, // Display category name
        //     'ganumber' => strval($design->ganumber),
        //     'quantity' => strval($design->quantity),
        //     'assigndesign' => $assignedUserName, // Display assigned user name
        // ];

        // // Send email to the auth user
        // Mail::send('emails.design', $data, function ($message) use ($authUser) {
        //     $message->to($authUser->email)
        //         ->subject('Design Created Notification');
        // });

        // // Send mail to assigned designer if exists
        // if (!empty($design->assigndesign)) {
        //     $assignedUser = User::find($design->assigndesign);
        //     if ($assignedUser) {
        //         Mail::send('emails.design', $data, function ($message) use ($assignedUser) {
        //             $message->to($assignedUser->email)
        //                 ->subject('New Lead assign for Design');
        //         });
        //     }
        // }
    }


    // Send email for Quotation
    private function sendQuotationEmail($lead)
    {
        // $authUser = auth()->user();

        // $productName = Product::find($quotation->products)->productname ?? 'N/A';
        // $categoryName = ProductCategory::find($quotation->categorys)->category_name ?? 'N/A';
        // // $assignedUserName = User::find($quotation->quota_id)->name ?? 'N/A';

        // $data = [
        //     'title' => 'Lead Convert and Move to Quotation',
        //     'message' => "New Lead Convert and Move to Quotation",
        //     'products' => $productName,
        //     'category' => $categoryName,
        //     'ga_number' => strval($quotation->ga_number),
        //     'quantity' => strval($quotation->quantitys),
        //     'quota_id' => strval($quotation->quota_id),
        // ];

        // Mail::send('emails.quotation', $data, function ($message) use ($authUser) {
        //     $message->to($authUser->email)
        //         ->subject('New Lead Convert and Move to Quotation');
        // });
        $authUser = auth()->user();

        $categoryName = ProductCategory::find($lead->category)->category_name ?? 'N/A';

        $data = [
            'lead_id' => $lead->lead_id ?? $lead->id, // ✅ real lead_id
            'category' => $categoryName,
        ];

        // Collect recipients
        $emails = [];

        if ($authUser && $authUser->email) {
            $emails[] = $authUser->email;
        }

        // Lead assigned user
        if (!empty($lead->assigned_to)) {
            $assignedUser = User::find($lead->assigned_to);
            if ($assignedUser && $assignedUser->email) {
                $emails[] = $assignedUser->email;
            }
        }

        // Remove duplicate emails
        $emails = array_unique($emails);

        if (count($emails)) {
            Mail::send('emails.quotation', $data, function ($message) use ($emails) {
                $message->to($emails)
                    ->subject('Lead Moved to Quotation');
            });
        }

    }



    public function designapproval(Request $request)
    {
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

                // Quotation::create([
                //     'id_lead' => $design->idlead,
                //     'id_customer' => $design->idcustomer,
                //     'products' => $design->product,
                //     'categorys' => $design->category,
                //     'ga_number' => $design->ganumber,
                //     'quantitys' => $design->quantity,
                //     'quota_id' => 'MSE/' . date('Y') . '/' . str_pad(Quotation::max('id') + 1, 4, '0', STR_PAD_LEFT)
                // ]);
            }

            DB::commit();
            $authUserEmail = Auth::user()->email;

            $mailData = [
                'leadid' => $leadId,
                'products' => $products,
                'categorys' => $categories,
                'quantitys' => $quantities,
                'quota_doc' => $filePath
            ];

            Mail::to($authUserEmail)
                ->send(new MoveQuotationMail($mailData));

            return redirect()->route('pages.viewconverted')->with('success', 'Data successfully updated and inserted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    private function generateBaseQuotationNo()
    {
        $year = date('Y');

        $lastBaseQuotation = Quotation::where('quota_id', 'like', "MSE/{$year}/%")
            ->where('quota_id', 'not like', '%(%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(quota_id, '/', -1) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->first();

        if ($lastBaseQuotation) {
            preg_match('/MSE\/\d{4}\/(\d+)/', $lastBaseQuotation->quota_id, $matches);
            $next = (int) $matches[1] + 1;
        } else {
            $next = 1;
        }

        return 'MSE/' . $year . '/' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }







    // public function createestimate(Request $request)
    // {
    //     $quotationProduct = QuotationProduct::create([
    //         'leadno' => $request->leadno,
    //         'quotationno' => $request->quotationno,
    //         'cliname' => $request->clientname,
    //         'placeofsupply' => $request->placeofsupply,
    //         'gstnum' => $request->gstnum,
    //         'catepro' => $request->catepro,
    //         'product' => $request->product,
    //         'part_no' => $request->part_no,
    //         'quanty' => $request->quanty,
    //         'allowdis' => $request->allowdis,
    //         'requestdis' => $request->requestdis,
    //         'ratee' => $request->ratee,
    //         'price' => $request->price,
    //         'priceamt' => $request->priceamt,
    //         'lbt' => $request->lbt,
    //         'tax' => $request->tax,
    //         'taxamt' => $request->taxamt,
    //         'grandtotal' => $request->grandtotal,
    //     ]);

    //     Freight::create([
    //         'leadnumber' => $request->leadno,
    //         'quotationno' => $request->quotationno,
    //         'termscondition' => $request->termscondition,
    //         'dov' => $request->dov,
    //         'freight' => $request->freight,
    //         'exwork' => $request->exwork,
    //         'freightextra' => $request->freightextra,
    //         'unloading' => $request->unloading,
    //         'remarks' => $request->remarks,
    //         'currency' => $request->currency,
    //         'warranty' => $request->warranty,
    //         'installation' => $request->installation,
    //         'agent' => $request->agent,
    //         'amount' => $request->ammt,
    //     ]);

    //     return redirect()->route('single.quota', ['leadid' => $quotationProduct->leadno])
    //                      ->with('success', 'Estimate created successfully!');
    // }

    // public function createestimate(Request $request)
    // {

    //     $freightCatLastId = null;
    //     $freightSubCatLastId = null;
    //     $freightDetailLastId = null;

    //     if ($request->freight_category_id == "new") {

    //         $ifExist = FreightCategory::where('categoty', $request->freight_category)->first();

    //         if (!$ifExist) {
    //             $freightDetail = new FreightCategory();
    //             $freightDetail->categoty = $request->freight_category;
    //             $freightDetail->save();
    //             $freightCatLastId = FreightCategory::latest()->first()->id;
    //         } else {
    //             $freightCatLastId = $ifExist->id;
    //         }
    //     } else {
    //         $freightCatLastId = $request->freight_category_id;
    //     }

    //     if ($request->freight_sub_category_id == "new") {

    //         $ifExist = FreightSubCategory::where('sub_category', $request->freight_sub_category)->first();

    //         if (!$ifExist) {
    //             $freightSubCat = new FreightSubCategory();
    //             $freightSubCat->freight_cat_id = $freightCatLastId;
    //             $freightSubCat->sub_category = $request->freight_sub_category;
    //             $freightSubCat->save();
    //             $freightSubCatLastId = FreightSubCategory::latest()->first()->id;
    //         } else {
    //             $freightSubCatLastId = $ifExist->id;
    //         }
    //     } else {
    //         $freightSubCatLastId = $request->freight_sub_category_id;
    //     }

    //     if ($request->freight_detail_id == "new") {

    //         $ifExist = FreightDetail::where('details', $request->freight_sub_category)->first();

    //         if (!$ifExist) {
    //             $freightDetail = new FreightDetail();
    //             $freightDetail->freight_cat_id = $freightCatLastId;
    //             $freightDetail->freight_sub_cat_id = $freightSubCatLastId;
    //             $freightDetail->details = $request->freight_detail;
    //             $freightDetail->save();
    //             $freightDetailLastId = FreightDetail::latest()->first()->id;
    //         } else {
    //             $freightDetailLastId = $ifExist->id;
    //         }
    //     } else {
    //         $freightDetailLastId = $request->freight_detail_id;
    //     }


    //     $allowdis = null;
    //     $requestdis = null;


    //     if (!empty($request->allowdis)) {
    //         $allowdis = floatval($request->allowdis);
    //     }

    //     if (!empty($request->requestdis)) {
    //         $requestdis = floatval($request->requestdis);
    //     }


    //     $disAppValue = $requestdis > $allowdis ? 1 : 2;
    //     $freightApproval = ($request->freight_category_id == "new" ||
    //         $request->freight_sub_category_id == "new" ||
    //         $request->freight_detail_id == "new") ? 1 : 0;

    //     $qutoAppValue = ($disAppValue == 1 || $freightApproval == 1) ? 0 : 2;


    //     DB::beginTransaction();

    //     $same_as_billing = 0;

    //     if ($request->same_as_billing) {
    //         $same_as_billing = 1;
    //     }

    //     try {

    //         $quotationProduct = QuotationProduct::create([
    //             'leadno' => $request->leadno,
    //             'quotationno' => $request->quotationno,
    //             'cliname' => $request->clientname,
    //             'placeofsupply' => $request->placeofsupply,
    //             'shippingcountry' => $request->shippingcountry,
    //             'shistate_id' => $request->shistate_id,
    //             'shippostalcode' => $request->shippostalcode,
    //             'shcitylist' => $request->shcitylist,
    //             'gstnum' => $request->gstnum,
    //             'catepro' => $request->catepro,
    //             'product' => $request->product,
    //             'part_no' => $request->part_no,
    //             'quanty' => $request->quanty,
    //             'allowdis' => $request->allowdis,
    //             'requestdis' => $request->requestdis,
    //             'ratee' => $request->ratee,
    //             'price' => $request->price,
    //             'priceamt' => $request->priceamt,
    //             'lbt' => $request->lbt,
    //             'tax' => $request->tax,
    //             'taxamt' => $request->taxamt,
    //             'grandtotal' => $request->grandtotal,
    //             'dis_app' => $disAppValue,
    //             'quto_app' => $qutoAppValue,
    //             'freight_approval' => $freightApproval,
    //             'same_as_billing_address' => $same_as_billing,
    //         ]);


    //         if ($request->termscondition === 'new' && $request->newtermscondition) {
    //             $term = Term::create([
    //                 'content' => $request->newtermscondition,
    //                 'termtype' => 1,
    //                 'term_approve' => 1,
    //                 'whichterm' => 1,
    //             ]);
    //             $termsConditionId = $term->id;
    //         } else {
    //             $termsConditionId = $request->termscondition;
    //         }


    //         if ($request->warranty === 'new' && $request->newwarcondition) {
    //             $warrantyTerm = Term::create([
    //                 'content' => $request->newwarcondition,
    //                 'termtype' => 2,
    //                 'term_approve' => 1,
    //                 'whichterm' => 1,
    //             ]);
    //             $warrantyId = $warrantyTerm->id;
    //         } else {
    //             $warrantyId = $request->warranty;
    //         }


    //         Freight::create([
    //             'leadnumber' => $request->leadno,
    //             'quotationno' => $quotationProduct->id,
    //             'termscondition' => $termsConditionId,
    //             'dov' => $request->dov,
    //             'freight_detail_id' => $freightDetailLastId,
    //             'freight_sub_category_id' => $freightSubCatLastId,
    //             'freight_category_id' => $freightCatLastId,
    //             'unloading' => $request->unloading,
    //             'remarks' => $request->remarks,
    //             'currency' => $request->currency,
    //             'warranty' => $warrantyId,
    //             'installation' => $request->installation,
    //             'agent' => $request->agent,
    //             'amount' => $request->ammt,
    //             'freight_approve' => $freightApproval,
    //         ]);

    //         DB::commit();


    //         $authUserEmail = auth()->user()->email;

    //         $superAdmins = User::where('role', 'Super Admin')->pluck('email')->toArray();


    //         $mailData = [
    //             'quotationno' => $request->quotationno,
    //             'clientname' => $request->clientname,
    //             'grandtotal' => $request->grandtotal,
    //             'created_at' => now(),
    //         ];


    //         Mail::to($authUserEmail)
    //             ->send(new QuotationCreatedMail($mailData));


    //         foreach ($superAdmins as $adminEmail) {
    //             Mail::to($adminEmail)->send(new QuotationCreatedMail($mailData));
    //         }

    //         return redirect()->route('single.quota', ['leadid' => $quotationProduct->leadno])
    //             ->with('success', 'Estimate created successfully!');
    //     } catch (Exception $e) {
    //         dd($e->getMessage());
    //         DB::rollback();
    //         return back()->with('error', 'An error occurred: ' . $e->getMessage());
    //     }
    // }
//     public function createestimate(Request $request)
// {
//     DB::beginTransaction();

    //     try {

    //         /* -------------------------------------------------
//          | 0️⃣ SAME AS BILLING
//          ------------------------------------------------- */
//         $same_as_billing = $request->same_as_billing ? 1 : 0;

    //         /* -------------------------------------------------
//          | 1️⃣ FREIGHT CATEGORY / SUB / DETAIL (NEW HANDLING)
//          ------------------------------------------------- */
//         // CATEGORY
//         if ($request->freight_category_id === 'new') {
//             $cat = FreightCategory::firstOrCreate([
//                 'categoty' => $request->freight_category
//             ]);
//             $freightCatLastId = $cat->id;
//             $freightApproval = 1;
//         } else {
//             $freightCatLastId = $request->freight_category_id;
//             $freightApproval = 0;
//         }

    //         // SUB CATEGORY
//         if ($request->freight_sub_category_id === 'new') {
//             $sub = FreightSubCategory::firstOrCreate([
//                 'freight_cat_id' => $freightCatLastId,
//                 'sub_category'   => $request->freight_sub_category
//             ]);
//             $freightSubCatLastId = $sub->id;
//             $freightApproval = 1;
//         } else {
//             $freightSubCatLastId = $request->freight_sub_category_id;
//         }

    //         // DETAIL
//         if ($request->freight_detail_id === 'new') {
//             $det = FreightDetail::firstOrCreate([
//                 'freight_cat_id'     => $freightCatLastId,
//                 'freight_sub_cat_id' => $freightSubCatLastId,
//                 'details'            => $request->freight_detail
//             ]);
//             $freightDetailLastId = $det->id;
//             $freightApproval = 1;
//         } else {
//             $freightDetailLastId = $request->freight_detail_id;
//         }

    //         /* -------------------------------------------------
//          | 2️⃣ CREATE / FETCH QUOTATION (ONE PER LEAD)
//          ------------------------------------------------- */
//         $quotation = Quotation::where('id_lead', $request->leadno)->first();

    //         if (!$quotation) {
//             $quotationNo = $this->generateQuotationNo();

    //             $quotation = Quotation::create([
//     'id_lead'      => $request->leadno,
//     'quotation_no' => $quotationNo,

    //     // totals
//     'subtotal'    => 0,
//     'tax_total'   => 0,
//     'grand_total' => 0,

    //     // approvals
//     'dis_app'          => 2,
//     'quto_app'         => 2,
//     'freight_approval' => $freightApproval,
//     'same_as_billing_address' => $same_as_billing,

    //     // billing
//     // 'billing_address'  => $request->billingad,
//     // 'billing_country'  => $request->billingcountry,
//     // 'billing_state'    => $request->state_id,
//     // 'billing_city'     => $request->citylist,
//     // 'billing_pincode'  => $request->postalcode,
//     // 'billing_gst'      => $request->billgs,

    //     // shipping
//     'shipping_address' => $request->placeofsupply,
//     'shipping_country' => $request->shippingcountry,
//     'shipping_state'   => $request->shistate_id,
//     'shipping_city'    => $request->shcitylist,
//     'shipping_pincode' => $request->shippostalcode,
//     'shipping_gst'     => $request->gstnum,
// ]);

    //         }

    //         /* -------------------------------------------------
//          | 3️⃣ INSERT MULTIPLE PRODUCTS
//          ------------------------------------------------- */
//         $subtotal = 0;
//         $taxTotal = 0;
//         $grandTotal = 0;
//         $needsApproval = false;

    //         foreach ($request->product_id as $key => $productId) {

    //             $subtotal   += (float) $request->lbt[$key];
//             $taxTotal   += (float) $request->taxamt[$key];
//             $grandTotal += (float) $request->total[$key];

    //             if ($request->requestdis[$key] > $request->allowdis[$key]) {
//                 $needsApproval = true;
//             }

    //             QuotationProduct::create([
//                 'quotation_id'         => $quotation->id,
//                 'catepro' => $request->product_category_id[$key],
//                 'product'          => $productId,
//                 'quotationno' => $quotationNo,
//                 'leadno'      => $request->leadno,
//                 'part_no'             => $request->part_no[$key],
//                 'quanty'                 => $request->quantity[$key],
//                 'ratee'                => $request->rate[$key],
//                 'allowdis'            => $request->allowdis[$key],
//                 'requestdis'          => $request->requestdis[$key],
//                 'lbt'                 => $request->lbt[$key],
//                 'tax'                 => $request->tax[$key],
//                 'taxamt'              => $request->taxamt[$key],
//                 'grandtotal'               => $request->total[$key],
//             ]);
//         }

    //         /* -------------------------------------------------
//          | 4️⃣ UPDATE QUOTATION TOTALS & APPROVAL
//          ------------------------------------------------- */
//         $quotation->update([
//             'subtotal'    => $subtotal,
//             'tax_total'   => $taxTotal,
//             'grand_total' => $grandTotal,
//             'dis_app'     => $needsApproval ? 1 : 2,
//             'quto_app'    => ($needsApproval || $freightApproval) ? 0 : 2,
//         ]);

    //         /* -------------------------------------------------
//          | 5️⃣ TERMS + WARRANTY
//          ------------------------------------------------- */
//         $termsConditionId = $request->termscondition === 'new'
//             ? Term::create([
//                 'content' => $request->newtermscondition,
//                 'termtype' => 1,
//                 'term_approve' => 1,
//                 'whichterm' => 1,
//             ])->id
//             : $request->termscondition;

    //         $warrantyId = $request->warranty === 'new'
//             ? Term::create([
//                 'content' => $request->newwarcondition,
//                 'termtype' => 2,
//                 'term_approve' => 1,
//                 'whichterm' => 1,
//             ])->id
//             : $request->warranty;

    //         /* -------------------------------------------------
//          | 6️⃣ FREIGHT INSERT (ONCE)
//          ------------------------------------------------- */
//         Freight::create([
//             'leadnumber'              => $request->leadno,
//             'quotationno'             => $quotation->id,
//             'termscondition'          => $termsConditionId,
//             'dov'                     => $request->dov,
//             'freight_category_id'     => $freightCatLastId,
//             'freight_sub_category_id' => $freightSubCatLastId,
//             'freight_detail_id'       => $freightDetailLastId,
//             'unloading'               => $request->unloading,
//             'remarks'                 => $request->remarks,
//             'currency'                => $request->currency,
//             'warranty'                => $warrantyId,
//             'installation'            => $request->installation,
//             'agent'                   => $request->agent,
//             'amount'                  => $request->ammt,
//             'freight_approve'         => $freightApproval,
//         ]);

    //         DB::commit();

    //         return redirect()
//             ->route('single.quota', ['leadid' => $request->leadno])
//             ->with('success', 'Quotation created successfully');

    //     } catch (\Exception $e) {
//         DB::rollBack();
//         return back()->with('error', $e->getMessage());
//     }
// }
    public function createestimate(Request $request)
    {
        if (in_array((string) $request->quto_status, ['3', '4', '5'], true)) {
            $request->validate([
                'quto_status_remarks' => 'required|string',
            ]);
        }

        $request->validate([
            'leadno' => 'required|exists:leads,id',
            'placeofsupply' => 'required|string',
            'shippingcountry' => 'required',
            'shistate_id' => 'required',
            'shcitylist' => 'required',
            'shippostalcode' => 'required',
            'termscondition' => 'required',
            'newtermscondition' => 'required_if:termscondition,new',
            'freight_category_id' => 'required',
            'freight_category' => 'required_if:freight_category_id,new',
            'freight_sub_category' => 'required_if:freight_sub_category_id,new',
            'warranty' => 'required',
            'newwarcondition' => 'required_if:warranty,new',
            'dov_new' => 'required_if:dov,new',
            'new_unloading' => 'required_if:unloading,new',
            'new_installation' => 'required_if:installation,new',
            'new_agent' => 'required_if:agent,new',
            'new_quto_status' => 'required_if:quto_status,new',
            'remarks' => 'nullable|string',
            'currency' => 'required|string',
        ]);

        if ($request->freight_category_id !== 'new' && $request->filled('freight_category_id')) {
            $fCatCheck = FreightCategory::find($request->freight_category_id);
            if ($fCatCheck && stripos($fCatCheck->categoty, 'Mercuriis') !== false) {
                $request->validate([
                    'freight_sub_category_id' => 'required',
                ], [
                    'freight_sub_category_id.required' => 'Please select Included or Extra for Mercuriis Scope.',
                ]);
            }
        } elseif ($request->freight_category_id === 'new' && stripos((string) $request->freight_category, 'Mercuriis') !== false) {
            $request->validate([
                'freight_sub_category_id' => 'required',
            ], [
                'freight_sub_category_id.required' => 'Please select Included or Extra for Mercuriis Scope.',
            ]);
        }

        $leadForValidation = Lead::findOrFail($request->leadno);
        $leadCategoryForValidation = ProductCategory::find($leadForValidation->category);
        $isSpecialForValidation = $leadCategoryForValidation && $leadCategoryForValidation->sub_category === 'special';

        if (!$isSpecialForValidation) {
            $request->validate([
                'product_category_id' => 'required|array|min:1',
                'product_category_id.*' => 'required',
                'product_id' => 'required|array|min:1',
                'product_id.*' => 'required',
                'quantity' => 'required|array|min:1',
                'quantity.*' => 'required|numeric|min:1',
                'rate' => 'required|array|min:1',
                'rate.*' => 'required|numeric|min:0',
                'tax' => 'required|array|min:1',
                'tax.*' => 'required|numeric|min:0',
            ]);
        }

        DB::beginTransaction();

        try {
            $lead = Lead::findOrFail($request->leadno);
            $selectedQuotationStatus = $this->resolveQuotationMasterOption(
                QuotationStatus::class,
                $request->quto_status,
                $request->new_quto_status
            );

            $leadCategory = \App\Models\ProductCategory::find($lead->category);

            $isSpecial = $leadCategory && $leadCategory->sub_category === 'special';


            /* -------------------------------------------------
             | 0️⃣ SAME AS BILLING
             ------------------------------------------------- */
            $same_as_billing = $request->same_as_billing ? 1 : 0;

            /* -------------------------------------------------
             | 1️⃣ FREIGHT CATEGORY / SUB / DETAIL
             ------------------------------------------------- */
            $freightApproval = 0;

            if ($request->freight_category_id === 'new') {
                $cat = FreightCategory::firstOrCreate([
                    'categoty' => $request->freight_category,
                    'product_category_id' => $lead->category,
                ]);
                $freightCatLastId = $cat->id;
                $freightApproval = 1;
            } else {
                $freightCatLastId = $request->freight_category_id;
                if ($freightCatLastId) {
                    $cat = FreightCategory::find($freightCatLastId);
                    if ($cat && stripos($cat->categoty, 'Mercuriis') !== false) {
                        $freightApproval = 1; // Mercuriis always triggers approval
                    }
                }
            }

            if ($request->freight_sub_category_id === 'new') {
                $sub = FreightSubCategory::firstOrCreate([
                    'freight_cat_id' => $freightCatLastId,
                    'sub_category' => $request->freight_sub_category,
                    'product_category_id' => $lead->category,
                ]);
                $freightSubCatLastId = $sub->id;
                $freightApproval = 1;
            } elseif (!empty($request->freight_sub_category_id)) {
                $freightSubCatLastId = $request->freight_sub_category_id;
                $freightApproval = 1; // Any Included/Extra selection triggers approval
            } else {
                $freightSubCatLastId = null;
            }

            if ($request->freight_detail_id === 'new') {
                $det = FreightDetail::firstOrCreate([
                    'freight_cat_id' => $freightCatLastId,
                    'freight_sub_cat_id' => $freightSubCatLastId,
                    'details' => $request->freight_detail
                ]);
                $freightDetailLastId = $det->id;
                $freightApproval = 1;
            } else {
                $freightDetailLastId = $request->freight_detail_id;
            }

            $unloadingId = $this->resolveQuotationMasterOption(
                QuotationUnloadingOption::class,
                $request->unloading,
                $request->new_unloading,
                true
            );
            $installationId = $this->resolveQuotationMasterOption(
                QuotationInstallationScope::class,
                $request->installation,
                $request->new_installation,
                true
            );
            $agentId = $this->resolveQuotationMasterOption(
                QuotationOrcOption::class,
                $request->agent,
                $request->new_agent,
                true
            );


            // $lastQuotation = Quotation::where('id_lead', $request->leadno)
//     ->orderBy('id', 'desc')
//     ->first();

            // if (!$lastQuotation) {
//     // 🟢 FIRST TIME CREATE
//     $quotationNo = $this->generateBaseQuotationNo();
// } else {
//     // 🔁 EDIT → CREATE REVISION
//     $baseNo = preg_replace('/\s*\(R\d+\)$/', '', $lastQuotation->quotation_no);
//     $quotationNo = $this->generateRevisionQuotationNo($baseNo);
// }

            $editMode = $request->filled('quotation_id');
            $revisionMajor = 0;
            $revisionMinor = 0;

            if (!$editMode) {

                // 🟢 NEW BASE QUOTATION
                $quotationNo = $this->generateBaseQuotationNo();

            } else {

                // 🔁 CREATE REVISION
                $currentQuotation = Quotation::findOrFail($request->quotation_id);

                $approvalStatus = $this->getQuotationApprovalStatus($currentQuotation->id);
                $qualifiesForExternal = \App\Helpers\QuotationRevisionHelper::qualifiesForExternalRevision($currentQuotation, $approvalStatus);

                $revData = \App\Helpers\QuotationRevisionHelper::generateNextRevision($currentQuotation, $qualifiesForExternal);
                $quotationNo = $revData['quota_id'];
                $revisionMajor = $revData['revision_major'];
                $revisionMinor = $revData['revision_minor'];
            }


            // FINAL quotation number — DO NOT CHANGE BELOW THIS
// $finalQuotationNo = $quotationNo;



            $quotation = Quotation::create([
                'id_lead' => $request->leadno,
                'quota_id' => $quotationNo,
                'revision_major' => $revisionMajor,
                'revision_minor' => $revisionMinor,
                'parent_quotation_id' => $editMode ? $currentQuotation->id : null,

                'subtotal' => 0,
                'tax_total' => 0,
                'grand_total' => 0,

                'dis_app' => 2,
                'quto_app' => 2,
                'freight_approval' => $freightApproval,
                'same_as_billing_address' => $same_as_billing,

                'shipping_address' => $request->placeofsupply,
                'shipping_country' => $request->shippingcountry,
                'shipping_state' => $request->shistate_id,
                'shipping_city' => $request->shcitylist,
                'shipping_pincode' => $request->shippostalcode,
                'shipping_gst' => $request->gstnum,
            ]);

            /* -------------------------------------------------
     | ⭐ SPECIAL PRODUCT TOTALS
     ------------------------------------------------- */
            /* -------------------------------------------------
             | ⭐ SPECIAL PRODUCT TOTALS
             ------------------------------------------------- */
            if ($isSpecial) {

                $subtotal = (float) $request->sp_subtotal;
                $taxTotal = (float) $request->sp_tax_total;
                $grandTotal = (float) $request->sp_grand_total;
                $basicTotal = (float) $request->sp_basic_total;
                // $allow_dis = (float) $request->allowed_discount;
                $req_dis = (float) $request->sp_request_discount;
                $reqdis_amt = (float) $request->sp_request_disamt;

                $allowedDiscountValue = (float) $request->allowed_discount;
                $requestedDiscount = (float) $request->sp_request_discount;

                $needsApproval = $requestedDiscount > $allowedDiscountValue;

                $quotation->update([
                    'subtotal' => $subtotal,
                    'tax_total' => $taxTotal,
                    'grand_total' => $grandTotal,
                    'basic_total' => $basicTotal,
                    'allow_dis' => $allowedDiscountValue,
                    'req_dis' => $req_dis,
                    'reqdis_amt' => $reqdis_amt,
                    'dis_app' => $needsApproval ? 1 : 2,
                    'quto_app' => $needsApproval ? 0 : 2,
                ]);
            }



            /* -------------------------------------------------
             | 3️⃣ INSERT PRODUCTS (NEW REVISION)
             ------------------------------------------------- */
            $subtotal = 0;
            $taxTotal = 0;
            $grandTotal = 0;
            $needsApproval = false;

            if (!$isSpecial && $request->has('product_id')) {

                QuotationProduct::where('quotationno', $quotationNo)->delete();
                \App\Models\QuotationAccessory::where('quotationno', $quotationNo)->delete();

                foreach ($request->product_id as $key => $productId) {

                    $subtotal += (float) $request->lbt[$key];
                    $taxTotal += (float) $request->taxamt[$key];
                    $grandTotal += (float) $request->total[$key];

                    if ($request->requestdis[$key] > $request->allowdis[$key]) {
                        $needsApproval = true;
                    }

                    $qp = QuotationProduct::create([
                        'quotation_id' => $quotation->id,
                        'quotationno' => $quotationNo,
                        'leadno' => $request->leadno,

                        'catepro' => $request->product_category_id[$key],
                        'product' => $productId,
                        'part_no' => $request->part_no[$key],
                        'quanty' => $request->quantity[$key],
                        'ratee' => $request->rate[$key],
                        'allowdis' => $request->allowdis[$key],
                        'requestdis' => $request->requestdis[$key],
                        'requestdis_amt' => $request->requestdis_amount[$key],
                        'lbt' => $request->lbt[$key],
                        'tax' => $request->tax[$key],
                        'taxamt' => $request->taxamt[$key],
                        'grandtotal' => $request->total[$key],
                    ]);

                    $accList = $request->input("accessories.{$key}") ?? ($request->accessories[$key] ?? null);
                    if (!empty($accList) && is_array($accList)) {
                        foreach ($accList as $accId) {
                            $accModel = \App\Models\Accessory::find($accId);
                            if ($accModel) {
                                $accQty = (int) ($request->input("accessory_quantity.{$key}.{$accId}") ?? 1);
                                if ($accQty < 1) {
                                    $accQty = 1;
                                }
                                $accPrice = (float) $accModel->price;
                                $accTotal = $accPrice * $accQty;

                                \App\Models\QuotationAccessory::create([
                                    'quotation_id'        => $quotation->id,
                                    'quotationproduct_id' => $qp->id,
                                    'quotationno'         => $quotationNo,
                                    'leadno'              => $request->leadno,
                                    'accessory_id'        => $accId,
                                    'name'                => $accModel->name,
                                    'part_no'             => $accModel->part_no,
                                    'hsn'                 => $accModel->hsn,
                                    'price'               => $accPrice,
                                    'quantity'            => $accQty,
                                    'total'               => $accTotal,
                                ]);
                            }
                        }
                    }
                }

                // Determine default installation charge based on product keywords
                // (goods lift = 30000, scissor lift / dock leveler = 15000)
                $defaultInstallCharge = 0.00;
                if ($request->has('product_id') && is_array($request->product_id)) {
                    $productModels = \App\Models\Product::whereIn('id', array_filter($request->product_id))->pluck('productname');
                    foreach ($productModels as $pname) {
                        $pNameLower = strtolower($pname);
                        if (str_contains($pNameLower, 'goods lift')) {
                            $defaultInstallCharge = 30000.00;
                            break;
                        } elseif (str_contains($pNameLower, 'dock leveler') ||
                            str_contains($pNameLower, 'scissor lift')) {
                            $defaultInstallCharge = 15000.00;
                        }
                    }
                }

                $includeInstInBasic = $request->has('include_installation_in_basic') && in_array($request->include_installation_in_basic, [1, '1', true, 'on', 'true'], true);
                $includeFreightInBasic = $request->has('include_freight_in_basic') && in_array($request->include_freight_in_basic, [1, '1', true, 'on', 'true'], true);

                if ($includeFreightInBasic) {
                    $freightApproval = 1;
                }

                $isMercuriisInstallation = false;
                if ($installationId) {
                    $instModel = QuotationInstallationScope::find($installationId);
                    if ($instModel && stripos($instModel->name, 'Mercuriis') !== false) {
                        $isMercuriisInstallation = true;
                    }
                }

                $isMercuriisUnloading = false;
                if ($unloadingId) {
                    $unlModel = QuotationUnloadingOption::find($unloadingId);
                    if ($unlModel && (stripos($unlModel->name, 'Mercuriis') !== false || stripos($unlModel->name, 'Mecuriis') !== false)) {
                        $isMercuriisUnloading = true;
                        $freightApproval = 1;
                    }
                }

                $userInstallCharge = (float) ($request->installation_charge ?? $defaultInstallCharge);
                $defaultFreightCharge = 10000.00;
                $userFreightCharge = (float) ($request->freight_charge ?? $defaultFreightCharge);

                $additionalWarrantyMonths = (int) ($request->additional_warranty_months ?? 0);
                $additionalWarrantyPct = (float) ($request->additional_warranty_percentage ?? 0.00);
                $additionalWarrantyCharge = (float) ($request->additional_warranty_charge ?? 0.00);
                $includeWarrantyInBasic = $request->has('include_warranty_in_basic') ? 1 : 0;

                $instTax = $userInstallCharge * 0.18;
                $freightTax = $userFreightCharge * 0.18;
                $warrantyTax = $additionalWarrantyCharge * 0.18;

                $subtotal += $userInstallCharge + $userFreightCharge + $additionalWarrantyCharge;
                $taxTotal += $instTax + $freightTax + $warrantyTax;
                $grandTotal += ($userInstallCharge + $instTax) + ($userFreightCharge + $freightTax) + ($additionalWarrantyCharge + $warrantyTax);

                $chargesDiffer = ($userInstallCharge != $defaultInstallCharge) || ($userFreightCharge != $defaultFreightCharge) || $includeInstInBasic || $isMercuriisInstallation;
                $chargeApprovalStatus = $chargesDiffer ? 'Pending Approval' : 'Approved';
                $maxReqDis = collect($request->requestdis ?? [])->map(fn($v) => (float)$v)->max() ?? 0.00;
                $maxAllowDis = collect($request->allowdis ?? [])->map(fn($v) => (float)$v)->max() ?? 0.00;
                $sumReqDisAmt = collect($request->requestdis_amount ?? [])->map(fn($v) => (float)$v)->sum() ?? 0.00;

                $quotation->update([
                    'subtotal' => $subtotal,
                    'tax_total' => $taxTotal,
                    'grand_total' => $grandTotal,
                    'allow_dis' => $maxAllowDis,
                    'req_dis' => $maxReqDis,
                    'reqdis_amt' => $sumReqDisAmt,
                    'dis_app' => $needsApproval ? 1 : 2,
                    'freight_approval' => $freightApproval,
                    'quto_app' => ($needsApproval || $freightApproval || $chargeApprovalStatus === 'Pending Approval') ? 0 : 2,
                    'default_installation_charge' => $defaultInstallCharge,
                    'installation_charge' => $userInstallCharge,
                    'include_installation_in_basic' => $includeInstInBasic ? 1 : 0,
                    'default_freight_charge' => $defaultFreightCharge,
                    'freight_charge' => $userFreightCharge,
                    'include_freight_in_basic' => $includeFreightInBasic ? 1 : 0,
                    'additional_warranty_months' => $additionalWarrantyMonths,
                    'additional_warranty_percentage' => $additionalWarrantyPct,
                    'additional_warranty_charge' => $additionalWarrantyCharge,
                    'include_warranty_in_basic' => $includeWarrantyInBasic,
                    'charge_approval_status' => $chargeApprovalStatus,
                ]);

                if ($chargeApprovalStatus === 'Pending Approval') {
                    $chargeRemarks = [];
                    if ($userInstallCharge != $defaultInstallCharge) $chargeRemarks[] = 'Inst: ₹' . number_format($userInstallCharge, 2);
                    if ($userFreightCharge != $defaultFreightCharge) $chargeRemarks[] = 'Freight: ₹' . number_format($userFreightCharge, 2);
                    if ($includeInstInBasic) $chargeRemarks[] = 'Inst Included in Basic';
                    if ($isMercuriisInstallation) $chargeRemarks[] = 'Mercuriis Installation Scope';

                    \App\Models\QuotationChargeApprovalHistory::create([
                        'quotation_id' => $quotation->id,
                        'action' => 'Submitted Custom Charges',
                        'status' => 'Pending Approval',
                        'default_installation_charge' => $defaultInstallCharge,
                        'installation_charge' => $userInstallCharge,
                        'default_freight_charge' => $defaultFreightCharge,
                        'freight_charge' => $userFreightCharge,
                        'acted_by' => auth()->id(),
                        'remarks' => 'Charge approval required (' . implode(', ', $chargeRemarks) . ')',
                    ]);
                }
            }
            $termsConditionId = $this->resolveSegmentTermId($lead, $request->termscondition, 1, 'termscondition');
            $warrantyId = $this->resolveSegmentTermId($lead, $request->warranty, 2, 'warranty');

            $hasTermApprovalRequest = false;

            $savedPaymentTerm = Term::find($termsConditionId);
            if ($request->termscondition === 'new' && $savedPaymentTerm && $savedPaymentTerm->term_approve != 0) {
                \App\Models\QuotationTermChangeRequest::create([
                    'quotation_id' => $quotation->id,
                    'type'         => 'payment_term',
                    'old_term_id'  => null,
                    'new_term_id'  => $termsConditionId,
                    'status'       => 'Pending Approval',
                    'requested_by' => auth()->id(),
                    'remarks'      => 'New Payment Term requested: ' . request('newtermscondition'),
                ]);
                $hasTermApprovalRequest = true;
            }

            $savedWarrantyTerm = Term::find($warrantyId);
            if ($request->warranty === 'new' && $savedWarrantyTerm && $savedWarrantyTerm->term_approve != 0) {
                \App\Models\QuotationTermChangeRequest::create([
                    'quotation_id' => $quotation->id,
                    'type'         => 'warranty',
                    'old_term_id'  => null,
                    'new_term_id'  => $warrantyId,
                    'status'       => 'Pending Approval',
                    'requested_by' => auth()->id(),
                    'remarks'      => 'New Warranty Term requested: ' . request('newwarcondition'),
                ]);
                $hasTermApprovalRequest = true;
            }

            if ($hasTermApprovalRequest) {
                $quotation->update(['quto_app' => 0]);
            }

            $dispatchTimeValue = $request->dispatch_time;

            if (empty($dispatchTimeValue) && $request->filled('product_id')) {
                $selectedProductId = collect($request->product_id)->filter()->first();
                $prod = Product::find($selectedProductId);
                if ($prod) {
                    $dispatchTimeValue = $prod->dispatch_time;
                    if (empty($dispatchTimeValue)) {
                        $cat = ProductCategory::find($prod->Productcategory);
                        if ($cat && !empty($cat->product_keyword)) {
                            $kwList = json_decode($cat->product_keyword, true);
                            $dispList = json_decode($cat->keyword_dispatch_time, true);
                            if (!is_array($kwList)) {
                                $kwList = [$cat->product_keyword];
                            }
                            if (!is_array($dispList)) {
                                $dispList = [$cat->keyword_dispatch_time];
                            }
                            foreach ($kwList as $kIndex => $kw) {
                                $kwStr = trim((string)$kw);
                                if (!empty($kwStr) && stripos($prod->productname, $kwStr) !== false) {
                                    $dispatchTimeValue = trim((string)($dispList[$kIndex] ?? '')) ?: 'Within a Week';
                                    break;
                                }
                            }
                        }
                        if (empty($dispatchTimeValue) && stripos($prod->productname, 'Hydraulic Hand Pallet') !== false) {
                            $dispatchTimeValue = 'Within a Week';
                        }
                    }
                }
            }

            if (empty($dispatchTimeValue) && $request->filled('product_category_id')) {
                $selectedCategoryId = collect($request->product_category_id)->filter()->first();
                $dispatchTimeValue = ProductCategory::where('id', $selectedCategoryId)->value('dispatch_time');
            }

            /* -------------------------------------------------
             | 5️⃣ FREIGHT
             ------------------------------------------------- */

            if ($request->dov == 'new' && $request->dov_new) {

                // Save new DOV into master table
                $dovValue = $request->dov_new;

                DB::table('date_of_validities')->insert([
                    'value' => $dovValue,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            } else {
                $dovValue = $request->dov;
            }


            Freight::create([
                'leadnumber' => $request->leadno,
                'quotationno' => $quotation->id,
                'termscondition' => $termsConditionId,
                // 'dov'                     => $request->dov,
                'dov' => $dovValue,
                'dispatch_time' => $dispatchTimeValue,
                'freight_category_id' => $freightCatLastId,
                'freight_sub_category_id' => $freightSubCatLastId,
                'freight_detail_id' => $freightDetailLastId,
                'unloading' => $unloadingId,
                'remarks' => $request->remarks,
                'currency' => $request->currency,
                'installation' => $installationId,
                'agent' => $agentId,
                'amount' => $request->ammt,
                'warranty' => $warrantyId,
                'freight_approve' => $freightApproval,
            ]);

            if ($selectedQuotationStatus) {
                $lead->quto_status = $selectedQuotationStatus;
                $lead->quotastatus_remarks = $request->quto_status_remarks;
                $lead->qutostatus_date = now();
                $lead->save();
            }

            DB::commit();

            return redirect()
                ->route('single.quota', ['leadid' => $request->leadno])
                ->with('success', "Quotation saved as {$quotationNo}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    private function resolveQuotationMasterOption($modelClass, $selectedValue, $newValue, $hasActiveFlag = false)
    {
        if ($selectedValue !== 'new') {
            return $selectedValue ?: null;
        }

        $name = trim((string) $newValue);

        if ($name === '') {
            return null;
        }

        $data = ['name' => $name];

        if ($hasActiveFlag) {
            $data['sort_order'] = $modelClass::max('sort_order') + 1;
            $data['is_active'] = true;
        }

        return $modelClass::firstOrCreate(['name' => $name], $data)->id;
    }


    /**
     * Check if all approvals (Terms, Charges, Freight, Discount) are completed for a quotation.
     */
    public function getQuotationApprovalStatus($quotationId): array
    {
        $quotation = Quotation::find($quotationId);
        if (!$quotation) {
            return [
                'approved' => false,
                'message' => 'Quotation not found.',
                'pending_reasons' => ['Quotation not found.'],
            ];
        }

        $pendingReasons = [];

        // 1. Payment Terms / Warranty approval in QuotationTermChangeRequest
        $termChangePending = \App\Models\QuotationTermChangeRequest::where('quotation_id', $quotation->id)
            ->where('status', 'Pending Approval')
            ->exists();
        if ($termChangePending) {
            $pendingReasons[] = 'Payment / Warranty Terms approval is pending.';
        }

        $termChangeRejected = \App\Models\QuotationTermChangeRequest::where('quotation_id', $quotation->id)
            ->where('status', 'Rejected')
            ->exists();
        if ($termChangeRejected) {
            $pendingReasons[] = 'Payment / Warranty Terms was rejected.';
        }

        // 2. Terms in Freight record
        $freight = \App\Models\Freight::where('quotationno', $quotation->id)->first();
        if ($freight) {
            $termApprovePending = \App\Models\Term::where(function ($query) use ($freight) {
                $query->where('id', $freight->termscondition)
                      ->orWhere('id', $freight->warranty);
            })->where('term_approve', 1)->exists();

            if ($termApprovePending) {
                $pendingReasons[] = 'Custom Payment / Warranty Terms approval is pending.';
            }

            $termApproveRejected = \App\Models\Term::where(function ($query) use ($freight) {
                $query->where('id', $freight->termscondition)
                      ->orWhere('id', $freight->warranty);
            })->where('term_approve', 2)->exists();

            if ($termApproveRejected) {
                $pendingReasons[] = 'Payment / Warranty Terms was rejected.';
            }
        }

        // 3. Charge approval (Freight / Installation below default)
        if (in_array($quotation->charge_approval_status, ['Pending Approval', 'Pending'])) {
            $pendingReasons[] = 'Freight / Installation charge approval is pending.';
        } elseif ($quotation->charge_approval_status === 'Rejected') {
            $pendingReasons[] = 'Freight / Installation charge was rejected.';
        }

        // 4. Discount approval
        if ($quotation->dis_app == 1) {
            $pendingReasons[] = 'Discount approval is pending.';
        } elseif ($quotation->dis_app == 0) {
            $pendingReasons[] = 'Discount was rejected.';
        }

        // 5. Freight approval
        if ($quotation->freight_approval == 1) {
            $pendingReasons[] = 'Freight approval is pending.';
        } elseif ($quotation->freight_approval == 2) {
            $pendingReasons[] = 'Freight was rejected.';
        }

        // 6. Overall Quotation Approval
        if ($quotation->quto_app != 2 && empty($pendingReasons)) {
            $pendingReasons[] = 'Quotation approval is pending.';
        }

        $isApproved = empty($pendingReasons) && ($quotation->quto_app == 2);

        return [
            'approved' => $isApproved,
            'pending_reasons' => $pendingReasons,
            'message' => $isApproved ? 'Quotation is approved.' : implode(' ', $pendingReasons),
        ];
    }

    public function generatePDF($id)
    {
        $isSuperAdmin = auth()->check() && auth()->user()->role === 'Super Admin';
        if (!$isSuperAdmin) {
            $status = $this->getQuotationApprovalStatus((int) $id);
            if (!$status['approved']) {
                return redirect()->back()->with('error', 'Cannot generate quotation PDF: ' . $status['message']);
            }
        }

        $data = app(QuotationDocumentService::class)->build((int) $id);
        $data['pdfMode'] = true;
        $safeFilename = str_replace('/', '-', $data['quotation']->quota_id);

        return Pdf::loadView('quotations.pdf', $data)
            ->setPaper('a4')
            ->stream($safeFilename . '.pdf');
    }

    public function viewQuotation($quotationId)
    {
        $isSuperAdmin = auth()->check() && auth()->user()->role === 'Super Admin';
        if (!$isSuperAdmin) {
            $status = $this->getQuotationApprovalStatus((int) $quotationId);
            $isApprover = auth()->check() && in_array(auth()->user()->role, ['Super Admin', 'Admin', 'Director', 'Manager', 'Sales Manager'], true);
            $hasDisplayPerm = auth()->check() && auth()->user()->can('quotation-display');

            if (!$status['approved'] && !$isApprover && !$hasDisplayPerm) {
                return redirect()->back()->with('error', 'Cannot view quotation document: ' . $status['message']);
            }
        }

        return view('quotations.pdf', app(QuotationDocumentService::class)->build((int) $quotationId));
    }

    public function downloadQuotation($id)
    {
        $isSuperAdmin = auth()->check() && auth()->user()->role === 'Super Admin';
        if (!$isSuperAdmin) {
            $status = $this->getQuotationApprovalStatus((int) $id);
            $hasDisplayPerm = auth()->check() && auth()->user()->can('quotation-display');
            if (!$status['approved'] && !$hasDisplayPerm) {
                return redirect()->back()->with('error', 'Cannot download quotation: ' . $status['message']);
            }
        }

        $data = app(QuotationDocumentService::class)->build((int) $id);
        $data['pdfMode'] = true;
        $safeFilename = str_replace('/', '-', $data['quotation']->quota_id);

        return Pdf::loadView('quotations.pdf', $data)
            ->setPaper('a4')
            ->download($safeFilename . '.pdf');
    }

    public function createorf($id)
    {
        $isSuperAdmin = auth()->check() && auth()->user()->role === 'Super Admin';
        if (!$isSuperAdmin) {
            $status = $this->getQuotationApprovalStatus((int) $id);
            if (!$status['approved']) {
                return redirect()->back()->with('error', 'Cannot create ORF: ' . $status['message']);
            }
        }

        $quto = Quotation::with(['lead'])
            ->where('id', $id)
            ->firstOrFail();

        $estimates = QuotationProduct::where('quotationno', $quto->quota_id)->get();

        // Check if category is SPECIAL
        $isSpecialCategory = DB::table('product_categories_tb')
            ->where('id', $quto->lead->category)
            ->where('sub_category', 'special')
            ->exists();

        // Load products ONLY if special
        $specialProducts = collect();

        if ($isSpecialCategory) {
            $specialProducts = DB::table('products')
                ->join('product_categories_tb', 'products.Productcategory', '=', 'product_categories_tb.id')
                ->where('product_categories_tb.id', $quto->lead->category)
                ->where('product_categories_tb.sub_category', 'special')
                ->select('products.id', 'products.productname')
                ->orderBy('products.productname')
                ->get();
        }

        return view(
            'pages.orfform',
            compact('quto', 'estimates', 'specialProducts', 'isSpecialCategory')
        );
    }




    public function moveQuotationApproval()
    {
        $leads = Lead::select(
            'leads.id',
            'leads.lead_id',
            'product_categories_tb.category_name as category',
            'products.productname as product',
            'leads.quota_doc',
            'leads.quota_proceed'
        )
            // ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
            // ->leftJoin('products', 'leads.products', '=', 'products.id')
            ->leftJoin('designs', 'designs.idlead', '=', 'leads.id')     // ✅ JOIN DESIGNS
            ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
            ->leftJoin('products', 'designs.product', '=', 'products.id')
            ->where('leads.quota_proceed', '=', 0)
            ->get();

        return view('pages.movetoquotation', compact('leads'));
    }

    // public function approvemovequto(Request $request, $id)
    // {
    //     $lead = Lead::find($id);

    //     if ($lead) {
    //         $lead->quota_proceed = 1;
    //         $lead->save();

    //         return response()->json(['message' => 'Lead approved successfully']);
    //     }

    //     return response()->json(['message' => 'Lead not found'], 404);
    // }

    public function approvemovequto(Request $request, $id)
    {
        $lead = Lead::select(
            'leads.id',
            'leads.lead_id',
            'product_categories_tb.category_name as category',
            'products.productname as product',
            'leads.quota_doc',
            'leads.quota_proceed',
            'leads.assigned_to'
        )
            ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
            ->leftJoin('products', 'leads.products', '=', 'products.id')
            ->where('leads.id', $id)
            ->first();

        if ($lead) {
            // Update the quota_proceed status
            $lead->quota_proceed = 1;
            $lead->save();

            // Prepare mail data
            $mailData = [
                'lead_id' => $lead->lead_id,
                'category' => $lead->category,
                'product' => $lead->product,
                'quota_doc' => $lead->quota_doc,
            ];

            // Fetch the auth user email
            $authUserEmail = Auth::user()->email;

            // Fetch the assigned_to user email
            $assignedUser = User::find($lead->assigned_to);
            $assignedUserEmail = $assignedUser ? $assignedUser->email : null;

            // Send emails to both auth user and assigned_to user
            Mail::to($authUserEmail)
                ->cc($assignedUserEmail)
                ->send(new MoveQutoMail($mailData));

            return response()->json(['message' => 'Lead approved and emails sent successfully']);
        }

        return response()->json(['message' => 'Lead not found'], 404);
    }

    public function viewQuotationApproval()
    {
        $quotations = Quotation::with([
            'lead.categoryDetails',
            'freight.freightCategory',
            'freight.freightSubCategory',
            'freight.freightDetail',
            'freight.termsCondition',
            'freight.warrantyTerm',
            'freight.installationScope',
            'freight.unloadingOption',
            'termChangeRequests.oldTerm',
            'termChangeRequests.newTerm',
            'termChangeRequests.requester',
            'quotationProducts',
        ])
        ->whereRaw('quotations.id = (
            SELECT MAX(q2.id) 
            FROM quotations as q2 
            WHERE q2.id_lead = quotations.id_lead
        )')
        ->where(function ($q) {
            $q->where('quto_app', 0)
              ->orWhere('dis_app', 1)
              ->orWhere('freight_approval', 1)
              ->orWhere('charge_approval_status', 'Pending Approval')
              ->orWhereHas('termChangeRequests', function ($tcr) {
                  $tcr->where('status', 'Pending Approval');
              })
              ->orWhereHas('freight', function ($f) {
                  $f->where('freight_approve', 1);
              });
        })
        ->latest('id')
        ->get();

        return view('pages.qutoapproval', compact('quotations'));
    }

    // public function viewQuotationApproval()
    // {
    //     $latestQuotationProduct = DB::table('quotationproduct as qp1')
    //         ->select('qp1.*')
    //         ->whereRaw('qp1.id = (
    //         SELECT MAX(qp2.id)
    //         FROM quotationproduct as qp2
    //         WHERE qp2.leadno = qp1.leadno
    //     )');

    //     $quotationCountSub = DB::table('quotationproduct')
    //         ->select('leadno', DB::raw('COUNT(*) as quotation_count'))
    //         ->groupBy('leadno');

    //     $quotations = DB::table('leads')
    //         ->select(
    //             'latest_qp.id as quotation_id',
    //             'latest_qp.quotationno',
    //             'latest_qp.catepro',
    //             'latest_qp.dis_app',
    //             'latest_qp.requestdis',
    //             'products.productname as product',
    //             'freight.id as freight_id',
    //             'freight.freight_approve',
    //             'freight_category.categoty as freight_category',
    //             'freight_details.details as freight_detail',
    //             'freight_sub_category.sub_category as freight_sub_category',
    //             'freight.termscondition as termsid',
    //             'freight.warranty as warrantyid',
    //             'term_condition.whichterm as term_type',
    //             'term_condition.content as term_content',
    //             'term_condition.term_approve',
    //             'warranty_term.whichterm as warranty_type',
    //             'warranty_term.content as warranty_content',
    //             'warranty_term.term_approve as warranty_approve',
    //             'leads.*',
    //             DB::raw('(SELECT COUNT(*) FROM quotationproduct AS qp
    //               WHERE qp.quotationno = latest_qp.quotationno
    //               AND qp.created_at <= latest_qp.created_at) AS quote_count')
    //         )
    //         ->leftJoinSub($latestQuotationProduct, 'latest_qp', function ($join) {
    //             $join->on('latest_qp.leadno', '=', 'leads.id');
    //         })
    //         ->leftJoinSub($quotationCountSub, 'quotation_count', function ($join) {
    //             $join->on('quotation_count.leadno', '=', 'leads.id');
    //         })
    //         ->leftJoin('products', 'latest_qp.product', '=', 'products.id')
    //         ->leftJoin('freight', 'freight.quotationno', '=', 'latest_qp.id')
    //         ->leftJoin('terms as term_condition', 'term_condition.id', '=', 'freight.termscondition')
    //         ->leftJoin('terms as warranty_term', 'warranty_term.id', '=', 'freight.warranty')
    //         ->leftJoin('freight_details', 'freight.freight_detail_id', '=', 'freight_details.id')
    //         ->leftJoin('freight_category', 'freight.freight_category_id', '=', 'freight_category.id')
    //         ->leftJoin('freight_sub_category', 'freight.freight_sub_category_id', '=', 'freight_sub_category.id')
    //         ->where(function ($query) {
    //             $query->where('latest_qp.quto_app', 0)
    //                 ->orWhere('latest_qp.freight_approval', 1);
    //         })
    //         ->where('term_condition.term_approve',0)
    //         ->where('warranty_term.term_approve',0)
    //         ->where('latest_qp.dis_app',1)
    //         ->orderBy('latest_qp.created_at')
    //         ->get();

    //     // dd($quotations);

    //     return view('pages.qutoapproval', compact('quotations'));

    // }

    public function approvedisc(Request $request, $id)
    {
        try {
            $quotation = Quotation::findOrFail($id);

            $discStatus = $request->input('disc_status');
            $disRemark = $request->input('remarks');
            $revisedDiscountValue = $request->input('revisedDiscount');

            if ($discStatus === 'yes') {
                $quotation->dis_app = 2;
                $quotation->disremark = null;

                $finalDiscount = ($revisedDiscountValue !== null && $revisedDiscountValue !== '') 
                    ? (float) $revisedDiscountValue 
                    : (float) ($quotation->effective_requested_discount ?? 0);

                $quotation->revised_discount = $finalDiscount;
                $quotation->req_dis = $finalDiscount;

                $hasOtherPending = ($quotation->freight_approval == 1)
                    || in_array($quotation->charge_approval_status, ['Pending Approval', 'Pending'])
                    || \App\Models\QuotationTermChangeRequest::where('quotation_id', $quotation->id)->where('status', 'Pending Approval')->exists();

                $quotation->quto_app = $hasOtherPending ? 0 : 2;

                $qpItems = QuotationProduct::where('quotationno', $quotation->quota_id)->get();
                $totalLbt = 0;
                $totalTax = 0;
                $totalDisAmt = 0;

                if ($qpItems->isNotEmpty()) {
                    foreach ($qpItems as $qp) {
                        $rate = (float) $qp->ratee;
                        $qty = (float) ($qp->quanty ?: 1);
                        $disAmt = ($rate * $finalDiscount / 100) * $qty;
                        $newLbt = ($rate * $qty) - $disAmt;
                        $taxRate = (float) ($qp->tax ?? 18);
                        $newTaxAmt = $newLbt * ($taxRate / 100);
                        $newGrandTotal = $newLbt + $newTaxAmt;

                        $qp->update([
                            'revised_discount' => $finalDiscount,
                            'revised_disamt' => $disAmt,
                            'requestdis' => $finalDiscount,
                            'requestdis_amt' => $disAmt,
                            'lbt' => $newLbt,
                            'taxamt' => $newTaxAmt,
                            'grandtotal' => $newGrandTotal,
                            'dis_app' => 2,
                            'disremark' => null,
                            'quto_app' => $hasOtherPending ? 0 : 2,
                        ]);

                        $totalDisAmt += $disAmt;
                        $totalLbt += $newLbt;
                        $totalTax += $newTaxAmt;
                    }

                    $customSubtotal = (float)$quotation->installation_charge + (float)$quotation->freight_charge + (float)$quotation->additional_warranty_charge;
                    $customTax = $customSubtotal * 0.18;

                    $quotation->reqdis_amt = $totalDisAmt;
                    $quotation->subtotal = $totalLbt + $customSubtotal;
                    $quotation->tax_total = $totalTax + $customTax;
                    $quotation->grand_total = $quotation->subtotal + $quotation->tax_total;
                } else {
                    $basic = (float) ($quotation->basic_total ?? 0);
                    $disAmt = ($basic * $finalDiscount / 100);
                    $newLbt = $basic - $disAmt;
                    $customSubtotal = (float)$quotation->installation_charge + (float)$quotation->freight_charge + (float)$quotation->additional_warranty_charge;
                    $subtotal = $newLbt + $customSubtotal;
                    $taxTotal = $subtotal * 0.18;

                    $quotation->reqdis_amt = $disAmt;
                    $quotation->subtotal = $subtotal;
                    $quotation->tax_total = $taxTotal;
                    $quotation->grand_total = $subtotal + $taxTotal;
                }
            } else {
                $quotation->dis_app = 0;
                $quotation->disremark = $disRemark;
                $quotation->quto_app = 0;

                QuotationProduct::where('quotationno', $quotation->quota_id)->update([
                    'dis_app' => 0,
                    'disremark' => $disRemark,
                    'quto_app' => 0,
                ]);
            }

            $quotation->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Discount decision saved successfully'
            ]);
        } catch (\Throwable $e) {
            \Log::error('Discount approval error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function approvefre(Request $request, $id)
    // {
    //     $quotation = Freight::findOrFail($id);

    //     $fre_status = $request->input('fre_status');
    //     $freremark = $request->input('freremark');
    //     // $revisedDiscountValue = $request->revisedDiscount;

    //     $quotation->dis_app = ($fre_status == 'yes') ? 2 : 0;

    //     if ($fre_status == 'no') {
    //         $quotation->freightextra = $freremark;
    //     } else {
    //         $quotation->freremark = null;
    //         if (!empty($revisedDiscountValue)) {
    //             $quotation->revised_discount = $revisedDiscountValue;
    //         }

    //     }

    //     $quotation->save();
    //          return response()->json(['message' => 'Freight status updated successfully']);
    // }
    public function approvefre(Request $request, $id)
    {
        $freight = Freight::findOrFail($id);

        $fre_status = $request->input('fre_status');
        $remark = $request->input('remarks');
        $freightText = $request->input('freight');     // new freight category name
        $included = $request->input('included');       // new subcategory name
        $extra = $request->input('extra');             // new detail name

        // Set freight approval status
        $freight->freight_approve = ($fre_status === 'yes') ? 0 : 1;

        // Set remarks
        $freight->freightextra = ($fre_status === 'no') ? $remark : null;

        // ✅ Update related tables (category, subcategory, and details)
        // 1. Find the freight_detail
        $freightDetail = FreightDetail::find($freight->freight_detail_id);

        if ($freightDetail) {
            // 2. Update `freight_details.details`
            $freightDetail->details = $extra;
            $freightDetail->save();

            // 3. Update `freight_sub_category.sub_category`
            $subCategory = FreightSubCategory::find($freightDetail->freight_sub_cat_id);
            if ($subCategory) {
                $subCategory->sub_category = $included;
                $subCategory->save();
            }

            // 4. Update `freight_category.categoty`
            $category = FreightCategory::find($freightDetail->freight_cat_id);
            if ($category) {
                $category->categoty = $freightText;
                $category->save();
            }
        }

        $freight->save();

        $quotation = Quotation::find($freight->quotationno) ?: QuotationProduct::find($freight->quotationno);

        if ($quotation) {
            $qutoAppValue = $quotation->quto_app;

            if ($fre_status === 'yes') {
                $hasOtherPending = \App\Models\QuotationTermChangeRequest::where('quotation_id', $quotation->id)->where('status', 'Pending Approval')->exists()
                    || $quotation->dis_app == 1
                    || in_array($quotation->charge_approval_status, ['Pending Approval', 'Pending']);
                $qutoAppValue = $hasOtherPending ? 0 : 2;
                $quotation->freight_approval = 0; // 0 = approved
            } else {
                $quotation->freight_approval = 2; // 2 = rejected
                $qutoAppValue = 0;
            }

            $quotation->quto_app = $qutoAppValue;
            $quotation->save();
        }

        return response()->json(['message' => 'Freight details updated successfully']);
    }

    public function approveTermsFunctions(Request $request)
    {
        $terms_id = $request->terms_id;
        $warrenty_id = $request->warrenty_id;
        $term_content = $request->term_content;
        $warranty_content = $request->warranty_content;
        $terms_status = $request->terms_status;

        if (!empty($request->terms_id)) {
            $termsTb = Term::findOrFail($terms_id);
            if ($termsTb) {
                if ($terms_status === 'yes') {
                    $termsTb->term_approve = 0;
                    $termsTb->content = $term_content;
                } else {
                    $termsTb->term_approve = 2;
                }
                $termsTb->save();
            }
        }

        if (!empty($request->warrenty_id)) {
            $warrentyTb = Term::findOrFail($warrenty_id);
            if ($warrentyTb) {
                if ($terms_status === 'yes') {
                    $warrentyTb->term_approve = 0;
                    $warrentyTb->content = $warranty_content;
                } else {
                    $warrentyTb->term_approve = 2;
                }
                $warrentyTb->save();
            }
        }

        return response()->json(['message' => 'Terms and conditions Approved']);

    }

    public function approvequto(Request $request, $id)
    {
        $quotation = QuotationProduct::findOrFail($id);

        $qutoStatus = $request->input('quto_status');
        $qutoRemark = $request->input('remarks');

        $quotation->quto_app = ($qutoStatus == 'yes') ? 2 : 1;
        if ($qutoStatus == 'no') {
            $quotation->qutoremark = $qutoRemark;
        } else {
            $quotation->qutoremark = null; // Optional: clear remarks on approval
        }
        $quotation->save();

        $authUserEmail = auth()->user()->email;

        $lead = Lead::where('id', $quotation->leadno)->first();
        // $assignedToEmail = $lead ? $lead->assigned_to : null;
        $assignedToEmail = $lead ? User::where('id', $lead->assigned_to)->value('email') : null;

        $product = Product::find($quotation->product);
        $productName = $product ? $product->productname : 'N/A';

        $mailData = [
            'quotation_no' => $quotation->quotationno,
            'status' => $qutoStatus == 'yes' ? 'Approved' : 'Revision',
            'product_name' => $productName,
            'category' => $quotation->catepro,
            'date' => now()->format('Y-m-d H:i:s'),
        ];

        try {
            if ($assignedToEmail) {
                Mail::to($authUserEmail)
                    ->cc($assignedToEmail)
                    ->send(new QuotationApprovedMail($mailData));
            } else {
                Mail::to($authUserEmail)
                    ->send(new QuotationApprovedMail($mailData));
            }

            return response()->json(['message' => 'Quotation status updated successfully and email sent']);
        } catch (Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());  // ✅ Log error message
            return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 500);
        }
    }
    public function leadFilterFunction($statusId)
    {
        $user = auth()->user();

        $leadList = DB::table('leads')
            ->select([
                'leads.*',
                'source.dropdowndata as source_name',
                'status.dropdowndata as status_name',
                'status.id as status_id',
                'city_list.city_name',
                'users.name',
                'users.role'
            ])
            ->leftJoin('dropdowndatas as source', 'source.id', '=', 'leads.Leadsource')
            ->leftJoin('dropdowndatas as status', 'status.id', '=', 'leads.Leadstatus')
            ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
            ->leftJoin('users', 'users.id', '=', 'leads.assigned_to');

        // ✅ Apply role-based access
        if ($user->role !== 'Super Admin') {
            $leadList = $leadList->where('leads.assigned_to', $user->id);
        }

        // ✅ Apply status filter
        if ($statusId != "0") {
            $leadList = $leadList->where('leads.Leadstatus', '=', $statusId);
        } else {
            $leadList = $leadList->where(function ($query) {
                $query->whereNotIn('leads.Leadstatus', [343, 351])
                    ->orWhereNull('leads.Leadstatus');
            });
        }

        $leadList = $leadList->orderBy('leads.created_at', 'desc')->get();

        // Add action buttons
        $leadList = $leadList->map(function ($lead) {
            $editUrl = url('/lead-edit/' . $lead->id);

            $action = '';
            if (auth()->user()->can('leads-edit')) {
                $action .= "<a class='btn btn-warning mb-2' href='{$editUrl}'>Edit</a>";
            }

            $lead->action = $action;
            return $lead;
        });


        return response()->json([
            'status' => 'success',
            'message' => 'Lead Filter Response',
            'data' => $leadList,
            'role' => $user->role
        ]);
    }

    public function getByCategory($categoryId, $subCategoryId)
    {
        $products = Product::where('Productcategory', $categoryId)->where('sub_cat_id', $subCategoryId)->get(['id', 'productname']);
        return response()->json(['products' => $products]);
    }

    // public function filterQuotation(Request $request)
    // {
    //     $categoryId = $request->get('category_id');
    //     $subCategoryId = $request->get('sub_category_id');
    //     $productId = $request->get('product_id');
    //     $statusId = $request->get('status_id');

    //     $query = Lead::select([
    //         'leads.id',
    //         'leads.LeadName',
    //         'leads.contactname',
    //         'leads.phonumber',
    //         'leads.email',
    //         'city_list.city_name',
    //         'users.name as assigned_to',
    //         'quotation_status.name as quota_status',
    //         'quotation_status.id as quotation_status_id',
    //         'quotations.quota_id',
    //         'quotations.created_at as quote_date',
    //         'product_categories_tb.category_name',
    //         'product_sub_categories.subcategory_name',
    //         'products.productname',
    //         'products.rate',
    //         'quotationproduct.grandtotal',
    //         'quotationproduct.id as quot_product_id',
    //         'quotationproduct.dis_app',
    //     ])
    //         ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
    //         ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
    //         ->leftJoin('quotation_status', 'quotation_status.id', '=', 'leads.quto_status')
    //         ->leftJoin('quotations', 'quotations.id_lead', '=', 'leads.id')
    //         ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
    //         ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
    //         ->leftJoin('products', 'products.id', '=', 'leads.products')
    //         ->leftJoin('quotationproduct', function ($join) {
    //             $join->on('quotationproduct.leadno', '=', 'leads.id')
    //                 ->whereRaw('quotationproduct.id = (SELECT MAX(id) FROM quotationproduct WHERE leadno = leads.id)');
    //         })
    //         ->where('leads.Leadstatus', 343)
    //         ->where('leads.quota_proceed', 1);

    //     if (!empty($categoryId) && $categoryId != 0) {
    //         $query->where('leads.category', $categoryId);
    //     }

    //     if (!empty($subCategoryId) && $subCategoryId != 0) {
    //         $query->where('leads.subcategory_id', $subCategoryId);
    //     }

    //     if (!empty($productId) && $productId != 0) {
    //         $query->where('leads.products', $productId);
    //     }

    //     if (!empty($statusId) && $statusId != 0) {
    //         $query->where('leads.quto_status', $statusId);
    //     }


    //     $data = $query->get()->map(function ($item) {
    //         // Action logic (same as main table)
    //         $quotationExists = QuotationProduct::where('leadno', $item->id)->exists();
    //         $action = "-";

    //         if (!$quotationExists) {
    //             $action = "<a href='/createquota/{$item->id}'><button class='btn btn-success btn-sm'>Create</button></a>";
    //         }

    //         if (!empty($item->grandtotal)) {
    //             $action = "<div class='quota-action-btns'>
    //             <a href='/editquota/{$item->quot_product_id}'><button class='btn btn-warning btn-sm'>Edit</button></a>";

    //             if ($item->dis_app == 2) {
    //                 $action .= "<a href='/orf/{$item->quot_product_id}'><button class='btn btn-primary btn-sm'>Move to ORF</button></a>";
    //             }

    //             $action .= "<a href='/quotation/view/{$item->quot_product_id}' target='_blank'><button class='btn btn-info btn-sm'>View</button></a>
    //         </div>";
    //         }

    //         return [
    //             'id' => $item->id,
    //             'quote_date' => $item->quote_date ? Carbon::parse($item->quote_date)->format('d-m-Y') : "-",
    //             'quota_id' => $item->quota_id ?? "-",
    //             'LeadName' => "<a href='/singlequota/{$item->id}'>{$item->LeadName}</a>",
    //             'city_name' => $item->city_name ?? "-",
    //             'contactname' => $item->contactname ?? "-",
    //             'phonumber' => $item->phonumber ?? "-",
    //             'category_name' => $item->category_name ?? "-",
    //             'subcategory_name' => $item->subcategory_name ?? "-",
    //             'productname' => $item->productname ?? "-",
    //             'grandtotal' => "<b style='color: #dc3545'>" . ($item->grandtotal ?? '-') . "</b>",
    //             'assigned_to' => $item->assigned_to ?? "-",
    //             'quota_status' => $item->quota_status ?? "-",
    //             'quotation_status_id' => $item->quotation_status_id ?? "",
    //             'action' => $action,
    //         ];
    //     });

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $data
    //     ]);
    // }

    public function filterQuotation(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategoryId = $request->sub_category_id;
        $productId = $request->product_id;
        $statusId = $request->status_id;

        $latestQuotationProduct = DB::table('quotationproduct as qp1')
            ->select('qp1.*')
            ->whereRaw('qp1.id = (
            SELECT MAX(qp2.id)
            FROM quotationproduct as qp2
            WHERE qp2.leadno = qp1.leadno
        )');

        $latestQuotation = DB::table('quotations as q1')
            ->select('q1.*')
            ->whereRaw('q1.id = (
            SELECT MAX(q2.id)
            FROM quotations as q2
            WHERE q2.id_lead = q1.id_lead
        )');

        $query = Lead::select([
            'leads.id',
            'leads.LeadName',
            'leads.contactname',
            'leads.phonumber',
            'leads.closure_date',
            'city_list.city_name',
            'users.name as assigned_to',

            'quotation_status.name as quota_status',
            'quotation_status.id as quotation_status_id',

            'quotations.quota_id',
            'quotations.created_at as quote_date',

            'product_categories_tb.category_name',
            'product_sub_categories.subcategory_name',
            'products.productname',

            'quotationproduct.grandtotal',
            'quotationproduct.id as quot_product_id',
            'quotations.dis_app',
        ])

            ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
            ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
            ->leftJoin('quotation_status', 'quotation_status.id', '=', 'leads.quto_status')

            ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
            ->leftJoin('products', 'products.id', '=', 'leads.products')

            ->leftJoinSub($latestQuotationProduct, 'quotationproduct', function ($join) {
                $join->on('quotationproduct.leadno', '=', 'leads.id');
            })

            ->leftJoinSub($latestQuotation, 'quotations', function ($join) {
                $join->on('quotations.id_lead', '=', 'leads.id');
            })

            ->where('leads.Leadstatus', 343)
            ->where('leads.quota_proceed', 1)

            // 🚫 EXCLUDE ORF LEADS (IMPORTANT)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('orf')
                    ->whereColumn('orf.leano', 'leads.id');
            })

            ->where(function ($q) {
                $q->whereNotIn('leads.quto_status', [3, 4, 5])
                    ->orWhereNull('leads.quto_status');
            });

        // 🔎 FILTERS
        if ($categoryId && $categoryId != 0) {
            $query->where('leads.category', $categoryId);
        }

        if ($subCategoryId && $subCategoryId != 0) {
            $query->where('leads.subcategory_id', $subCategoryId);
        }

        if ($productId && $productId != 0) {
            $query->where('leads.products', $productId);
        }

        if ($statusId && $statusId != 0) {
            $query->where('leads.quto_status', $statusId);
        }

        $data = $query->orderBy('leads.updated_at', 'desc')->get()->map(function ($item) {

            $leadId = $item->id;

            $quotationExists = Quotation::where('id_lead', $leadId)->exists();
            $orfExists = Orf::where('quoproid', $item->quotation_id)->exists();

            /* ================= ACTION ================= */
            if (!$quotationExists) {
                $action = "<a href='/createquota/{$leadId}'>
            <button class='btn btn-success waves-effect waves-light'>Create</button>
        </a>";
            } elseif (!empty($item->grandtotal)) {

                $action = "<div class='quota-action-btns'>
            <a href='/editquota/{$item->quotation_id}'>
                <button class='btn btn-bull_yellow edit-btn waves-effect waves-light'>Edit</button>
            </a>";

                if ($item->dis_app == 2 && !$orfExists && $item->freight_approval == 0) {
                    $action .= "
            <a href='/orf/{$item->quotation_id}'>
                <button style='width:120px' class='btn btn-primary waves-effect waves-light'>
                    Move to ORF
                </button>
            </a>";
                }

                $action .= "
            <a href='/quotation/view/{$item->quotation_id}' target='_blank'>
                <button class='btn btn-info waves-effect waves-light'>View</button>
            </a>
        </div>";
            } else {
                $action = "-";
            }

            /* ================= CLOSURE DATE ================= */
            $closureDate = $item->closure_date
                ? \Carbon\Carbon::parse($item->closure_date)->format('d-m-Y')
                : 'Set Date';

            $closureDateHtml = "
        <span class='closure-date-btn text-primary cursor_pointer'
            data-id='{$item->id}'
            data-date='{$item->closure_date}'
            data-bs-toggle='modal'
            data-bs-target='#closureDateModal'>
            {$closureDate}
        </span>";

            return [
                'id' => $item->id,
                'quote_date' => $item->quote_date
                    ? Carbon::parse($item->quote_date)->format('d-m-Y')
                    : "-",

                'quota_id' => $item->quota_id ?? "-",
                'LeadName' => "<a href='/singlequota/{$item->id}'>{$item->LeadName}</a>",
                'city_name' => $item->city_name ?? "-",
                'contactname' => $item->contactname ?? "-",
                'phonumber' => $item->phonumber ?? "-",
                'category_name' => $item->category_name ?? "-",
                'grandtotal' => "<b class='text-danger'>{$item->grandtotal}</b>",
                'assigned_to' => $item->assigned_to ?? "-",
                'quota_status' => $item->quota_status ?? "-",
                'quotation_status_id' => $item->quotation_status_id,

                'closure_date' => $closureDateHtml,
                'action' => $action,
            ];
        });


        // $data = $query->orderBy('leads.updated_at', 'desc')->get()->map(function ($item) {

        //     $action = "-";

        //     if ($item->grandtotal) {
        //         $action = "
        //         <div class='quota-action-btns'>
        //             <a href='/editquota/{$item->quot_product_id}' class='btn btn-warning btn-sm'>Edit</a>";

        //         if ($item->dis_app == 2) {
        //             $action .= "
        //             <a href='/orf/{$item->quot_product_id}' class='btn btn-primary btn-sm'>Move to ORF</a>";
        //         }

        //         $action .= "
        //             <a href='/quotation/view/{$item->quot_product_id}' target='_blank' class='btn btn-info btn-sm'>View</a>
        //         </div>";
        //     }

        //     return [
        //         'id' => $item->id,
        //         'quote_date' => $item->quote_date ? Carbon::parse($item->quote_date)->format('d-m-Y') : "-",
        //         'quota_id' => $item->quota_id ?? "-",
        //         'LeadName' => "<a href='/singlequota/{$item->id}'>{$item->LeadName}</a>",
        //         'city_name' => $item->city_name ?? "-",
        //         'contactname' => $item->contactname ?? "-",
        //         'phonumber' => $item->phonumber ?? "-",
        //         'category_name' => $item->category_name ?? "-",
        //         'grandtotal' => "<b style='color:#dc3545'>{$item->grandtotal}</b>",
        //         'assigned_to' => $item->assigned_to ?? "-",
        //         'quota_status' => $item->quota_status ?? "-",
        //         'quotation_status_id' => $item->quotation_status_id,
        //         'action' => $action,
        //     ];
        // });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }



    public function filterdesign(Request $request)
    {
        $categoryId = $request->get('category_id');
        $subCategoryId = $request->get('sub_category_id');
        $productId = $request->get('product_id');

        $query = Lead::select([
            'leads.*',
            'source.dropdowndata as source_name',
            'status.dropdowndata as status_name',
            'city_list.city_name',
            'users.name as assigned_to',
            'product_categories_tb.category_name',
            'product_categories_tb.sub_category',
            'product_sub_categories.subcategory_name',
            'products.productname',
            'products.rate',
        ])
            ->leftJoin('dropdowndatas as source', 'source.id', '=', 'leads.Leadsource')
            ->leftJoin('dropdowndatas as status', 'status.id', '=', 'leads.Leadstatus')
            ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
            ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
            ->leftJoin('designs', 'designs.idlead', '=', 'leads.id')
            ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
            ->leftJoin('products', 'products.id', '=', 'leads.products')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
            ->where('product_categories_tb.sub_category', 'special')
            ->where('leads.Leadstatus', '=', 343)
            ->where(function ($query) {
                $query->whereNotIn('leads.quota_proceed', [1, 0])
                    ->orWhereNull('leads.quota_proceed');
            });

        // Apply filters
        if (!empty($categoryId) && $categoryId != 0) {
            $query->where('leads.category', $categoryId);
        }

        if (!empty($subCategoryId) && $subCategoryId != 0) {
            $query->where('leads.subcategory_id', $subCategoryId);
        }

        if (!empty($productId) && $productId != 0) {
            $query->where('leads.products', $productId);
        }

        $query->orderBy('leads.updated_at', 'desc');

        $data = $query->get()->map(function ($item) {
            $url = url('/singlequota/' . $item->id);

            return [
                'Entrydate' => $item->Entrydate ? Carbon::parse($item->Entrydate)->format('d-m-Y') : "-",
                'LeadName' => "<a href='{$url}'>" . e($item->LeadName) . "</a>",
                'citylist' => $item->city_name ?? "-",
                'contactname' => $item->contactname ?? "-",
                'phonumber' => $item->phonumber ?? "-",
                'category_name' => $item->category_name ?? "-",
                // 'subcategory_name' => $item->subcategory_name ?? "-",
                // 'productname' => "<div style='width: 250px' title='{$item->rate}'>{$item->productname}</div>",
                'assigned_to' => $item->assigned_to ?? "-",
                'lead_id' => $item->lead_id ?? "-",
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function sendQuotationMail($id)
    {
        $isSuperAdmin = auth()->check() && auth()->user()->role === 'Super Admin';
        if (!$isSuperAdmin) {
            $status = $this->getQuotationApprovalStatus((int) $id);
            if (!$status['approved']) {
                return response()->json(['message' => 'Cannot send quotation email: ' . $status['message']], 422);
            }
        }

        // 1️⃣ Get complete quotation document data via QuotationDocumentService
        $pdfData = app(\App\Services\QuotationDocumentService::class)->build((int) $id);
        $pdfData['pdfMode'] = true;

        $quotation = $pdfData['quotation'];

        // 2️⃣ Client email
        $toEmail = optional($quotation->lead)->email;

        if (!$toEmail) {
            return response()->json(['message' => 'Client email not found'], 422);
        }

        // 3️⃣ Mail settings
        $settings = Mailsetting::first();
        $fromMail = $settings->from_email ?? config('mail.from.address');
        $ccMail = $settings->cc_email ?? null;

        // 4️⃣ Generate PDF with complete data (including productGroupImages)
        $pdf = Pdf::loadView('quotations.pdf', $pdfData)->setPaper('a4');

        $drawingPaths = []; // ✅ ALWAYS initialize

        $leadCategory = (optional($quotation->lead)->category) ? \App\Models\ProductCategory::find($quotation->lead->category) : null;

        if ($leadCategory && $leadCategory->sub_category === 'special') {
            // Get design for this lead
            $design = Design::where('idlead', $quotation->lead->id)->first();

            if ($design) {
                // Get ALL drawings
                $drawings = DesignDrawing::where('design_id', $design->id)->get();

                foreach ($drawings as $drawing) {
                    if ($drawing->file_path && file_exists(storage_path('app/public/' . $drawing->file_path))) {
                        $drawingPaths[] = storage_path('app/public/' . $drawing->file_path);
                    }
                }
            }
        }

        // 5️⃣ Send mail
        Mail::to($toEmail)
            ->cc($ccMail ? array_filter(explode(',', $ccMail)) : [])
            ->send(new QuotationMail($quotation, $pdf, $fromMail, $drawingPaths));

        // Mark quotation as sent to customer
        $quotation->update([
            'sent_to_customer' => 1,
            'sent_to_customer_at' => now(),
            'sent_to_customer_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Quotation sent successfully to client.'
        ]);
    }

    public function markQuotationSent($id)
    {
        $quotation = Quotation::findOrFail($id);

        $isSuperAdmin = auth()->check() && auth()->user()->role === 'Super Admin';
        $hasEditPerm = auth()->check() && (auth()->user()->can('quotation-edit') || auth()->user()->can('quotation-create'));

        if (!$isSuperAdmin && !$hasEditPerm) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $quotation->update([
            'sent_to_customer' => 1,
            'sent_to_customer_at' => now(),
            'sent_to_customer_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quotation marked as sent to client.',
        ]);
    }

    // public function sendQuotationMail($id)
// {
//     $quotation = Quotation::with([
//         'lead.assignedUser',
//         'freight.termsCondition'
//     ])->findOrFail($id);

    //     $quotationProducts = QuotationProduct::with('productDetails.category')
//         ->where('quotationno', $quotation->quota_id)
//         ->get();

    //     $toEmail = $quotation->lead->email;
//     if (!$toEmail) {
//         return response()->json(['message' => 'Client email not found'], 422);
//     }

    //     $settings = Mailsetting::first();
//     $fromMail = $settings->from_email ?? config('mail.from.address');
//     $ccMail   = $settings->cc_email ?? null;

    //     // 🔥 EXACT SAME VIEW AS /quotation/view/{id}
//     $url = url("/quotation/view/{$quotation->id}");

    //     $fileName = "Quotation-{$quotation->quota_id}.pdf";
//     $pdfPath = storage_path("app/public/{$fileName}");

    //     Browsershot::url($url)
//         ->format('A3')
//         ->margins(10, 10, 10, 10)
//         ->showBackground()
//         ->save($pdfPath);

    //     Mail::to($toEmail)
//         ->cc($ccMail ? explode(',', $ccMail) : [])
//         ->send(new QuotationMail(
//             $quotation,
//             $pdfPath,
//             $fromMail
//         ));

    //     return response()->json([
//         'message' => 'Quotation sent successfully to client.'
//     ]);
// }
// public function sendQuotationMail($id)
// {
//     $quotation = Quotation::with('lead')->findOrFail($id);

    //     // 🔹 Client email
//     $toEmail = $quotation->lead->email;

    //     if (!$toEmail) {
//         return response()->json(['message' => 'Client email not found'], 422);
//     }


    //     $settings = Mailsetting::first();
//     $fromMail = $settings->from_email ?? config('mail.from.address');
//     $ccMail   = $settings->cc_email ?? null;

    //     // 🔹 Generate PDF from existing view
//     $pdf = Pdf::loadView('quotations.pdf', compact('quotation'));

    //     // 🔹 Send mail
//     Mail::to($toEmail)
//         ->cc($ccMail ? explode(',', $ccMail) : [])
//         ->send(new QuotationMail($quotation, $pdf, $fromMail));

    //     return response()->json([
//         'message' => 'Quotation sent successfully to client.'
//     ]);
// }


    public function getSpecs($categoryId, $type)
    {
        return DesignSpecificationMaster::where('category_id', $categoryId)
            ->where('type', $type)
            ->orderBy('spec_name')
            ->get(['id', 'spec_name']);
    }

    public function assignDesigner(Request $request)
    {
        $request->validate([
            'design_id' => 'required',
            'design_user' => 'required'
        ]);

        $design = Design::findOrFail($request->design_id);

        Design::where('id', $request->design_id)->update([
            'design_user' => $request->design_user,
            // 'design_status' => 'assigned'
        ]);

        $this->sendDesignerAssignedMail($design, $request->design_user);

        return response()->json(['success' => true]);
    }

    private function sendDesignerAssignedMail($design, $newDesignerId)
    {
        $authUser = auth()->user();
        $lead = Lead::find($design->idlead);

        $categoryName = ProductCategory::find($design->category)->category_name ?? 'N/A';

        // Collect recipients
        $emails = [];

        // Auth user
        if ($authUser && $authUser->email) {
            $emails[] = $authUser->email;
        }

        // Previously assigned design user
        if (!empty($design->assigndesign)) {
            $oldDesigner = User::find($design->assigndesign);
            if ($oldDesigner && $oldDesigner->email) {
                $emails[] = $oldDesigner->email;
            }
        }

        // Newly assigned designer
        $newDesigner = User::find($newDesignerId);
        if ($newDesigner && $newDesigner->email) {
            $emails[] = $newDesigner->email;
        }

        // Lead assigned sales user
        if ($lead && !empty($lead->assigned_to)) {
            $sales = User::find($lead->assigned_to);
            if ($sales && $sales->email) {
                $emails[] = $sales->email;
            }
        }

        // Remove duplicates
        $emails = array_unique($emails);

        if (empty($emails)) {
            return;
        }

        $data = [
            'lead_id' => $lead->lead_id ?? $lead->id,
            'category' => $categoryName,
            'designer' => $newDesigner->name ?? 'N/A',
        ];

        Mail::send('emails.designer_assigned', $data, function ($message) use ($emails) {
            $message->to($emails)
                ->subject('Designer Assigned to Design');
        });
    }

    // public function viewSpecifications($designId)
// {
//     $specs = DB::table('design_specifications as ds')
//         ->join(
//             'design_specification_masters as dsm',
//             'dsm.id',
//             '=',
//             'ds.specification'
//         )
//         ->where('ds.design_id', $designId)
//         ->select([
//             'dsm.spec_name',
//             'ds.spec_value'
//         ])
//         ->orderBy('dsm.spec_name')
//         ->get();

    //     return response()->json($specs);
// }
    public function viewSpecifications($designId)
    {
        $designInputs = DB::table('design_inputs')
            ->where('design_id', $designId)
            ->select('file_path')
            ->get();

        $specs = DB::table('design_specifications as ds')
            ->join('design_specification_masters as dsm', 'dsm.id', '=', 'ds.specification')
            ->where('ds.design_id', $designId)
            ->select([
                'dsm.spec_name',
                'ds.spec_value'
            ])
            ->orderBy('dsm.spec_name')
            ->get();

        $leadRemark = DB::table('designs')
            ->join('leads', 'leads.id', '=', 'designs.idlead')
            ->where('designs.id', $designId)
            ->value('leads.remarks');

        return response()->json([
            'specs' => $specs,
            'design_inputs' => $designInputs,
            'lead_remark' => $leadRemark
        ]);
    }

    // public function uploadFiles(Request $request)
// {
//     $request->validate([
//         'design_id' => 'required|exists:designs,id',
//         'quotation' => 'required|file|mimes:pdf,jpg,jpeg,png,xls,xlsx',
//     ]);

    //     $design = Design::findOrFail($request->design_id);

    //     // QUOTATION ONLY
//     if ($request->hasFile('quotation')) {
//         $design->quotation = $request->file('quotation')
//             ->store('designs/quotations', 'public');
//     }

    //     // Move to Sales
//     $design->design_status = 1;
//     $design->save();

    //     return response()->json(['success' => true]);
// }
// public function uploadFiles(Request $request)
// {
//     $request->validate([
//         'design_id' => 'required|exists:designs,id',
//         'quotation' => 'required|file|mimes:pdf,jpg,jpeg,png,xls,xlsx',
//     ]);

    //     $design = Design::findOrFail($request->design_id);


    //     if ($request->hasFile('quotation')) {

    //         $file = $request->file('quotation');

    //         $quotationPath = $file->store('designs/quotations', 'public');
//         $design->quotation = $quotationPath;


    //         $ext = strtolower($file->getClientOriginalExtension());

    //         if (in_array($ext, ['xls', 'xlsx'])) {

    //             $excelPath = storage_path('app/public/'.$quotationPath);

    //             $pdfName = pathinfo($quotationPath, PATHINFO_FILENAME).'.pdf';
//             $pdfRelativePath = 'designs/quotations/'.$pdfName;
//             $pdfFullPath = storage_path('app/public/'.$pdfRelativePath);

    //             \App\Helpers\QuotationHelper::convertExcelToPdf(
//                 $excelPath,
//                 $pdfFullPath
//             );

    //             // store pdf path separately
//             $design->quotation_pdf = $pdfRelativePath;
//         }
//     }


    //     $design->design_status = 1;
//     $design->save();


    //     $this->sendMoveToSalesMail($design);

    //     return response()->json(['success' => true]);
// }

    public function uploadFiles(Request $request)
    {
        $request->validate([
            'design_id' => 'required|exists:designs,id',
            'quotation' => 'required|file|mimes:pdf,jpg,jpeg,png,xls,xlsx',
        ]);

        $design = Design::findOrFail($request->design_id);

        // QUOTATION ONLY
        if ($request->hasFile('quotation')) {
            $design->quotation = $request->file('quotation')
                ->store('designs/quotations', 'public');
        }

        // Move to Sales
        $design->design_status = 1;
        $design->save();

        // ✅ SEND MAIL
        $this->sendMoveToSalesMail($design);

        return response()->json(['success' => true]);
    }

    private function sendMoveToSalesMail($design)
    {
        $lead = Lead::find($design->idlead);
        $authUser = auth()->user();

        $emails = [];

        // Logged-in user
        if ($authUser && $authUser->email) {
            $emails[] = $authUser->email;
        }

        // Design TL
        if (!empty($design->assigndesign)) {
            $user = User::find($design->assigndesign);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        // Design Engineer
        if (!empty($design->design_user)) {
            $user = User::find($design->design_user);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        // Sales user
        if ($lead && !empty($lead->assigned_to)) {
            $user = User::find($lead->assigned_to);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        // ✅ REMOVE DUPLICATES + NULLS
        $emails = array_unique(array_filter($emails));

        if (!count($emails)) {
            return;
        }

        Mail::send('emails.design_moved_to_sales', [
            'lead_id' => $lead->lead_id ?? $lead->id,
        ], function ($message) use ($emails) {
            $message->to($emails)
                ->subject('Design Moved to Sales');
        });
    }


    // public function uploadFiles(Request $request)
// {
//     $request->validate([
//         'design_id' => 'required|exists:designs,id',
//         'quotation' => 'required|file|mimes:pdf,jpg,jpeg,png,xls,xlsx',
//         'drawing'   => 'required|file|mimes:pdf,jpg,jpeg,png,xls,xlsx',
//     ]);

    //     $design = Design::findOrFail($request->design_id);

    //     if ($request->hasFile('quotation')) {
//         $design->quotation = $request->file('quotation')
//             ->store('designs/quotations', 'public');
//     }

    //     if ($request->hasFile('drawing')) {
//         $design->drawing = $request->file('drawing')
//             ->store('designs/drawings', 'public');
//     }

    //     $design->design_status = 1;
//     $design->save();

    //     return response()->json(['success' => true]);
// }


    public function viewQuotationd($designId)
    {
        $design = Design::findOrFail($designId);

        if (!$design->quotation || !Storage::disk('public')->exists($design->quotation)) {
            abort(404, 'Quotation not found');
        }

        // open file in browser
        return redirect(asset('storage/' . $design->quotation));
    }

    public function viewDrawing($designId)
    {
        $design = Design::findOrFail($designId);

        if (!$design->drawing || !Storage::disk('public')->exists($design->drawing)) {
            abort(404, 'Drawing not found');
        }

        return redirect(asset('storage/' . $design->drawing));
    }

    public function moveToQuotation(Request $request)
    {
        $design = Design::with('lead')->findOrFail($request->design_id);

        // must be completed
        if ((int) $design->design_status !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Design is not completed'
            ]);
        }

        // prevent duplicate quotation
        $exists = Quotation::where('id_lead', $design->idlead)->exists();
        if ($exists) {
            return response()->json([
                'success' => true,
                'message' => 'Already moved to quotation'
            ]);
        }


        // update lead
        $design->lead->update([
            'quota_proceed' => 1
        ]);

        return response()->json([
            'success' => true,
            'redirect' => url('/createquota/' . $design->idlead),
            'message' => 'Moved to quotation successfully'
        ]);
    }
    public function returnDesign(Request $request)
    {
        $request->validate([
            'design_id' => 'required|exists:designs,id',
            'return_remarks' => 'required|string',
        ]);
        $design = Design::findOrFail($request->design_id);
        Design::where('id', $request->design_id)
            ->where('return_status', 0)
            ->update([
                'return_status' => 1,
                'return_remarks' => $request->return_remarks,
                'updated_at' => now(),
            ]);

        $this->sendDesignRejectedMail($design);

        return response()->json(['success' => true]);
    }
    private function sendDesignRejectedMail($design)
    {
        $authUser = auth()->user();

        $lead = Lead::find($design->idlead);
        $categoryName = ProductCategory::find($design->category)->category_name ?? 'N/A';

        // Collect email recipients
        $emails = [];

        // Auth user
        if ($authUser && $authUser->email) {
            $emails[] = $authUser->email;
        }

        // Assigned designer
        if (!empty($design->assigndesign)) {
            $designer = User::find($design->assigndesign);
            if ($designer && $designer->email) {
                $emails[] = $designer->email;
            }
        }

        // Lead assigned sales user
        if ($lead && !empty($lead->assigned_to)) {
            $salesUser = User::find($lead->assigned_to);
            if ($salesUser && $salesUser->email) {
                $emails[] = $salesUser->email;
            }
        }

        // Remove duplicates
        $emails = array_unique($emails);

        if (empty($emails)) {
            return;
        }

        $data = [
            'lead_id' => $lead->lead_id ?? $lead->id, // show custom lead_id
            'category' => $categoryName,
            'remarks' => $design->return_remarks,
        ];

        Mail::send('emails.design_rejected', $data, function ($message) use ($emails) {
            $message->to($emails)
                ->subject('Design Rejected & Returned to Sales');
        });
    }


    public function getEditData($designId)
    {
        $design = Design::findOrFail($designId);

        $designInputs = DB::table('design_inputs')
            ->where('design_id', $designId)
            ->select('id', 'file_path')
            ->get();

        $specs = DB::table('design_specifications as ds')
            ->join('design_specification_masters as dsm', 'dsm.id', '=', 'ds.specification')
            ->where('ds.design_id', $designId)
            ->select([
                'ds.id as spec_row_id',
                'ds.specification as spec_id',
                'dsm.spec_name',
                'ds.spec_value'
            ])
            ->get();

        return response()->json([
            'design_inputs' => $designInputs,
            'specs' => $specs,
            'return_remarks' => $design->return_remarks,
        ]);
    }

    public function updateAfterReturn(Request $request)
    {
        $design = Design::findOrFail($request->design_id);

        /* -------------------------------
         | 1️⃣ DELETE FILES
         -------------------------------- */
        if ($request->has('delete_files')) {
            foreach ($request->delete_files as $fileId) {
                $file = DB::table('design_inputs')->where('id', $fileId)->first();
                if ($file && file_exists(public_path($file->file_path))) {
                    unlink(public_path($file->file_path));
                }
                DB::table('design_inputs')->where('id', $fileId)->delete();
            }
        }

        /* -------------------------------
         | 2️⃣ REPLACE FILES
         -------------------------------- */
        if ($request->hasFile('replace_files')) {
            foreach ($request->file('replace_files') as $fileId => $newFile) {

                if (!$newFile)
                    continue;

                $old = DB::table('design_inputs')->where('id', $fileId)->first();

                if ($old && file_exists(public_path($old->file_path))) {
                    unlink(public_path($old->file_path));
                }

                $filename = time() . '_design_' . uniqid() . '_' . $newFile->getClientOriginalName();
                $newFile->move(public_path('assets/design_inputs'), $filename);

                DB::table('design_inputs')
                    ->where('id', $fileId)
                    ->update([
                        'file_path' => 'assets/design_inputs/' . $filename
                    ]);
            }
        }

        /* -------------------------------
         | 3️⃣ ADD NEW FILES
         -------------------------------- */
        if ($request->hasFile('new_design_input')) {
            foreach ($request->file('new_design_input') as $file) {

                if (!$file)
                    continue;

                $filename = time() . '_design_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/design_inputs'), $filename);

                DB::table('design_inputs')->insert([
                    'design_id' => $design->id,
                    'file_path' => 'assets/design_inputs/' . $filename,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        /* -------------------------------
         | 4️⃣ UPDATE SPECS (UNCHANGED)
         -------------------------------- */
        if ($request->has('spec_ids')) {
            foreach ($request->spec_ids as $i => $specId) {
                DesignSpecification::where('design_id', $design->id)
                    ->where('specification', $specId)
                    ->update([
                        'spec_value' => $request->spec_values[$i]
                    ]);
            }
        }

        /* -------------------------------
         | 5️⃣ RESET RETURN STATUS
         -------------------------------- */
        $design->update([
            'return_status' => 0,
            'design_status' => null,
            'design_user' => null,
        ]);

        $this->sendDesignResubmittedMail($design);

        return response()->json(['success' => true]);
    }

    private function sendDesignResubmittedMail($design)
    {
        $authUser = auth()->user();
        $lead = Lead::find($design->idlead);

        $categoryName = ProductCategory::find($design->category)->category_name ?? 'N/A';

        // Collect recipients
        $emails = [];

        if ($authUser && $authUser->email) {
            $emails[] = $authUser->email;
        }

        // Lead assigned user (sales)
        if ($lead && !empty($lead->assigned_to)) {
            $sales = User::find($lead->assigned_to);
            if ($sales && $sales->email) {
                $emails[] = $sales->email;
            }
        }

        // Design assigned user
        if (!empty($design->assigndesign)) {
            $designer = User::find($design->assigndesign);
            if ($designer && $designer->email) {
                $emails[] = $designer->email;
            }
        }

        // Remove duplicate emails
        $emails = array_unique($emails);

        if (empty($emails)) {
            return;
        }

        $data = [
            'lead_id' => $lead->lead_id ?? $lead->id,
            'category' => $categoryName,
        ];

        Mail::send('emails.design_resubmitted', $data, function ($message) use ($emails) {
            $message->to($emails)
                ->subject('Rejected Design Updated & Sent to Design Team');
        });
    }


    // public function updateAfterReturn(Request $request)
// {
//     $design = Design::findOrFail($request->design_id);

    //     // Update design input (if uploaded)
//     if ($request->hasFile('design_input')) {
//         $file = $request->file('design_input');
//         $name = time().'_design_'.$file->getClientOriginalName();
//         $path = $file->move(public_path('assets/design_inputs'), $name);
//         $design->design_input = 'assets/design_inputs/'.$name;
//     }

    //     // Update specs
//     if ($request->has('spec_ids')) {
//         foreach ($request->spec_ids as $i => $specId) {
//             DesignSpecification::where('design_id', $design->id)
//                 ->where('specification', $specId)
//                 ->update([
//                     'spec_value' => $request->spec_values[$i]
//                 ]);
//         }
//     }

    //     // ✅ RESET RETURN STATUS
//     $design->return_status = 0;
//     $design->design_status = null;
//     $design->design_user = null;
//     $design->save();

    //     return response()->json(['success' => true]);
// }

    public function designEngineerList()
    {
        $user = Auth::user();

        // Allow only Design Engineer
        if ($user->role !== 'Design Engineer') {
            abort(403, 'Unauthorized');
        }

        $designs = Design::where('design_user', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.design_list', compact('designs'));
    }
    public function edit($id)
    {
        $design = Design::findOrFail($id);

        // Security: only assigned designer can edit
        if ($design->design_user != auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('pages.design_edit', compact('design'));
    }
    public function updateFiles(Request $request, $id)
    {
        $request->validate([
            'quotation' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xls,xlsx',
            'drawing' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xls,xlsx',
        ]);

        $design = Design::findOrFail($id);

        // Security check
        if ($design->design_user != auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($request->hasFile('quotation')) {
            $design->quotation = $request->file('quotation')
                ->store('designs/quotations', 'public');
        }

        if ($request->hasFile('drawing')) {
            $design->drawing = $request->file('drawing')
                ->store('designs/drawings', 'public');
        }

        $design->save();

        return redirect()
            ->route('design.engineer.list')
            ->with('success', 'Design files updated successfully');
    }
    public function getDesignDrawings($leadId)
    {
        $design = \App\Models\Design::where('idlead', $leadId)->first();

        if (!$design) {
            return response()->json([]);
        }

        $drawings = DesignDrawing::where('design_id', $design->id)
            ->select('file_path')
            ->get()
            ->map(function ($d) {
                return [
                    'url' => asset('storage/' . $d->file_path),
                    'ext' => strtolower(pathinfo($d->file_path, PATHINFO_EXTENSION)),
                ];
            });

        return response()->json($drawings);
    }
    public function getuploadDesignDrawings($designId)
    {
        return DB::table('design_drawings')
            ->where('design_id', $designId)
            ->select('id', 'file_path')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'file_path' => $row->file_path,
                    'file_name' => basename($row->file_path),
                ];
            });
    }

    public function uploadDrawings(Request $request)
    {
        $design = Design::findOrFail($request->design_id);

        // DELETE
        if ($request->has('delete_drawings')) {
            foreach ($request->delete_drawings as $id) {
                $file = DB::table('design_drawings')->where('id', $id)->first();
                if ($file && Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }
                DB::table('design_drawings')->where('id', $id)->delete();
            }
        }

        // REPLACE
        if ($request->hasFile('replace_files')) {
            foreach ($request->file('replace_files') as $id => $file) {
                if (!$file)
                    continue;

                $old = DB::table('design_drawings')->where('id', $id)->first();
                if ($old && Storage::disk('public')->exists($old->file_path)) {
                    Storage::disk('public')->delete($old->file_path);
                }

                $path = $file->store('designs/drawings', 'public');

                DB::table('design_drawings')->where('id', $id)->update([
                    'file_path' => $path,
                    'updated_at' => now(),
                ]);
            }
        }

        // ADD NEW
        if ($request->hasFile('drawing')) {
            foreach ($request->file('drawing') as $file) {
                $path = $file->store('designs/drawings', 'public');

                DB::table('design_drawings')->insert([
                    'design_id' => $design->id,
                    'file_path' => $path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // MOVE TO APPROVAL
        $design->update(['drawing_approved' => 'pending']);

        // SEND MAIL
        $this->sendDrawingUploadMail($design);

        return response()->json(['success' => true]);
    }
    private function sendDrawingUploadMail($design)
    {
        $lead = Lead::find($design->idlead);
        $auth = auth()->user();

        $emails = [];

        if ($auth)
            $emails[] = $auth->email;
        if ($design->assigndesign)
            $emails[] = User::find($design->assigndesign)->email ?? null;
        if ($design->design_user)
            $emails[] = User::find($design->design_user)->email ?? null;
        if ($lead && $lead->assigned_to)
            $emails[] = User::find($lead->assigned_to)->email ?? null;

        $emails = array_unique(array_filter($emails));

        Mail::send('emails.drawing_uploaded', [
            'lead_id' => $lead->lead_id ?? $lead->id
        ], function ($m) use ($emails) {
            $m->to($emails)->subject('Design Drawings Uploaded');
        });
    }

    public function getDrawingApproval($designId)
    {
        $design = Design::findOrFail($designId);
        $lead = Lead::find($design->idlead);

        return response()->json([
            'can_approve' => auth()->id() == $lead->assigned_to,
            'drawings' => DesignDrawing::where('design_id', $designId)->get()
        ]);
    }
    // public function saveDrawingApproval(Request $request)
// {
//     $design = Design::findOrFail($request->design_id);

    //     $hasReject = false;

    //     foreach ($request->drawings as $id => $data) {

    //         DesignDrawing::where('id', $id)->update([
//             'drawing_app' => $data['status'],
//             'drawing_remarks' => $data['remarks'] ?? null
//         ]);

    //         if ($data['status'] == 2) {
//             $hasReject = true;
//         }
//     }

    //     $design->update([
//         'drawing_approved' => $hasReject ? 'rejected' : 'approved'
//     ]);

    //     return response()->json(['success' => true]);
// }
    public function saveDrawingApproval(Request $request)
    {
        $design = Design::findOrFail($request->design_id);
        $hasReject = false;

        foreach ($request->drawings as $id => $data) {

            $update = [
                'drawing_app' => $data['status'],
                'drawing_remarks' => $data['remarks'] ?? null,
            ];

            // ✅ SALES ATTACH FILE
            if ($request->hasFile("sales_attach.$id")) {

                $file = $request->file("sales_attach.$id");
                $path = $file->store('design/sales_attach', 'public');

                $update['sales_attach'] = $path;
            }

            DesignDrawing::where('id', $id)->update($update);

            if ($data['status'] == 2) {
                $hasReject = true;
            }
        }

        $design->update([
            'drawing_approved' => $hasReject ? 'rejected' : 'approved'
        ]);

        $this->sendDrawingapproMail($design);

        return response()->json(['success' => true]);
    }

    // public function saveDrawingApproval(Request $request)
// {
//     $design = Design::findOrFail($request->design_id);

    //     $hasReject = false;

    //     foreach ($request->drawings as $id => $data) {

    //         DesignDrawing::where('id', $id)->update([
//             'drawing_app'     => $data['status'],
//             'drawing_remarks' => $data['remarks'] ?? null
//         ]);

    //         if ($data['status'] == 2) {
//             $hasReject = true;
//         }
//     }

    //     $design->update([
//         'drawing_approved' => $hasReject ? 'rejected' : 'approved'
//     ]);

    //     // ✅ SEND MAIL
//     $this->sendDrawingapproMail($design);

    //     return response()->json(['success' => true]);
// }
    private function sendDrawingapproMail($design)
    {
        $lead = Lead::find($design->idlead);
        $authUser = auth()->user();

        $emails = [];

        // Logged-in user
        if ($authUser && $authUser->email) {
            $emails[] = $authUser->email;
        }

        // Design TL
        if (!empty($design->assigndesign)) {
            $user = User::find($design->assigndesign);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        // Design Engineer
        if (!empty($design->design_user)) {
            $user = User::find($design->design_user);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        // Sales user
        if ($lead && !empty($lead->assigned_to)) {
            $user = User::find($lead->assigned_to);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        // ✅ REMOVE DUPLICATES + NULLS
        $emails = array_unique(array_filter($emails));

        if (!count($emails)) {
            return;
        }

        Mail::send('emails.drawing_approval', [
            'lead_id' => $lead->lead_id ?? $lead->id,
            'status' => $design->drawing_approved // approved / rejected
        ], function ($message) use ($emails, $design) {

            $subject = $design->drawing_approved === 'approved'
                ? 'Design Drawings Approved'
                : 'Design Drawings Rejected';

            $message->to($emails)->subject($subject);
        });
    }

    public function getRejectedDrawings($designId)
    {
        $design = Design::findOrFail($designId);

        return response()->json([
            'can_edit' => auth()->id() == $design->design_user,
            'drawings' => DesignDrawing::where('design_id', $designId)->get()
        ]);
    }
    public function updateRejectedDrawings(Request $request)
    {
        $design = Design::findOrFail($request->design_id);

        // only design user allowed
        abort_if(auth()->id() != $design->design_user, 403);

        if ($request->hasFile('replace_files')) {

            foreach ($request->file('replace_files') as $id => $file) {

                $path = $file->store('designs/drawings', 'public');

                DesignDrawing::where('id', $id)->update([
                    'file_path' => $path,
                    'drawing_app' => null,
                    'drawing_remarks' => null,
                    'updated_at' => now(),
                ]);
            }
        }

        // back to approval
        $design->update(['drawing_approved' => 'pending']);

        // ✅ SEND MAIL
        $this->resendDrawingapproMail($design);

        return response()->json(['success' => true]);
    }
    private function resendDrawingapproMail($design)
    {
        $lead = Lead::find($design->idlead);
        $authUser = auth()->user();

        $emails = [];

        // Logged-in user
        if ($authUser && $authUser->email) {
            $emails[] = $authUser->email;
        }

        // Design Engineer
        if (!empty($design->design_user)) {
            $user = User::find($design->design_user);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        // Design TL
        if (!empty($design->assigndesign)) {
            $user = User::find($design->assigndesign);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        // Sales user
        if ($lead && !empty($lead->assigned_to)) {
            $user = User::find($lead->assigned_to);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        // ✅ REMOVE DUPLICATES + NULLS
        $emails = array_unique(array_filter($emails));

        if (!count($emails)) {
            return;
        }

        Mail::send('emails.drawing_resubmitted', [
            'lead_id' => $lead->lead_id ?? $lead->id,
        ], function ($message) use ($emails) {
            $message->to($emails)
                ->subject('Rejected Drawings Updated & Resubmitted');
        });
    }


    public function sendSingleDrawingMail(Request $request)
    {
        $drawing = DesignDrawing::findOrFail($request->drawing_id);
        $design = Design::findOrFail($drawing->design_id);
        $lead = Lead::findOrFail($design->idlead);

        abort_if(auth()->id() !== $lead->assigned_to, 403);

        // ✅ Save sales attachment if uploaded
        if ($request->hasFile('sales_attach')) {
            $path = $request->file('sales_attach')
                ->store('design/sales_attach', 'public');

            $drawing->update(['sales_attach' => $path]);
        }

        $emails = array_unique(array_filter([
            $lead->email,
            auth()->user()->email
        ]));

        Mail::send('emails.single_drawing', [
            'drawing' => $drawing
        ], function ($m) use ($emails, $drawing) {

            $m->to($emails)
                ->subject('Design Drawing');

            // ✅ Attach drawing file
            $m->attach(
                storage_path('app/public/' . $drawing->file_path),
                ['as' => basename($drawing->file_path)]
            );

            // ✅ Attach sales file if exists
            if ($drawing->sales_attach) {
                $m->attach(
                    storage_path('app/public/' . $drawing->sales_attach),
                    ['as' => basename($drawing->sales_attach)]
                );
            }
        });

        return response()->json(['success' => true]);
    }


    public function sendAllDrawingsMail(Request $request)
    {
        $design = Design::findOrFail($request->design_id);
        $lead = Lead::findOrFail($design->idlead);

        abort_if(auth()->id() !== $lead->assigned_to, 403);

        // ✅ Save uploaded sales attachments
        foreach ($request->file('sales_attach', []) as $id => $file) {
            $path = $file->store('design/sales_attach', 'public');
            DesignDrawing::where('id', $id)->update(['sales_attach' => $path]);
        }

        $drawings = DesignDrawing::where('design_id', $design->id)->get();

        $emails = array_unique(array_filter([
            $lead->email,
            auth()->user()->email
        ]));

        Mail::send('emails.all_drawings', [
            'drawings' => $drawings
        ], function ($m) use ($emails, $drawings) {

            $m->to($emails)
                ->subject('All Design Drawings');

            // ✅ Attach all drawings
            foreach ($drawings as $drawing) {

                if ($drawing->file_path) {
                    $m->attach(
                        storage_path('app/public/' . $drawing->file_path),
                        ['as' => basename($drawing->file_path)]
                    );
                }

                if ($drawing->sales_attach) {
                    $m->attach(
                        storage_path('app/public/' . $drawing->sales_attach),
                        ['as' => basename($drawing->sales_attach)]
                    );
                }
            }
        });

        return response()->json(['success' => true]);
    }
    public function approvedDrawings(Request $request)
    {
        $request->validate([
            'design_id' => 'required|exists:designs,id',
            'quotation_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,csv',
            'drawing.*' => 'nullable|file|mimes:pdf,jpg,png,jpeg,dwg,xls,xlsx,csv',
            'drawing_remarks' => 'nullable|string',
        ]);

        $design = Design::findOrFail($request->design_id);

        /* -----------------------------------------
           SAVE QUOTATION FILE
        ----------------------------------------- */
        if ($request->hasFile('quotation_file')) {

            $quotationPath = $request->file('quotation_file')
                ->store('designs/quotations', 'public');

            $design->quotation = $quotationPath;
        }

        /* -----------------------------------------
           UPDATE DESIGN STATUS
        ----------------------------------------- */
        $design->drawing_approved = 'approved';
        $design->design_status = 1;
        $design->drawing_remarks = $request->drawing_remarks;
        $design->save();

        /* -----------------------------------------
           SAVE APPROVED DRAWINGS
        ----------------------------------------- */
        if ($request->hasFile('drawing')) {

            foreach ($request->file('drawing') as $file) {

                $drawingPath = $file->store('designs/drawings', 'public');

                DesignDrawing::create([
                    'design_id' => $design->id,
                    'file_path' => $drawingPath,
                    'drawing_app' => 1, // ✅ Approved
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Approved drawings and quotation uploaded successfully'
        ]);
    }


}