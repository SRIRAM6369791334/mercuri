<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{


    public function showReports(Request $request)
    {
        $query = Lead::select(
            'leads.lead_id',
            'leads.LeadName',
            'product_categories_tb.category_name',
            'products.productname',
            'leads.converteddate',
            'quotationproduct.grandtotal',
            DB::raw("
                CASE
                    WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                    WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                    WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                    WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                    ELSE 'N/A'
                END as stage
            ")
        )
        ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
        ->leftJoin('products', 'leads.products', '=', 'products.id')
        ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
        ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
        ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
        ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        $leads = $query->orderBy('leads.converteddate', 'asc')->get();

        $query2 = Lead::select(
            'leads.lead_id',
            'leads.LeadName',
            'product_categories_tb.category_name',
            'products.productname',
            'leads.converteddate',
            'quotationproduct.grandtotal',
            DB::raw("
                CASE
                    WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                    WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                    WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                    WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                    ELSE 'N/A'
                END as stage
            ")
        )
        ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
        ->leftJoin('products', 'leads.products', '=', 'products.id')
        ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
        ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
        ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
        ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        $leads2 = $query2->orderBy('leads.converteddate', 'desc')->get();



        $query3 = Lead::select(
            'leads.lead_id',
            'leads.LeadName',
            'product_categories_tb.category_name',
            'products.productname',
            'leads.converteddate',
            'quotationproduct.grandtotal',
            DB::raw("
                CASE
                    WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                    WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                    WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                    WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                    ELSE 'N/A'
                END as stage
            ")
        )
        ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
        ->leftJoin('products', 'leads.products', '=', 'products.id')
        ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
        ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
        ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
        ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        $leads3 = $query3->orderBy('leads.converteddate', 'desc')->get();


        $query4 = Lead::select(
            'leads.lead_id',
            'leads.LeadName',
            'product_categories_tb.category_name',
            'products.productname',
            'leads.converteddate',
            'quotationproduct.grandtotal',
            DB::raw("
                CASE
                    WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                    WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                    WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                    WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                    ELSE 'N/A'
                END as stage
            ")
        )
        ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
        ->leftJoin('products', 'leads.products', '=', 'products.id')
        ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
        ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
        ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
        ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        $leads4 = $query4->orderBy('leads.converteddate', 'desc')->get();


        $query5 = Lead::select(
            'leads.lead_id',
            'leads.LeadName',
            'product_categories_tb.category_name',
            'products.productname',
            'leads.converteddate',
            'quotationproduct.grandtotal',
            DB::raw("
                CASE
                    WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                    WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                    WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                    WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                    ELSE 'N/A'
                END as stage
            ")
        )
        ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
        ->leftJoin('products', 'leads.products', '=', 'products.id')
        ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
        ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
        ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
        ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        $leads5 = $query5->orderBy('leads.converteddate', 'desc')->get();


        $query6 = Lead::select(
            'leads.lead_id',
            'leads.LeadName',
            'product_categories_tb.category_name',
            'products.productname',
            'leads.converteddate',
            'quotationproduct.grandtotal',
            DB::raw("
                CASE
                    WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                    WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                    WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                    WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                    ELSE 'N/A'
                END as stage
            ")
        )
        ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
        ->leftJoin('products', 'leads.products', '=', 'products.id')
        ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
        ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
        ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
        ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        $leads6 = $query6->orderBy('leads.converteddate', 'desc')->get();


        $query7 = Lead::select(
            'leads.lead_id',
            'leads.LeadName',
            'product_categories_tb.category_name',
            'products.productname',
            'leads.converteddate',
            'quotationproduct.grandtotal',
            DB::raw("
                CASE
                    WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                    WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                    WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                    WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                    ELSE 'N/A'
                END as stage
            ")
        )
        ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
        ->leftJoin('products', 'leads.products', '=', 'products.id')
        ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
        ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
        ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
        ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        $leads7 = $query7->orderBy('leads.converteddate', 'desc')->get();


        return view('pages.reports', compact('leads','leads2','leads3','leads4','leads5','leads6','leads7'));
    }


    public function equipfilter(Request $request)
    {
        $query = Lead::select(
                'leads.lead_id',
                'leads.LeadName',
                'product_categories_tb.category_name',
                'products.productname',
                'leads.converteddate',
                'quotationproduct.grandtotal',
                DB::raw("
                    CASE
                        WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                        WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                        WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                        WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                        ELSE 'N/A'
                    END as stage
                ")
            )
            ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
            ->leftJoin('products', 'leads.products', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
            ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
            ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
            ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        // ✅ Apply Filters (same as before)
        if ($request->fromdate && $request->todate) {
            $query->whereBetween('leads.converteddate', [
                Carbon::parse($request->fromdate)->startOfDay(),
                Carbon::parse($request->todate)->endOfDay()
            ]);
        }

        if ($request->assigned_to) {
            $query->where('leads.assigned_to', $request->assigned_to);
        }

        if ($request->category_id) {
            $query->where('product_categories_tb.id', $request->category_id);
        }

        if ($request->time_filter) {
            switch ($request->time_filter) {
                case 'monthly':
                    $query->whereMonth('leads.converteddate', Carbon::now()->month);
                    break;
                case 'weekly':
                    $query->whereBetween('leads.converteddate', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'quarterly':
                    $currentQuarter = ceil(Carbon::now()->month / 3);
                    $query->whereRaw('QUARTER(leads.converteddate) = ?', [$currentQuarter]);
                    break;
                case 'fy':
                    $startFy = Carbon::createFromDate(Carbon::now()->year, 4, 1);
                    $endFy = Carbon::createFromDate(Carbon::now()->year + 1, 3, 31);
                    $query->whereBetween('leads.converteddate', [$startFy, $endFy]);
                    break;
            }
        }

        $lead = $query->orderBy('leads.converteddate', 'desc')->get();
        return response()->json($lead);
    }


    public function sparserfilter(Request $request)
    {
        $query = Lead::select(
                'leads.lead_id',
                'leads.LeadName',
                'product_categories_tb.category_name',
                'products.productname',
                'leads.converteddate',
                'quotationproduct.grandtotal',
                DB::raw("
                    CASE
                        WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                        WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                        WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                        WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                        ELSE 'N/A'
                    END as stage
                ")
            )
            ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
            ->leftJoin('products', 'leads.products', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
            ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
            ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
            ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        // ✅ Apply Filters (same as before)
        if ($request->fromdate && $request->todate) {
            $query->whereBetween('leads.converteddate', [
                Carbon::parse($request->fromdate)->startOfDay(),
                Carbon::parse($request->todate)->endOfDay()
            ]);
        }

        if ($request->assigned_to) {
            $query->where('leads.assigned_to', $request->assigned_to);
        }

        if ($request->category_id) {
            $query->where('product_categories_tb.id', $request->category_id);
        }

        if ($request->time_filter) {
            switch ($request->time_filter) {
                case 'monthly':
                    $query->whereMonth('leads.converteddate', Carbon::now()->month);
                    break;
                case 'weekly':
                    $query->whereBetween('leads.converteddate', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'quarterly':
                    $currentQuarter = ceil(Carbon::now()->month / 3);
                    $query->whereRaw('QUARTER(leads.converteddate) = ?', [$currentQuarter]);
                    break;
                case 'fy':
                    $startFy = Carbon::createFromDate(Carbon::now()->year, 4, 1);
                    $endFy = Carbon::createFromDate(Carbon::now()->year + 1, 3, 31);
                    $query->whereBetween('leads.converteddate', [$startFy, $endFy]);
                    break;
            }
        }

        $lead = $query->orderBy('leads.converteddate', 'desc')->get();
        return response()->json($lead);
    }


    public function sparserbillfilter(Request $request)
    {
        $query = Lead::select(
                'leads.lead_id',
                'leads.LeadName',
                'product_categories_tb.category_name',
                'products.productname',
                'leads.converteddate',
                'quotationproduct.grandtotal',
                DB::raw("
                    CASE
                        WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                        WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                        WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                        WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                        ELSE 'N/A'
                    END as stage
                ")
            )
            ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
            ->leftJoin('products', 'leads.products', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
            ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
            ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
            ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        // ✅ Apply Filters (same as before)
        if ($request->fromdate && $request->todate) {
            $query->whereBetween('leads.converteddate', [
                Carbon::parse($request->fromdate)->startOfDay(),
                Carbon::parse($request->todate)->endOfDay()
            ]);
        }

        if ($request->assigned_to) {
            $query->where('leads.assigned_to', $request->assigned_to);
        }

        if ($request->category_id) {
            $query->where('product_categories_tb.id', $request->category_id);
        }

        if ($request->time_filter) {
            switch ($request->time_filter) {
                case 'monthly':
                    $query->whereMonth('leads.converteddate', Carbon::now()->month);
                    break;
                case 'weekly':
                    $query->whereBetween('leads.converteddate', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'quarterly':
                    $currentQuarter = ceil(Carbon::now()->month / 3);
                    $query->whereRaw('QUARTER(leads.converteddate) = ?', [$currentQuarter]);
                    break;
                case 'fy':
                    $startFy = Carbon::createFromDate(Carbon::now()->year, 4, 1);
                    $endFy = Carbon::createFromDate(Carbon::now()->year + 1, 3, 31);
                    $query->whereBetween('leads.converteddate', [$startFy, $endFy]);
                    break;
            }
        }

        $lead = $query->orderBy('leads.converteddate', 'desc')->get();
        return response()->json($lead);
    }


    public function equipbillfilter(Request $request)
    {
        $query = Lead::select(
                'leads.lead_id',
                'leads.LeadName',
                'product_categories_tb.category_name',
                'products.productname',
                'leads.converteddate',
                'quotationproduct.grandtotal',
                DB::raw("
                    CASE
                        WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                        WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                        WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                        WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                        ELSE 'N/A'
                    END as stage
                ")
            )
            ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
            ->leftJoin('products', 'leads.products', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
            ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
            ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
            ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        // ✅ Apply Filters (same as before)
        if ($request->fromdate && $request->todate) {
            $query->whereBetween('leads.converteddate', [
                Carbon::parse($request->fromdate)->startOfDay(),
                Carbon::parse($request->todate)->endOfDay()
            ]);
        }

        if ($request->assigned_to) {
            $query->where('leads.assigned_to', $request->assigned_to);
        }

        if ($request->category_id) {
            $query->where('product_categories_tb.id', $request->category_id);
        }

        if ($request->time_filter) {
            switch ($request->time_filter) {
                case 'monthly':
                    $query->whereMonth('leads.converteddate', Carbon::now()->month);
                    break;
                case 'weekly':
                    $query->whereBetween('leads.converteddate', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'quarterly':
                    $currentQuarter = ceil(Carbon::now()->month / 3);
                    $query->whereRaw('QUARTER(leads.converteddate) = ?', [$currentQuarter]);
                    break;
                case 'fy':
                    $startFy = Carbon::createFromDate(Carbon::now()->year, 4, 1);
                    $endFy = Carbon::createFromDate(Carbon::now()->year + 1, 3, 31);
                    $query->whereBetween('leads.converteddate', [$startFy, $endFy]);
                    break;
            }
        }

        $lead = $query->orderBy('leads.converteddate', 'desc')->get();
        return response()->json($lead);
    }


    public function openorderfilter(Request $request)
    {
        $query = Lead::select(
                'leads.lead_id',
                'leads.LeadName',
                'product_categories_tb.category_name',
                'products.productname',
                'leads.converteddate',
                'quotationproduct.grandtotal',
                DB::raw("
                    CASE
                        WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                        WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                        WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                        WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                        ELSE 'N/A'
                    END as stage
                ")
            )
            ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
            ->leftJoin('products', 'leads.products', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
            ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
            ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
            ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        // ✅ Apply Filters (same as before)
        if ($request->fromdate && $request->todate) {
            $query->whereBetween('leads.converteddate', [
                Carbon::parse($request->fromdate)->startOfDay(),
                Carbon::parse($request->todate)->endOfDay()
            ]);
        }

        if ($request->assigned_to) {
            $query->where('leads.assigned_to', $request->assigned_to);
        }

        if ($request->category_id) {
            $query->where('product_categories_tb.id', $request->category_id);
        }

        if ($request->time_filter) {
            switch ($request->time_filter) {
                case 'monthly':
                    $query->whereMonth('leads.converteddate', Carbon::now()->month);
                    break;
                case 'weekly':
                    $query->whereBetween('leads.converteddate', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'quarterly':
                    $currentQuarter = ceil(Carbon::now()->month / 3);
                    $query->whereRaw('QUARTER(leads.converteddate) = ?', [$currentQuarter]);
                    break;
                case 'fy':
                    $startFy = Carbon::createFromDate(Carbon::now()->year, 4, 1);
                    $endFy = Carbon::createFromDate(Carbon::now()->year + 1, 3, 31);
                    $query->whereBetween('leads.converteddate', [$startFy, $endFy]);
                    break;
            }
        }

        $lead = $query->orderBy('leads.converteddate', 'desc')->get();
        return response()->json($lead);
    }

    public function monbookfilter(Request $request)
    {
        $query = Lead::select(
                'leads.lead_id',
                'leads.LeadName',
                'product_categories_tb.category_name',
                'products.productname',
                'leads.converteddate',
                'quotationproduct.grandtotal',
                DB::raw("
                    CASE
                        WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                        WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                        WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                        WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                        ELSE 'N/A'
                    END as stage
                ")
            )
            ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
            ->leftJoin('products', 'leads.products', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
            ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
            ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
            ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        // ✅ Apply Filters (same as before)
        if ($request->fromdate && $request->todate) {
            $query->whereBetween('leads.converteddate', [
                Carbon::parse($request->fromdate)->startOfDay(),
                Carbon::parse($request->todate)->endOfDay()
            ]);
        }

        if ($request->assigned_to) {
            $query->where('leads.assigned_to', $request->assigned_to);
        }

        if ($request->category_id) {
            $query->where('product_categories_tb.id', $request->category_id);
        }

        if ($request->time_filter) {
            switch ($request->time_filter) {
                case 'monthly':
                    $query->whereMonth('leads.converteddate', Carbon::now()->month);
                    break;
                case 'weekly':
                    $query->whereBetween('leads.converteddate', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'quarterly':
                    $currentQuarter = ceil(Carbon::now()->month / 3);
                    $query->whereRaw('QUARTER(leads.converteddate) = ?', [$currentQuarter]);
                    break;
                case 'fy':
                    $startFy = Carbon::createFromDate(Carbon::now()->year, 4, 1);
                    $endFy = Carbon::createFromDate(Carbon::now()->year + 1, 3, 31);
                    $query->whereBetween('leads.converteddate', [$startFy, $endFy]);
                    break;
            }
        }

        $lead = $query->orderBy('leads.converteddate', 'desc')->get();
        return response()->json($lead);
    }



    public function monbillfilter(Request $request)
    {
        $query = Lead::select(
                'leads.lead_id',
                'leads.LeadName',
                'product_categories_tb.category_name',
                'products.productname',
                'leads.converteddate',
                'quotationproduct.grandtotal',
                DB::raw("
                    CASE
                        WHEN orf.leano IS NOT NULL THEN 'ORF Stage'
                        WHEN quotationproduct.leadno IS NOT NULL THEN 'Quotation Stage'
                        WHEN quotations.id_lead IS NOT NULL THEN 'Quotation Stage'
                        WHEN designs.idlead IS NOT NULL THEN 'Design Stage'
                        ELSE 'N/A'
                    END as stage
                ")
            )
            ->leftJoin('product_categories_tb', 'leads.category', '=', 'product_categories_tb.id')
            ->leftJoin('products', 'leads.products', '=', 'products.id')
            ->leftJoin(DB::raw('(SELECT * FROM quotationproduct WHERE id IN (SELECT MAX(id) FROM quotationproduct GROUP BY leadno)) as quotationproduct'), 'leads.id', '=', 'quotationproduct.leadno')
            ->leftJoin('orf', 'leads.id', '=', 'orf.leano')
            ->leftJoin('quotations', 'leads.id', '=', 'quotations.id_lead')
            ->leftJoin('designs', 'leads.id', '=', 'designs.idlead');

        // ✅ Apply Filters (same as before)
        if ($request->fromdate && $request->todate) {
            $query->whereBetween('leads.converteddate', [
                Carbon::parse($request->fromdate)->startOfDay(),
                Carbon::parse($request->todate)->endOfDay()
            ]);
        }

        if ($request->assigned_to) {
            $query->where('leads.assigned_to', $request->assigned_to);
        }

        if ($request->category_id) {
            $query->where('product_categories_tb.id', $request->category_id);
        }

        if ($request->time_filter) {
            switch ($request->time_filter) {
                case 'monthly':
                    $query->whereMonth('leads.converteddate', Carbon::now()->month);
                    break;
                case 'weekly':
                    $query->whereBetween('leads.converteddate', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'quarterly':
                    $currentQuarter = ceil(Carbon::now()->month / 3);
                    $query->whereRaw('QUARTER(leads.converteddate) = ?', [$currentQuarter]);
                    break;
                case 'fy':
                    $startFy = Carbon::createFromDate(Carbon::now()->year, 4, 1);
                    $endFy = Carbon::createFromDate(Carbon::now()->year + 1, 3, 31);
                    $query->whereBetween('leads.converteddate', [$startFy, $endFy]);
                    break;
            }
        }

        $lead = $query->orderBy('leads.converteddate', 'desc')->get();
        return response()->json($lead);
    }

}
