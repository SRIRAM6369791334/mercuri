<?php

namespace App\DataTables;

use App\Models\AdditionalPOOrder;
use App\Models\Lead;
use App\Models\Orf;
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

class OrfDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            // ->addColumn('Entrydate', function ($query) {
            //     return $query->Entrydate ?? "-";
            // })

            ->addColumn('cat_name', function ($query) {
                return $query->cat_name ?? "-";
            })
            ->addColumn('subcategory_name', function ($query) {
                return $query->subcategory_name ?? "-";
            })

            ->addColumn('productname', function ($query) {
                return "<div style='width: 200px'>" . $query->productname ?? '-' . "</div>";
            })

            ->addColumn('orfref', function ($query) {
                return $query->orfref ?? "-";
            })

            ->addColumn('assigned_to', function ($query) {
                return $query->name ?? "-";
            })
            ->addColumn('cs_status', function ($query) {
                if ($query->cs_status == 1) {
                    return "<span class='badge badge-success' style='background:#47bb1f;padding:8px;'>
                        <i class='fas fa-check-circle'></i> Approved
                    </span>";
                } elseif ($query->cs_status == 2) {
                    return "
                    <div class='dropdown'>
                        <button class='btn btn-danger btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-expanded='false'>
                            <i class='fas fa-times-circle'></i> Rejected
                        </button>
                        <div class='dropdown-menu' style='width:200px;'>
                            <span class='dropdown-item-text' style='font-size:12px;'>{$query->csreject_remark}</span>
                        </div>
                    </div>";
                }
                $button = "<span class='badge badge-warning";
                if (Auth::user()->role == 'Service Coordinator' || Auth::user()->role == 'Cs' || Auth::user()->role == 'Super Admin') {
                    $button .= " orf-csstatus-btn' data-bs-toggle='modal' data-bs-target='#statusModalcs' data-appsta='{$query->approval_status}' data-rid='{$query->id}'";
                } else {
                    $button .= "'";
                }

                $button .= "style='background:#efa213;padding:8px;cursor:pointer'>
                                <i class='fas fa-hourglass-half'></i> Pending
                            </span>";
                return $button;
            })
            ->addColumn('approval_status', function ($query) {

    // 🔒 Sales status visible ONLY after CS approval
    if ($query->cs_status != 1) {
        return "<span class='badge badge-secondary' style='padding:8px;'>
                    Waiting for CS Approval
                </span>";
    }

    // CS Approved → Show Sales Status
    if ($query->approval_status == 1) {
        return "<span class='badge badge-success' style='background:#47bb1f;padding:8px;'>
            <i class='fas fa-check-circle'></i> Approved
        </span>";
    }
    elseif ($query->approval_status == 2) {
        return "
        <div class='dropdown'>
            <button class='btn btn-danger btn-sm dropdown-toggle' type='button' data-toggle='dropdown'>
                <i class='fas fa-times-circle'></i> Rejected
            </button>
            <div class='dropdown-menu' style='width:200px;'>
                <span class='dropdown-item-text' style='font-size:12px;'>
                    {$query->reject_remark}
                </span>
            </div>
        </div>";
    }

    // Pending (ONLY after CS approval)
    if (Auth::user()->role == 'Sales Manager' || Auth::user()->role == 'Super Admin') {
        return "<span class='badge badge-warning orf-status-btn'
                    data-bs-toggle='modal'
                    data-bs-target='#statusModal'
                    data-cssta='{$query->cs_status}'
                    data-id='{$query->id}'
                    style='background:#efa213;padding:8px;cursor:pointer'>
                    <i class='fas fa-hourglass-half'></i> Pending
                </span>";
    }

    return "<span class='badge badge-warning' style='padding:8px;'>
                <i class='fas fa-hourglass-half'></i> Pending
            </span>";
})

            // ->addColumn('approval_status', function ($query) {
            //     if ($query->approval_status == 1) {
            //         return "<span class='badge badge-success' style='background:#47bb1f;padding:8px;'>
            //             <i class='fas fa-check-circle'></i> Approved
            //         </span>";
            //     } elseif ($query->approval_status == 2) {
            //         return "
            //         <div class='dropdown'>
            //             <button class='btn btn-danger btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-expanded='false'>
            //                 <i class='fas fa-times-circle'></i> Rejected
            //             </button>
            //             <div class='dropdown-menu' style='width:200px;'>
            //                 <span class='dropdown-item-text' style='font-size:12px;'>{$query->reject_remark}</span>
            //             </div>
            //         </div>";
            //     }

            //     $button = "<span class='badge badge-warning";

            //     if (Auth::user()->role == 'Sales Manager' || Auth::user()->role == 'Super Admin') {
            //         $button .= " orf-status-btn' data-bs-toggle='modal' data-bs-target='#statusModale' data-cssta='{$query->cs_status}' data-id='{$query->id}'";
            //     } else {
            //         $button .= "'";
            //     }

            //     $button .= "style='background:#efa213;padding:8px;cursor: pointer'> <i class='fas fa-hourglass-half'></i> Pending </span>";

            //     return $button;
            // })

            ->addColumn('LeadName', function ($query) {
                return $query->LeadName ?? "-";
            })

            ->addColumn('view', function ($query) {
                $quotUrl = url('/quotation/view/' . $query->quoproid);
                $viewUrl = url('/orfdetail/view/' . $query->quoproid);

                $buttons = "<div style='display: flex; gap: 5px; justify-content: center;'>";
                if (auth()->user()->can('quotation-display')) {
                    $buttons .= "<a href='{$quotUrl}' target='_blank'>
                        <button style='color:#fff;background:#1b5583;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;'>
                            Quot
                        </button>
                    </a>";
                }
                if (auth()->user()->can('orf-display')) {
                    $buttons .= "<a href='{$viewUrl}' target='_blank'>
                        <button style='color:#fff;background:#fe6502;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;'>
                            <i class='fas fa-eye'></i>
                        </button>
                    </a>";
                }
                $buttons .= "</div>";
                return $buttons;
            })
->addColumn('action', function ($query) {

    $action = "<div style='display:flex; gap:5px; justify-content:center;'>";

    /* CS USERS */
    if (in_array(Auth::user()->role, ['Service Coordinator', 'Cs', 'Super Admin'])) {
        $action .= "<a class='btn btn-primary mb-2 orf-csstatus-btn'
                        href='javascript:void(0)'
                        data-bs-toggle='modal'
                        data-bs-target='#statusModalcs'
                        data-appsta='{$query->approval_status}'
                        data-rid='{$query->id}'>
                        CS Approval
                    </a>";
    }

    /* SALES USERS → ONLY AFTER CS APPROVED */
    if (
        in_array(Auth::user()->role, ['Sales Manager', 'Super Admin']) &&
        $query->cs_status == 1
    ) {

        $action .= "<a class='btn btn-info orf-status-btn mb-2'
                        href='javascript:void(0)'
                        data-bs-toggle='modal'
                        data-bs-target='#statusModal'
                        data-cssta='{$query->cs_status}'
                        data-id='{$query->id}'>
                        Sales Proceed
                    </a>";

        if (Auth::user()->can('orf-approval-edit')) {
            $editUrl = route('orf.edit', $query->id);
            $action .= "<a class='btn btn-warning' href='{$editUrl}'>Edit</a>";
        }
    }

     if (
        Auth::user()->role === 'Sales Manager' &&
        $query->cs_status == 2 &&
        Auth::user()->can('orf-approval-edit')
    ) {
        $editUrl = route('orf.edit', $query->id);
        $action .= "<a class='btn btn-warning' href='{$editUrl}'>Edit</a>";
    }

    $action .= "</div>";
    return $action;
})

            // ->addColumn('action', function ($query) {
            //     $action = "<div style='display: flex; gap: 5px; justify-content: center;'>";
            //     if (Auth::user()->role == 'Service Coordinator' || Auth::user()->role == 'Cs' || Auth::user()->role == 'Super Admin') {

            //         $action .= "<a class='btn btn-primary mb-2 orf-csstatus-btn' href='javascript:void(0)' data-bs-toggle='modal' data-bs-target='#statusModalcs' data-appsta='{$query->approval_status}' data-rid='{$query->id}'>CS Approval</a>";
            //     }

            //     if (Auth::user()->role == 'Sales Manager' || Auth::user()->role == 'Super Admin') {

            //         $editUrl = route('orf.edit', $query->id);

            //         $action .= "<a class='btn btn-info orf-status-btn mb-2' href='javascript:void(0)' data-bs-toggle='modal' data-bs-target='#statusModal' data-cssta='{$query->cs_status}' data-id='{$query->id}' >Sales Proceed</a>";

            //         if (Auth::user()->can('orf-approval-edit')) {
            //             $action .= "<a class='btn btn-warning' href='{$editUrl}'>
            //                 Edit
            //             </a>";
            //         };


            //     }

            //     $action .= "</div>";

            //     return $action;
            // })

            ->filterColumn('LeadName', function ($query, $keyword) {
                $query->where('leads.LeadName', 'like', "%{$keyword}%");
            })
            ->filterColumn('orfref', function ($query, $keyword) {
                $query->where('orf.orfref', 'like', "%{$keyword}%");
            })
            ->filterColumn('cat_name', function ($query, $keyword) {
                $query->where('cate.dropdowndata', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('subcategory_name', function ($query, $keyword) {
                $query->where('product_sub_categories.subcategory_name', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('productname', function ($query, $keyword) {
                $query->where('products.productname', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('assigned_to', function ($query, $keyword) {
                $query->where('users.name', 'LIKE', "%{$keyword}%");
            })
            ->rawColumns(['LeadName', 'action', 'view', 'cs_status', 'approval_status', 'productname'])
            ->addIndexColumn()
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    // public function query(Orf $model): QueryBuilder
    // {
    //     return $model->newQuery()
    //         ->select([
    //             'orf.*',
    //             'leads.LeadName',
    //             'leads.assigned_to',
    //             'cate.category_name as cat_name',
    //             'products.productname',
    //             'product_sub_categories.subcategory_name',
    //             'users.name'
    //         ])
    //         ->where(function ($query) {
    //             $query->where(function ($subQuery) {
    //                 $subQuery->where('orf.approval_status', 0)
    //                     ->where('orf.cs_status', 0);
    //             })
    //                 ->orWhere(function ($subQuery) {
    //                     $subQuery->where('orf.approval_status', 1)
    //                         ->where('orf.cs_status', 0);
    //                 })
    //                 ->orWhere(function ($subQuery) {
    //                     $subQuery->where('orf.approval_status', 0)
    //                         ->where('orf.cs_status', 1);
    //                 })
    //                 ->orWhere(function ($subQuery) {
    //                     $subQuery->where('orf.approval_status', 2)
    //                         ->where('orf.cs_status', 1);
    //                 })
    //                 ->orWhere(function ($subQuery) {
    //                     $subQuery->where('orf.approval_status', 1)
    //                         ->where('orf.cs_status', 2);
    //                 })
    //                 ->orWhere(function ($subQuery) {
    //                     $subQuery->where('orf.approval_status', 0)
    //                         ->where('orf.cs_status', 2);
    //                 })
    //                 ->orWhere(function ($subQuery) {
    //                     $subQuery->where('orf.approval_status', 2)
    //                         ->where('orf.cs_status', 0);
    //                 })
    //                 ->orWhere(function ($subQuery) {
    //                     $subQuery->where('orf.approval_status', 2)
    //                         ->where('orf.cs_status', 2);
    //                 });
    //         })
    //         ->leftJoin('leads', 'orf.leano', '=', 'leads.id')
    //         ->leftJoin('product_categories_tb as cate', 'cate.id', '=', 'leads.category')
    //         ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
    //         ->leftJoin('products', 'products.id', '=', 'leads.products')
    //         ->leftJoin('users', 'users.id', '=', 'leads.assigned_to');

    // }
    public function query(Orf $model): QueryBuilder
{
    $user = Auth::user();

    $query = $model->newQuery()
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
        ->leftJoin('users', 'users.id', '=', 'leads.assigned_to');

    /* -------------------------------------------------
     | ROLE BASED FILTER
     ------------------------------------------------- */

    // 🔹 Sales Manager → only their leads
    if ($user->role === 'Sales Manager') {
        $query->where('leads.assigned_to', $user->id);
    }

    // 🔹 Super Admin → no filter (see all)

    return $query;
}

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('orf')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->serverSide(true) // Enable server-side processing
            ->processing(true) // Show processing indicator
            // ->parameters([
            //     'dom' => 'Bfrtip',
            //     'buttons' => ['excel'],
            //     'scrollX'      => true,
            // ])
            ->parameters([
            'dom'       => 'Bfrtip',
            'buttons'   => ['excel'],
            'scrollX'   => true,
            'autoWidth' => false,

            'columnDefs' => [
                ['targets' => 0, 'width' => '60px',  'className' => 'dt-center'], // S.No
                ['targets' => 1, 'width' => '220px'], // Customer
                ['targets' => 2, 'width' => '180px'], // Segment
                ['targets' => 3, 'width' => '160px'], // ORF No
                ['targets' => 4, 'width' => '170px', 'className' => 'dt-center'], // Sales Status
                ['targets' => 5, 'width' => '170px', 'className' => 'dt-center'], // CS Status
                ['targets' => 6, 'width' => '200px', 'className' => 'dt-center'], // View
                ['targets' => 7, 'width' => '240px', 'className' => 'dt-center'], // Action
            ],

            // 🔥 ABSOLUTELY REQUIRED
            'initComplete' => "function () {
                this.api().columns.adjust();
            }",
            'drawCallback' => "function () {
                this.api().columns.adjust();
            }",
        ])

            ->orderBy(1)
            ->selectStyleSingle();
            // ->fixedColumns(true);
    }

    public function getColumns(): array
{
    return [
        Column::computed('DT_RowIndex')
            ->title('S.No.')
            ->orderable(false)
            ->searchable(false),

        Column::make('LeadName')->title('Customer Name'),
        Column::make('cat_name')->title('Product Segment'),
        Column::make('orfref')->title('ORF No'),
        Column::make('approval_status')->title('ORF Sales Status'),
        Column::make('cs_status')->title('ORF CS Status'),

        Column::computed('view')
            ->title('View')
            ->exportable(false)
            ->printable(false)
            ->addClass('dt-center'),

        Column::computed('action')
            ->title('Action')
            ->exportable(false)
            ->printable(false)
            ->addClass('dt-center'),
    ];
}

    /**
     * Get the dataTable columns definition.
     */
    // public function getColumns(): array
    // {
    //     return [
    //         Column::computed('DT_RowIndex')
    //             ->title('S.No.')->orderable(false),
    //         Column::make('LeadName')->title('Customer Name'),
    //         Column::make('cat_name')->title('Product Segment'),
    //         // Column::make('subcategory_name')->title('Product Group'),
    //         // Column::make('productname')->title('Product Model'),
    //         Column::make('orfref')->title('ORF No'),
    //         // Column::make('assigned_to')->title('Sales Executive'),
    //         Column::make('approval_status')->title('ORF Sales Status'),
    //         Column::make('cs_status')->title('ORF CS Status'),
    //         Column::computed('view')->title('View')->exportable(false)->printable(false)->width(60)->addClass('dt-center'),
    //         Column::computed('action')->title('Action')->exportable(false)->printable(false)->width(60)->addClass('dt-center'),
    //     ];
    // }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Orf_' . date('YmdHis');
    }
}
