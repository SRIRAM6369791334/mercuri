<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;

use App\Helpers\ResponseHelper;
use App\Models\QuotationProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Lead;


class QuotationControllerApi extends Controller
{

    public function quotationLeadData($userId)
    {
        $userDetails = User::find($userId);

        // Subquery: Latest quotationproduct per leadno
        $latestQuotationProduct = DB::table('quotationproduct as qp1')
            ->select('qp1.*')
            ->whereRaw('qp1.id = (
            SELECT MAX(qp2.id)
            FROM quotationproduct as qp2
            WHERE qp2.leadno = qp1.leadno
        )');

        // Subquery: Quotation count per leadno
        $quotationCountSub = DB::table('quotationproduct')
            ->select('leadno', DB::raw('COUNT(*) as quotation_count'))
            ->groupBy('leadno');

        // Base query
        $leadsQuery = DB::table('leads')->select([
            'leads.*',
            'source.dropdowndata as source_name',
            'status.dropdowndata as status_name',
            'city_list.city_name',
            'users.name',
            'quotation_status.name as quota_status',
            'quotation_status.id as quotation_status_id',
            'quotations.quota_id',
            'quotations.id as quotation_id',
            'quotations.created_at as quote_date',
            'product_categories_tb.category_name',
            'product_categories_tb.sub_category',
            'products.productname',
            'product_sub_categories.subcategory_name',
            'products.rate',
            'quotationproduct.grandtotal',
            'quotationproduct.id as quot_product_id',
            'quotationproduct.dis_app',
            'quotationproduct.freight_approval',
            DB::raw('IFNULL(quotation_count.quotation_count, 0) as quotation_count'),
        ])
            ->leftJoin('dropdowndatas as source', 'source.id', '=', 'leads.Leadsource')
            ->leftJoin('dropdowndatas as status', 'status.id', '=', 'leads.Leadstatus')
            ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
            ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
            ->leftJoin('quotation_status', 'quotation_status.id', '=', 'leads.quto_status')
            ->leftJoin('quotations', 'quotations.id_lead', '=', 'leads.id')
            ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
            ->leftJoin('products', 'products.id', '=', 'leads.products')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
            ->leftJoinSub($latestQuotationProduct, 'quotationproduct', function ($join) {
                $join->on('quotationproduct.leadno', '=', 'leads.id');
            })
            ->leftJoinSub($quotationCountSub, 'quotation_count', function ($join) {
                $join->on('quotation_count.leadno', '=', 'leads.id');
            })
            ->where('leads.Leadstatus', '=', 343)
            ->where('leads.quota_proceed', '=', 1)
            ->where(function ($query) {
                $query->whereNotIn('leads.quto_status', [3, 4, 5])
                    ->orWhereNull('leads.quto_status');
            });

        // Restrict data to assigned user if not Super Admin
        if (strtolower($userDetails->role) !== 'super admin') {
            $leadsQuery->where('leads.assigned_to', $userId);
        }

        $leads = $leadsQuery
            ->orderBy('leads.updated_at', 'desc')
            ->get();

        return response()->json([
            'status' => "success",
            'data' => $leads,
            'message' => 'Leads with quotation details retrieved successfully',
        ]);
    }


    public function filterQuotationFunction(Request $request)
    {
        $categoryId = $request->get('category_id');
        $subCategoryId = $request->get(key: 'sub_category_id');
        $productId = $request->get('product_id');
        $statusId = $request->get('status_id');
        $userId = $request->get('user_id');
        
        $userDetails = User::find($userId);

        $query = Lead::select([
            'leads.id',
            'leads.LeadName',
            'leads.contactname',
            'leads.phonumber',
            'leads.email',
            'city_list.city_name',
            'users.name as assigned_to',
            'quotation_status.name as quota_status',
            'quotation_status.id as quotation_status_id',
            'quotations.quota_id',
            'quotations.created_at as quote_date',
            'product_categories_tb.category_name',
            'product_sub_categories.subcategory_name',
            'products.productname',
            'products.rate',
            'quotationproduct.grandtotal',
            'quotationproduct.id as quot_product_id',
            'quotationproduct.dis_app',
        ])
            ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
            ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
            ->leftJoin('quotation_status', 'quotation_status.id', '=', 'leads.quto_status')
            ->leftJoin('quotations', 'quotations.id_lead', '=', 'leads.id')
            ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
            ->leftJoin('products', 'products.id', '=', 'leads.products')
            ->leftJoin('quotationproduct', function ($join) {
                $join->on('quotationproduct.leadno', '=', 'leads.id')
                    ->whereRaw('quotationproduct.id = (SELECT MAX(id) FROM quotationproduct WHERE leadno = leads.id)');
            });
            if(strtolower($userDetails->role) !== 'super admin'){
                $query->where('leads.assigned_to', $userId);
            }
            $query->where('leads.Leadstatus', 343)
            ->where('leads.quota_proceed', 1);

        if (!empty($categoryId) && $categoryId != 0) {
            $query->where('leads.category', $categoryId);
        }

        if (!empty($subCategoryId) && $subCategoryId != 0) {
            $query->where('leads.subcategory_id', $subCategoryId);
        }

        if (!empty($productId) && $productId != 0) {
            $query->where('leads.products', $productId);
        }

        if (!empty($statusId) && $statusId != 0) {
            $query->where('leads.quto_status', $statusId);
        }


        $data = $query->get()->map(function ($item) {
            // Action logic (same as main table)
            $quotationExists = QuotationProduct::where('leadno', $item->id)->exists();
            $action = "-";

            if (!$quotationExists) {
                $action = "<a href='/createquota/{$item->id}'><button class='btn btn-success btn-sm'>Create</button></a>";
            }

            if (!empty($item->grandtotal)) {
                $action = "<div class='quota-action-btns'>
                <a href='/editquota/{$item->quot_product_id}'><button class='btn btn-warning btn-sm'>Edit</button></a>";

                if ($item->dis_app == 2) {
                    $action .= "<a href='/orf/{$item->quot_product_id}'><button class='btn btn-primary btn-sm'>Move to ORF</button></a>";
                }

                $action .= "<a href='/quotation/view/{$item->quot_product_id}' target='_blank'><button class='btn btn-info btn-sm'>View</button></a>
            </div>";
            }

            return [
                'id' => $item->id,
                'quote_date' => $item->quote_date ? Carbon::parse($item->quote_date)->format('d-m-Y') : "-",
                'quota_id' => $item->quota_id ?? "-",
                'LeadName' => "<a href='/singlequota/{$item->id}'>{$item->LeadName}</a>",
                'city_name' => $item->city_name ?? "-",
                'contactname' => $item->contactname ?? "-",
                'phonumber' => $item->phonumber ?? "-",
                'category_name' => $item->category_name ?? "-",
                'subcategory_name' => $item->subcategory_name ?? "-",
                'productname' => $item->productname ?? "-",
                'grandtotal' => "<b style='color: #dc3545'>" . ($item->grandtotal ?? '-') . "</b>",
                'assigned_to' => $item->assigned_to ?? "-",
                'quota_status' => $item->quota_status ?? "-",
                'quotation_status_id' => $item->quotation_status_id ?? "",
                'action' => $action,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => "Filtered quotation list"
        ]);
    }

}
