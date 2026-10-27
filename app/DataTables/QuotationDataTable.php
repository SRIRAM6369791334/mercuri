<?php

namespace App\DataTables;

use App\Models\AdditionalPOOrder;
use App\Models\Freight;
use App\Models\Lead;
use App\Models\Orf;
use App\Models\Quotation;
use App\Models\QuotationProduct;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class QuotationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('quote_date', function ($query) {
                return $query->quote_date ? \Carbon\Carbon::parse($query->quote_date)->format('d-m-Y') : "-";
            })
            ->addColumn('quota_id', function ($query) {
                $html = '<div class="d-flex justify-content-end" style="padding: 10px;">';

                // Quotation status
                $status = $query->quto_app;
                $badgeClass = $status == 0 ? 'bg-warning' : ($status == 1 ? 'bg-danger quto-revision' : 'bg-success');
                $tooltip = $status == 0 ? 'Quotation Pending' : ($status == 1 ? 'Quotation Revision' : 'Quotation Approved');
                $label = $status == 0 ? 'P' : ($status == 1 ? 'R' : 'A');
                $cursor = $status == 1 ? 'pointer' : 'default';
                // $html .= '<span
                // data-bs-toggle="' . ($status == 1 ? 'tooltip' : '') . '"
                // data-bs-placement="top"
                // class="badge ms-1 ' . $badgeClass . '"
                // data-bs-original-title="' . $tooltip . '"
                // data-id="' . $query->id . '"
                // data-remark="' . $query->qutoremark . '"
                // style="cursor: ' . $cursor . ';">
                // ' . $label . '
                // </span>';

                // Example conditions — make sure these vars are available or calculated inside the closure

                $freight = Freight::where(
                    'quotationno',
                    $query->quot_product_id,
                )->first();
                $isApproved = false;
                if ($freight) {
                    $isApproved = Term::where(function ($query) use ($freight, ) {
                        $query
                            ->where('id', $freight->termscondition)
                            ->orWhere('id', $freight->warranty);
                    })->where('term_approve', 1)->exists();
                }

                $isRejected = false;
                $termRemark = null;

                if ($freight) {
                    $rejectedTerm = Term::where(function ($query) use ($freight, ) {
                        $query
                            ->where('id', $freight->termscondition)
                            ->orWhere('id', $freight->warranty);
                    })
                        ->where('term_approve', 2)
                        ->first();

                    if ($rejectedTerm) {
                        $isRejected = true;
                        $termRemark = $rejectedTerm->term_remark;
                    }
                }

                $quotationProduct = QuotationProduct::find($query->quot_product_id);
                $disApproved = $quotationProduct && $quotationProduct->dis_app == 1;
                $disnotApproved = $quotationProduct && $quotationProduct->dis_app == 0;
                $frieghtApproved = $quotationProduct && $quotationProduct->freight_approval == 1;
                $frieghtRejected = $quotationProduct && $quotationProduct->freight_approval == 2;
                $quotationApproved = $quotationProduct && $quotationProduct->quto_app == 2;

                if ($isApproved) {
                    $html .= '<span data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-original-title="Terms Approval Pending"
                    class="badge bg-info ms-1">TAP</span>';
                }

                if ($isRejected) {
                    $html .= '<span data-bs-toggle="modal" data-bs-target="#termRemarkModal"
                    data-remark="' . $termRemark . '" class="badge bg-danger ms-1 term-reject"
                    style="cursor:pointer;">TAR</span>';
                }

                if ($disApproved) {
                    $html .= '<span data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-original-title="Waiting for Discount Approval"
                    class="badge bg-warning ms-1">DAP</span>';
                }

                if ($quotationProduct && $quotationProduct->dis_app == 2) {
                    $html .= '<span data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-original-title="Discount Approved"
                    class="badge bg-success ms-1">DA</span>';
                }

                // if ($disnotApproved) {
                //     $html .= '<span class="badge bg-danger ms-1 dis-rejected"
                //     data-id="' . $query->id . '"
                //     data-remark="' . $query->disremark . '" data-bs-toggle="tooltip"
                //     data-bs-placement="top" data-bs-original-title="Discount Approval Rejected"
                //     style="cursor: pointer;">
                //     DR
                //     </span>';
                // }

                if ($frieghtApproved) {
                    $html .= '<span class="badge bg-info ms-1 dis-rejected"
                    data-id="' . $query->id . '"
                    data-remark="' . $query->disremark . '" data-bs-toggle="tooltip"
                    data-bs-placement="top" data-bs-original-title="Frieght Approval Pending"
                    style="cursor: pointer;"> FAP
                    </span>';
                }

                if ($frieghtRejected) {
                    $html .= '<span class="badge bg-danger ms-1 dis-rejected"
                    data-id="' . $query->id . '"
                    data-remark="' . $query->disremark . '" data-bs-toggle="tooltip"
                    data-bs-placement="top" data-bs-original-title="Frieght Rejected"
                    style="cursor: pointer;"> FR
                    </span>';
                }

                // if ($quotationApproved) {
                //     $html .= '<span class="badge bg-success ms-1 dis-rejected"
                //     data-id="' . $query->id . '"
                //     data-remark="' . $query->disremark . '" data-bs-toggle="tooltip"
                //     data-bs-placement="top" data-bs-original-title="Approved"
                //     style="cursor: pointer;"> Approved
                //     </span>';
                // }

                $html .= '</div>';

                // Return the full content including quota_id and status
                return '<b>' . $query->quota_id . '</b>' . $html;
            })

            ->addColumn('citylist', function ($query) {
                return $query->city_name ?? "-";
            })
            ->addColumn('Leadsource', function ($query) {
                return $query->source_name ?? "-";
            })
            ->addColumn('Leadstatus', function ($query) {
                return $query->status_name ?? "-";
            })
            ->addColumn('contactname', function ($query) {
                return $query->contactname ?? "-";
            })
            ->addColumn('phonumber', function ($query) {
                return $query->phonumber ?? "-";
            })
            ->addColumn('category_name', function ($query) {
                return $query->category_name ?? "-";
            })
            ->addColumn('subcategory_name', function ($query) {
                return $query->subcategory_name ?? "-";
            })
            ->addColumn('productname', function ($query) {
                $productname = $query->productname ?? "-";
                $rate = $query->rate ?? "-";
                return "<div data-bs-toggle='tooltip' data-bs-placement='top' title='{$rate}' class='dt-wrap' style='width: 250px'>{$productname}</div>";
            })
            // ->addColumn('grand_total', function ($query) {
            //     $rate = $query->grand_total ?? "-";
            //     return "<b class='dt-wrap' style='color: #dc3545'>{$rate}</b>";
            // })
                 ->addColumn('grand_total', function ($query) {
    return $query->grand_total ?? 0;
})
            ->addColumn('assigned_to', function ($query) {
                return $query->name ?? "-";
            })
            ->addColumn('quota_status', function ($query) {
                $textColor = "#000000";
                // if ($query->quotation_status_id == "1") {
                //     $textColor = '#20c997';
                // } elseif ($query->quotation_status_id == "5") {
                //     $textColor = '#ffc107';
                // } elseif ($query->quotation_status_id == "3") {
                //     $textColor = '#dc3545';
                // } elseif ($query->quotation_status_id == "4") {
                //     $textColor = '#052c65';
                // }elseif ($query->quotation_status_id == "2"){
                //     $textColor = '#0dcaf0';
                // }
                $status = $query->quota_status ?? "-";
                $style = 'color:' . $textColor . ';';
                return "<div class='qutochange-status-btn cursor_pointer' data-bs-toggle='modal' data-bs-target='#qutostatusModal' data-id='{$query->id}' data-leadremark='{$query->remarks}' data-leadga='{$query->ganumber}' data-quotstatusid='{$query->quotation_status_id}' style='{$style}'>{$status}</div>";
            })
            ->addColumn('closure_date', function ($query) {
    $date = $query->closure_date
        ? \Carbon\Carbon::parse($query->closure_date)->format('d-m-Y')
        : 'Set Date';

    return "<span class='closure-date-btn text-primary cursor_pointer'
                data-id='{$query->id}'
                data-date='{$query->closure_date}'
                data-bs-toggle='modal'
                data-bs-target='#closureDateModal'>
                {$date}
            </span>";
})
            // ->addColumn('LeadName', function ($query) {
            //     $url = url('/singlequota/' . $query->id);
            //     return '<div style="width: 200px"><a href="' . $url . '">' . e($query->LeadName) . '</a></div>';
            // })
            ->addColumn('LeadName', function ($query) {
    $url = url('/singlequota/' . $query->id);

    return '<a href="'.$url.'" class="dt-lead-name">'
        . e($query->LeadName) .
    '</a>';
})

            ->addColumn('action', function ($query) {
                $leadId = $query->id;

                // Check if any quotation record exists for this lead
                $quotationExists = Quotation::where('id_lead', $leadId)->exists();
                $orfExists = Orf::where('quoproid', $query->quotation_id)->exists();

                if (!$quotationExists) {
                    // Show 'Create' button
                    if (auth()->user()->can('quotation-create')) {
                        return "<a href='/createquota/{$leadId}'><button class='btn btn-success waves-effect waves-light'>Create</button></a>";
                    }
                    return "-";
                }

                // Show Edit and View buttons if quotation exists
                if (!empty($query->grand_total)) {
                    $action = "<div class='quota-action-btns'>";
                    if (auth()->user()->can('quotation-edit')) {
                        $action .= "<a href='/editquota/{$query->quotation_id}'><button class='btn btn-bull_yellow edit-btn waves-effect waves-light'>Edit</button></a>";
                    }

                    if ($query->dis_app == 2 && !$orfExists && $query->freight_approval == 0 && auth()->user()->can('orf-create')) {
                        $action .= "<a href='/orf/{$query->quotation_id}'><button style='width:120px' class='btn btn-primary waves-effect waves-light'>Move to ORF</button></a>";
                    }

                    if (auth()->user()->can('quotation-display')) {
                        $action .= "<a href='/quotation/view/{$query->quotation_id}' target='_blank'><button class='btn btn-info waves-effect waves-light'>View</button></a>";
                    }
                    $action .= "</div>";

                    return $action;
                }

                return "-";
            })
            ->filterColumn('quote_date', function ($query, $keyword) {
                $query->where('quotations.created_at', 'like', "%{$keyword}%");
            })
            ->filterColumn('quota_id', function ($query, $keyword) {
                $query->where('quotations.quota_id', 'like', "%{$keyword}%");
            })
            ->filterColumn('Leadsource', function ($query, $keyword) {
                $query->where('source.dropdowndata', 'like', "%{$keyword}%");
            })
            ->filterColumn('Leadstatus', function ($query, $keyword) {
                $query->where('status.dropdowndata', 'like', "%{$keyword}%");
            })
            ->filterColumn('LeadName', function ($query, $keyword) {
                $query->where('leads.LeadName', 'like', "%{$keyword}%");
            })
            ->filterColumn('citylist', function ($query, $keyword) {
                $query->where('city_list.city_name', 'like', "%{$keyword}%");
            })
            ->filterColumn('contactname', function ($query, $keyword) {
                $query->where('leads.contactname', 'like', "%{$keyword}%");
            })
            ->filterColumn('phonumber', function ($query, $keyword) {
                $query->where('leads.phonumber', 'like', "%{$keyword}%");
            })
            ->filterColumn('category_name', function ($query, $keyword) {
                $query->where('category_name', 'like', "%{$keyword}%");
            })
            ->filterColumn('subcategory_name', function ($query, $keyword) {
                $query->where('subcategory_name', 'like', "%{$keyword}%");
            })
            ->filterColumn('productname', function ($query, $keyword) {
                $query->where('productname', 'like', "%{$keyword}%");
            })
            ->filterColumn('grand_total', function ($query, $keyword) {
                $query->where('grand_total', 'like', "%{$keyword}%");
            })
            ->filterColumn('assigned_to', function ($query, $keyword) {
                $query->where('users.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('quota_status', function ($query, $keyword) {
                $query->where('quotation_status.name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['LeadName', 'action', 'closure_date','productname', 'quota_id', 'grand_total', 'quota_status'])
            ->addIndexColumn()
            ->setRowId('id');


    }

    /**
     * Get the query source of dataTable.
     */
    // public function query(Lead $model): QueryBuilder
    // {

    // $user = Auth::user();
    // $role = $user->role;

    //     $latestQuotationProduct = DB::table('quotationproduct as qp1')
    //         ->select('qp1.*')
    //         ->whereRaw('qp1.id = (
    //         SELECT MAX(qp2.id)
    //         FROM quotationproduct as qp2
    //         WHERE qp2.leadno = qp1.leadno
    //     )');
    //     $latestQuotation = DB::table('quotations as q1')
    // ->select('q1.*')
    // ->whereRaw('q1.id = (
    //     SELECT MAX(q2.id)
    //     FROM quotations as q2
    //     WHERE q2.id_lead = q1.id_lead
    // )');



    //     $quotationCountSub = DB::table('quotationproduct')
    //         ->select('leadno', DB::raw('COUNT(*) as quotation_count'))
    //         ->groupBy('leadno');

    //     return $model->newQuery()
    //         ->select([
    //             'leads.*',
    //             'source.dropdowndata as source_name',
    //             'status.dropdowndata as status_name',
    //             'city_list.city_name',
    //             'users.name',
    //             'quotation_status.name as quota_status',
    //             'quotation_status.id as quotation_status_id',
    //             'quotations.quota_id',
    //             'quotations.id as quotation_id',
    //             'quotations.created_at as quote_date',
    //             'product_categories_tb.category_name',
    //             'product_categories_tb.sub_category',
    //             'products.productname',
    //             'product_sub_categories.subcategory_name',
    //             'products.rate',
    //             'quotationproduct.grandtotal',
    //             'quotations.grand_total',
    //             'quotationproduct.id as quot_product_id',
    //             'quotations.dis_app',
    //             'quotationproduct.freight_approval',
    //             DB::raw('IFNULL(quotation_count.quotation_count, 0) as quotation_count'),
    //         ])
    //         ->leftJoin('dropdowndatas as source', 'source.id', '=', 'leads.Leadsource')
    //         ->leftJoin('dropdowndatas as status', 'status.id', '=', 'leads.Leadstatus')
    //         ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
    //         ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
    //         ->leftJoin('quotation_status', 'quotation_status.id', '=', 'leads.quto_status')
    //         // ->leftJoin('quotations', 'quotations.id_lead', '=', 'leads.id')
    //         ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
    //         ->leftJoin('products', 'products.id', '=', 'leads.products')
    //         ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
    //         ->leftJoinSub($latestQuotationProduct, 'quotationproduct', function ($join) {
    //             $join->on('quotationproduct.leadno', '=', 'leads.id');
    //         })
    //         ->leftJoinSub($quotationCountSub, 'quotation_count', function ($join) {
    //             $join->on('quotation_count.leadno', '=', 'leads.id');
    //         })
    //          ->leftJoinSub($latestQuotation, 'quotations', function ($join) {
    //     $join->on('quotations.id_lead', '=', 'leads.id');
    // })
    //         // ->where('leads.Leadstatus', '=', 343)
    //         ->where('leads.quota_proceed', '=', 1)
    //         ->whereNotExists(function ($q) {
    //     $q->select(DB::raw(1))
    //       ->from('orf')
    //       ->whereColumn('orf.leano', 'leads.id');
    // })
    //         ->where(function ($query) {
    //             $query->whereNotIn('leads.quto_status', [3, 4, 5])
    //                 ->orWhereNull('leads.quto_status');
    //         })
    //         ->orderBy('leads.updated_at', 'desc');
    // }
public function query(Lead $model): QueryBuilder
{
    $user = Auth::user();
    $role = $user->role;

    /* -------------------------------------------------
     | SUBQUERIES
     ------------------------------------------------- */

    // Latest quotationproduct per lead
    $latestQuotationProduct = DB::table('quotationproduct as qp1')
        ->select('qp1.*')
        ->whereRaw('qp1.id = (
            SELECT MAX(qp2.id)
            FROM quotationproduct as qp2
            WHERE qp2.leadno = qp1.leadno
        )');

    // Latest quotation per lead
    $latestQuotation = DB::table('quotations as q1')
        ->select('q1.*')
        ->whereRaw('q1.id = (
            SELECT MAX(q2.id)
            FROM quotations as q2
            WHERE q2.id_lead = q1.id_lead
        )');

    // Quotation count per lead
    $quotationCountSub = DB::table('quotationproduct')
        ->select('leadno', DB::raw('COUNT(*) as quotation_count'))
        ->groupBy('leadno');

    /* -------------------------------------------------
     | MAIN QUERY
     ------------------------------------------------- */

    $query = $model->newQuery()
        ->select([
            'leads.*',
            'source.dropdowndata as source_name',
            'status.dropdowndata as status_name',
            'city_list.city_name',
            'users.name as sales_name',
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
            'quotations.grand_total',
            'quotationproduct.id as quot_product_id',
            'quotations.dis_app',
            'quotationproduct.freight_approval',
            DB::raw('IFNULL(quotation_count.quotation_count, 0) as quotation_count'),
        ])

        ->leftJoin('dropdowndatas as source', 'source.id', '=', 'leads.Leadsource')
        ->leftJoin('dropdowndatas as status', 'status.id', '=', 'leads.Leadstatus')
        ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
        ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
        ->leftJoin('quotation_status', 'quotation_status.id', '=', 'leads.quto_status')
        ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
        ->leftJoin('products', 'products.id', '=', 'leads.products')
        ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')

        ->leftJoinSub($latestQuotationProduct, 'quotationproduct', function ($join) {
            $join->on('quotationproduct.leadno', '=', 'leads.id');
        })

        ->leftJoinSub($quotationCountSub, 'quotation_count', function ($join) {
            $join->on('quotation_count.leadno', '=', 'leads.id');
        })

        ->leftJoinSub($latestQuotation, 'quotations', function ($join) {
            $join->on('quotations.id_lead', '=', 'leads.id');
        })

        // Only proceeded quotations
        ->where('leads.quota_proceed', 1)

        // Exclude ORF-created leads
        ->whereNotExists(function ($q) {
            $q->select(DB::raw(1))
              ->from('orf')
              ->whereColumn('orf.leano', 'leads.id');
        })

        // Exclude closed / rejected quotation statuses
        ->where(function ($q) {
            $q->whereNotIn('leads.quto_status', [3, 4, 5])
              ->orWhereNull('leads.quto_status');
        });

    /* -------------------------------------------------
     | ROLE BASED FILTERING
     ------------------------------------------------- */

    // 🔹 Super Admin → see all (no filter)
    if ($role === 'Super Admin') {
        // nothing to add
    }

    // 🔹 Sales Manager → only their own leads
    elseif ($role === 'Sales Manager') {
        $query->where('leads.assigned_to', $user->id);
    }

    // 🔹 Other roles → no data (safe default)
    else {
        $query->whereRaw('1 = 0');
    }

    return $query->orderBy('leads.updated_at', 'desc');
}


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('leadsq')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->serverSide(true) // Enable server-side processing
            ->processing(true) // Show processing indicator
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => ['excel'],
                'scrollX'      => true,
            ])
            ->orderBy(1)
            ->selectStyleSingle()
            ->fixedColumns(true);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('S.No.')->orderable(false),
            Column::make('quote_date')->title('Date'),
            Column::make('quota_id')->title('Quotation No'),
            // Column::make('LeadName')->title('Customer Name'),
            Column::make('LeadName')
    ->title('Customer Name')
    ->width(220),
            Column::make('citylist')->title('City'),
            Column::make('contactname')->title('Contact Name'),
            Column::make('phonumber')->title('Contact No'),
            Column::make('category_name')->title('Segment'),
            // Column::make('subcategory_name')->title('Group'),
            // Column::make('productname')->title('Model'),
            Column::make('grand_total')->title('Value'),
            Column::make('assigned_to')->title('Sales Manager'),
            Column::make('quota_status')->title('Status'),
            Column::make('closure_date')->title('Closure Date')->width(140),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->width(60)->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Quotation_' . date('YmdHis');
    }
}
