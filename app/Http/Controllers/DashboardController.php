<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Orf;
use App\Models\ProductStock;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\QuotationProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
$isSuperAdmin = $user->role === 'Super Admin';

        // $totalLeads = Lead::count();
        $totalLeads = Lead::when(!$isSuperAdmin, function ($q) use ($user) {
    $q->where('assigned_to', $user->id);
})->count();


        // $leads = DB::table('dropdowndatas')->where('formid', 4)->get();
        // $leads = DB::table('dropdowndatas')
        //     ->where('dropdowndatas.formid', 4)
        //     ->leftJoin('leads', 'leads.LeadSource', '=', 'dropdowndatas.id') // Left join to include all dropdowndata records
        //     ->select('dropdowndatas.id', 'dropdowndatas.dropdowndata', DB::raw('count(leads.id) as lead_count'))
        //     ->groupBy('dropdowndatas.id', 'dropdowndatas.dropdowndata')
        //     ->get();
        $leads = DB::table('dropdowndatas')
    ->where('dropdowndatas.formid', 4)
    ->leftJoin('leads', function ($join) use ($isSuperAdmin, $user) {
        $join->on('leads.LeadSource', '=', 'dropdowndatas.id');

        if (!$isSuperAdmin) {
            $join->where('leads.assigned_to', $user->id);
        }
    })
    ->select(
        'dropdowndatas.id',
        'dropdowndatas.dropdowndata',
        DB::raw('COUNT(leads.id) as lead_count')
    )
    ->groupBy('dropdowndatas.id', 'dropdowndatas.dropdowndata')
    ->get();


        // $leadsstatus = DB::table('dropdowndatas')
        //     ->where('dropdowndatas.formid', 6)
        //     ->leftJoin('leads', 'leads.Leadstatus', '=', 'dropdowndatas.id') // Left join to include all dropdowndata records
        //     ->select('dropdowndatas.id', 'dropdowndatas.dropdowndata', DB::raw('count(leads.id) as lead_count'))
        //     ->groupBy('dropdowndatas.id', 'dropdowndatas.dropdowndata')
        //     ->get();
        $leadsstatus = DB::table('dropdowndatas')
    ->where('dropdowndatas.formid', 6)
    ->leftJoin('leads', function ($join) use ($isSuperAdmin, $user) {
        $join->on('leads.Leadstatus', '=', 'dropdowndatas.id');

        if (!$isSuperAdmin) {
            $join->where('leads.assigned_to', $user->id);
        }
    })
    ->select(
        'dropdowndatas.id',
        'dropdowndatas.dropdowndata',
        DB::raw('COUNT(leads.id) as lead_count')
    )
    ->groupBy('dropdowndatas.id', 'dropdowndatas.dropdowndata')
    ->get();

$latestPerLead = QuotationProduct::select('quotationproduct.id')
    ->whereIn('quotationproduct.id', function ($query) use ($isSuperAdmin, $user) {

        $query->selectRaw('MAX(quotationproduct.id)')
            ->from('quotationproduct')
            ->when(!$isSuperAdmin, function ($q) use ($user) {
                $q->join('leads', 'leads.id', '=', 'quotationproduct.leadno')
                  ->where('leads.assigned_to', $user->id);
            })
            ->groupBy('quotationproduct.leadno');
    })
    ->get();


        // $latestPerLead = QuotationProduct::select('id')
        //     ->whereIn('id', function ($query) {
        //         $query->selectRaw('MAX(id)')
        //             ->from('quotationproduct')
        //             ->groupBy('leadno');
        //     })->get();

        // $quotationTotalCount = Quotation::count();
        $quotationTotalCount = Quotation::when(!$isSuperAdmin, function ($q) use ($user) {
        $q->join('leads', 'leads.id', '=', 'quotations.id_lead')
          ->where('leads.assigned_to', $user->id);
    })
    ->count();

        $quotationApprovedCount = QuotationProduct::whereIn('id', $latestPerLead->pluck('id'))->where('quto_app', 2)->count();
        $quotationPendingCount = QuotationProduct::whereIn('id', $latestPerLead->pluck('id'))->where('quto_app', 0)->count();

        $quotationAmountTotal = QuotationProduct::whereIn('id', $latestPerLead->pluck('id'))->sum('grandtotal');
        $quotationApprovedTotal = QuotationProduct::whereIn('id', $latestPerLead->pluck('id'))->where('quto_app', 2)->sum('grandtotal');
        $quotationPendingTotal = QuotationProduct::whereIn('id', $latestPerLead->pluck('id'))->where('quto_app', 0)->sum('grandtotal');



        $quotationSummary['quotationTotalCount'] = $quotationTotalCount;
        $quotationSummary['quotationApprovedCount'] = $quotationApprovedCount;
        $quotationSummary['quotationPendingCount'] = $quotationPendingCount;
        $quotationSummary['quotationAmountTotal'] = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $quotationAmountTotal);
        $quotationSummary['quotationApprovedTotal'] = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $quotationApprovedTotal);
        $quotationSummary['quotationPendingTotal'] = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $quotationPendingTotal);

        // $orfCount = Orf::count();
        $orfCount = Orf::when(!$isSuperAdmin, function ($q) use ($user) {
    $q->join('leads', 'leads.id', '=', 'orf.leano')
      ->where('leads.assigned_to', $user->id);
})->count();

        // $orfApprovedCount = Orf::where('approval_status', 1)->where('cs_status', 1)->count();
        $orfApprovedCount = Orf::when(!$isSuperAdmin, function ($q) use ($user) {
        $q->join('leads', 'leads.id', '=', 'orf.leano')
          ->where('leads.assigned_to', $user->id);
    })
    ->where('approval_status', 1)
    ->where('cs_status', 1)
    ->count();

        // $orfPendingCount = Orf::where(function ($query) {
        //     $query->where(function ($q) {
        //         $q->where('approval_status', 0)->where('cs_status', 0);
        //     })->orWhere(function ($q) {
        //         $q->where('approval_status', 0)->where('cs_status', 1);
        //     })->orWhere(function ($q) {
        //         $q->where('approval_status', 1)->where('cs_status', 0);
        //     });
        // })->count();
        $orfPendingCount = Orf::when(!$isSuperAdmin, function ($q) use ($user) {
        $q->join('leads', 'leads.id', '=', 'orf.leano')
          ->where('leads.assigned_to', $user->id);
    })
    ->where(function ($query) {
        $query->where(function ($q) {
            $q->where('approval_status', 0)->where('cs_status', 0);
        })
        ->orWhere(function ($q) {
            $q->where('approval_status', 0)->where('cs_status', 1);
        })
        ->orWhere(function ($q) {
            $q->where('approval_status', 1)->where('cs_status', 0);
        });
    })
    ->count();



        // $orfAmountTotal = Orf::join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
        //     ->sum('quotationproduct.grandtotal');
        $orfAmountTotal = Orf::join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
    ->when(!$isSuperAdmin, function ($q) use ($user) {
        $q->join('leads', 'leads.id', '=', 'orf.leano')
          ->where('leads.assigned_to', $user->id);
    })
    ->sum('quotationproduct.grandtotal');


        // $orfApprovedAmountTotal = Orf::join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
        //     ->where('orf.approval_status', 1)
        //     ->where('orf.cs_status', 1)
        //     ->sum('quotationproduct.grandtotal');
        $orfApprovedAmountTotal = Orf::join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
    ->when(!$isSuperAdmin, function ($q) use ($user) {
        $q->join('leads', 'leads.id', '=', 'orf.leano')
          ->where('leads.assigned_to', $user->id);
    })
    ->where('orf.approval_status', 1)
    ->where('orf.cs_status', 1)
    ->sum('quotationproduct.grandtotal');

$orfPendingTotal = Orf::join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
    ->when(!$isSuperAdmin, function ($q) use ($user) {
        $q->join('leads', 'leads.id', '=', 'orf.leano')
          ->where('leads.assigned_to', $user->id);
    })
    ->where(function ($query) {
        $query->where(function ($q) {
            $q->where('orf.approval_status', 0)->where('orf.cs_status', 0);
        })
        ->orWhere(function ($q) {
            $q->where('orf.approval_status', 0)->where('orf.cs_status', 1);
        })
        ->orWhere(function ($q) {
            $q->where('orf.approval_status', 1)->where('orf.cs_status', 0);
        });
    })
    ->sum('quotationproduct.grandtotal');

        // $orfPendingTotal = Orf::join('quotationproduct', 'quotationproduct.id', '=', 'orf.quoproid')
        //     ->where(function ($query) {
        //         $query->orWhere(function ($q) {
        //             $q->where('orf.approval_status', 0)->where('orf.cs_status', 0);
        //         });
        //         $query->orWhere(function ($q) {
        //             $q->where('orf.approval_status', 0)->where('orf.cs_status', 1);
        //         });
        //         $query->orWhere(function ($q) {
        //             $q->where('orf.approval_status', 1)->where('orf.cs_status', 0);
        //         });
        //     })
        //     ->sum('quotationproduct.grandtotal');

            $poCount = PurchaseOrder::count();
             $poAmountTotal = PurchaseOrder::sum('purchase_order_tb.total_amount');

        $poSummary['poCount'] = $poCount;
        $poSummary['poAmountTotal'] = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $poAmountTotal);
        $orfSummary['orfCount'] = $orfCount;
        $orfSummary['orfApprovedCount'] = $orfApprovedCount;
        $orfSummary['orfPendingCount'] = $orfPendingCount;
        $orfSummary['orfAmountTotal'] = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $orfAmountTotal);
        $orfSummary['orfApprovedAmountTotal'] = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $orfApprovedAmountTotal);
        $orfSummary['orfPendingTotal'] = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $orfPendingTotal);

        return view('pages.dashboard', compact('totalLeads', 'leads', 'leadsstatus', 'quotationSummary', 'orfSummary', 'poSummary'));

    }

    // public function getLeads()
    // {
    //     $leads = Lead::leftJoin('dropdowndatas as source', 'leads.Leadsource', '=', 'source.id')
    //         ->leftJoin('dropdowndatas as status', 'leads.Leadstatus', '=', 'status.id')
    //         ->select(
    //             'leads.id',
    //             'leads.LeadName',
    //             'source.dropdowndata as Leadsource',  // Replacing ID with actual name
    //             'leads.Entrydate',
    //             'status.dropdowndata as Leadstatus'  // Replacing ID with actual name
    //         )
    //         ->get();
    //     // $leads = Lead::select('LeadName', 'Leadsource', 'Entrydate', 'Leadstatus')->get();
    //     return response()->json(['leads' => $leads]);
    // }

    public function getLeads()
{
    $user = Auth::user();

    $query = Lead::leftJoin('dropdowndatas as source', 'leads.Leadsource', '=', 'source.id')
        ->leftJoin('dropdowndatas as status', 'leads.Leadstatus', '=', 'status.id')
        ->select(
            'leads.id',
            'leads.LeadName',
            'source.dropdowndata as Leadsource',
            'leads.Entrydate',
            'status.dropdowndata as Leadstatus'
        );

    /* ---------------------------------
     | ROLE BASED FILTER
     --------------------------------- */

    // Sales Manager → only their leads
    if ($user->role === 'Sales Manager') {
        $query->where('leads.assigned_to', $user->id);
    }

    // Other roles (except Super Admin) → no data
    elseif ($user->role !== 'Super Admin') {
        $query->whereRaw('1 = 0');
    }

    $leads = $query->get();

    return response()->json(['leads' => $leads]);
}

    // public function getDashboardData(Request $request)
    // {
    //     $query = Orf::where('orf.approval_status', 1)
    //         ->where('orf.cs_status', 1)
    //         ->leftJoin('quotationproduct', 'orf.quoproid', '=', 'quotationproduct.id')
    //         ->select('orf.*', 'quotationproduct.catepro'); // select catepro along with orf columns

    //     // Handle Quick Period
    //     if ($request->period == 'this_week') {
    //         $query->whereBetween('orf.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    //     } elseif ($request->period == 'this_month') {
    //         $query->whereMonth('orf.created_at', Carbon::now()->month);
    //     }

    //     // Handle custom from/to
    //     if ($request->fromdate && $request->todate) {
    //         $query->whereBetween('orf.created_at', [$request->fromdate, $request->todate]);
    //     }

    //     // Assigned To
    //     if ($request->assignedto) {
    //         $query->where('orf.assigned_to', $request->assignedto);
    //     }

    //     // Return count and data
    //     $data = $query->get();
    //     return response()->json([
    //         'count' => $data->count(),
    //         'data' => $data
    //     ]);
    // }
    public function getDashboardData(Request $request)
    {
        $data = [];

        // Common filter logic
        $applyFilters = function ($query) use ($request) {
            if ($request->period == 'this_week') {
                $query->whereBetween('orf.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($request->period == 'this_month') {
                $query->whereMonth('orf.created_at', Carbon::now()->month);
            }

            if ($request->fromdate && $request->todate) {
                $from = Carbon::parse($request->fromdate)->startOfDay();
                $to = Carbon::parse($request->todate)->endOfDay();
                $query->whereBetween('orf.created_at', [$from, $to]);
            }


            if ($request->assignedto) {
                $query->where('assigned_to', $request->assignedto);
            }

            return $query;
        };

        // $applyFilters = function ($query) use ($request) {
        //     if ($request->period == 'this_week') {
        //         $query->whereBetween('orf.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        //     } elseif ($request->period == 'this_month') {
        //         $query->whereMonth('orf.created_at', Carbon::now()->month);
        //     }

        //     if ($request->fromdate && $request->todate) {
        //         $from = Carbon::parse($request->fromdate)->startOfDay();
        //         $to = Carbon::parse($request->todate)->endOfDay();
        //         $query->whereBetween('orf.created_at', [$from, $to]);
        //     }


        //     if ($request->assignedto) {
        //         $query->where('assigned_to', $request->assignedto);
        //     }

        //     return $query;
        // };

        // Card 1: Sales
        // $salesQuery = Orf::where('orf.approval_status', 1)
        //     ->where('orf.cs_status', 1)
        //     ->leftJoin('quotationproduct', 'orf.quoproid', '=', 'quotationproduct.id')
        //     ->select('orf.*', 'quotationproduct.catepro');
        // $salesQuery = $applyFilters($salesQuery);
        // $data['sales'] = $salesQuery->get();

        // // Card 2: Spares
        // $sparesQuery = Orf::where('orf.approval_status', 1)
        //     ->where('orf.cs_status', 1)
        //     ->leftJoin('quotationproduct', 'orf.quoproid', '=', 'quotationproduct.id')
        //     ->select('orf.*', 'quotationproduct.catepro');
        // $sparesQuery = $applyFilters($sparesQuery);
        // $data['spares'] = $sparesQuery->get();
        // Card 1: Sales
$salesQuery = Orf::where('orf.approval_status', 1)
    ->where('orf.cs_status', 1)

    // ORF → Lead
    ->leftJoin('leads', 'leads.id', '=', 'orf.leano')

    // ORF → Quotation
    ->leftJoin('quotations', 'orf.quoproid', '=', 'quotations.id')

    // Lead → Category
    ->leftJoin(
        'product_categories_tb',
        'product_categories_tb.id',
        '=',
        'leads.category'
    )

    ->select(
        'orf.*',
        'product_categories_tb.category_name as catepro'
    );

$salesQuery = $applyFilters($salesQuery);
$data['sales'] = $salesQuery->get();


// Card 2: Spares
$sparesQuery = Orf::where('orf.approval_status', 1)
    ->where('orf.cs_status', 1)

    // ORF → Lead
    ->leftJoin('leads', 'leads.id', '=', 'orf.leano')

    // ORF → Quotation
    ->leftJoin('quotations', 'orf.quoproid', '=', 'quotations.id')

    // Lead → Category
    ->leftJoin(
        'product_categories_tb',
        'product_categories_tb.id',
        '=',
        'leads.category'
    )

    ->select(
        'orf.*',
        'product_categories_tb.category_name as catepro'
    );

$sparesQuery = $applyFilters($sparesQuery);
$data['spares'] = $sparesQuery->get();



        // Card 3: stock equipment
        // $sparesQuery = ProductStock::get();
        // $sparesQuery = $applyFilters($sparesQuery);
        // $data['stockequip'] = $sparesQuery->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

}

