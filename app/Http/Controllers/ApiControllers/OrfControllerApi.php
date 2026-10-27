<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;

use App\Models\Orf;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrfControllerApi extends Controller
{

    public function getOrfData()
    {
        $orfs = Orf::query()
            ->select([
                'orf.*',
                'leads.LeadName',
                'leads.assigned_to',
                'cate.category_name as cat_name',
                'users.name',
                'products.productname',
                'product_sub_categories.subcategory_name',
            ])
            ->where('orf.approval_status', '=', 1)
            ->where('orf.cs_status', '=', 1)
            ->leftJoin('leads', 'orf.leano', '=', 'leads.id')
            ->leftJoin('product_categories_tb as cate', 'cate.id', '=', 'leads.category')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
            ->leftJoin('products', 'products.id', '=', 'leads.products')
            ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')->get();

        return ResponseHelper::successResponseData('success', 'ORF List', $orfs);
    }

    public function getOrfApprovalPendingData()
    {
        $orfs = Orf::query()
            ->select([
                'orf.*',
                'leads.LeadName',
                'leads.assigned_to',
                'cate.category_name as cat_name',
                'products.productname',
                'product_sub_categories.subcategory_name',
                'users.name'
            ])
            ->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('orf.approval_status', 0)
                        ->where('orf.cs_status', 0);
                })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('orf.approval_status', 1)
                            ->where('orf.cs_status', 0);
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('orf.approval_status', 0)
                            ->where('orf.cs_status', 1);
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('orf.approval_status', 2)
                            ->where('orf.cs_status', 1);
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('orf.approval_status', 1)
                            ->where('orf.cs_status', 2);
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('orf.approval_status', 0)
                            ->where('orf.cs_status', 2);
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('orf.approval_status', 2)
                            ->where('orf.cs_status', 0);
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('orf.approval_status', 2)
                            ->where('orf.cs_status', 2);
                    });
            })
            ->leftJoin('leads', 'orf.leano', '=', 'leads.id')
            ->leftJoin('product_categories_tb as cate', 'cate.id', '=', 'leads.category')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
            ->leftJoin('products', 'products.id', '=', 'leads.products')
            ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')->get();

        return ResponseHelper::successResponseData('success', 'ORF Approval pending List', $orfs);
    }
}
