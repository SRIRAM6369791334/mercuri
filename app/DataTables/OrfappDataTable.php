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

class OrfappDataTable extends DataTable
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
                return "<div class='dt-wrap'  style='width: 200px'>".$query->productname ?? "-"."</div>";
            })

            ->addColumn('orfref', function ($query) {
                return $query->orfref ?? "-";
            })

            ->addColumn('assigned_to', function ($query) {
                return $query->name ?? "-";
            })
            ->addColumn('approval_status', function ($query) {
                return $query->approval_status == 1
                    ? '<span class="badge badge-success" style="background:#47bb1f;padding:8px;">
                    <i class="fas fa-check-circle"></i> Approved
               </span>'
                    : ($query->approval_status ?? '-');
            })
            ->addColumn('cs_status', function ($query) {
                return $query->cs_status == 1
                    ? '<span class="badge badge-success" style="background:#47bb1f;padding:8px;">
                    <i class="fas fa-check-circle"></i> Approved
               </span>'
                    : ($query->cs_status ?? '-');
            })

            ->addColumn('LeadName', function ($query) {
                return $query->LeadName ?? "-";
            })

            ->addColumn('po_status', function ($query) {
                if ($query->po_status === 'released' || !empty($query->supplier_po_no)) {
                    $poNo = $query->supplier_po_no ?? 'View PO';
                    return '<div class="text-center">
                        <span class="badge" style="background:#28a745;color:#fff;padding:6px 10px;border-radius:4px;font-size:12px;display:inline-block;">
                            <i class="fas fa-check-circle"></i> PO Released
                        </span>
                        <div style="margin-top:4px;">
                            <a href="' . url('/po-invoice/' . $query->supplier_po_no) . '" target="_blank" class="btn btn-sm btn-outline-primary" style="padding:2px 8px;font-size:11px;font-weight:600;text-decoration:none;">
                                <i class="fas fa-eye"></i> View PO (' . e($poNo) . ')
                            </a>
                        </div>
                    </div>';
                } else {
                    return '<div class="text-center">
                        <span class="badge" style="background:#6c757d;color:#fff;padding:5px 8px;border-radius:4px;font-size:11px;font-weight:600;display:inline-block;">
                            <i class="fas fa-hourglass-half"></i> Awaiting PO
                        </span>
                        <div style="margin-top:4px;">
                            <a href="' . url('/create-po?orf_id=' . $query->id) . '" class="btn btn-sm" style="background:#1b5583;color:#fff;padding:3px 10px;font-size:11px;font-weight:600;border-radius:4px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fas fa-file-invoice"></i> Release PO
                            </a>
                        </div>
                    </div>';
                }
            })

            ->addColumn('view', function ($query) {
                $quotUrl = url('/quotation/view/' . $query->quoproid);
                $orfUrl = url('/orfdetail/view/' . $query->quoproid);

                // $cpoUrl = !empty($query->attcuspo) ? url('assets/images/' . ltrim(str_replace('assets/images/', '', $query->attcuspo), '/')) : '#';
                // $sigdrawUrl = !empty($query->sigdraw) ? url('assets/images/' . ltrim(str_replace('assets/images/', '', $query->sigdraw), '/')) : '#';


                $cpoBtn = "
        <button class='btn btn-success btn-sm view-attachments'
            data-orf='{$query->id}'
            data-type='attcuspo'>
            C-PO
        </button>";

    $drawBtn = "
        <button class='btn btn-secondary btn-sm view-attachments'
            data-orf='{$query->id}'
            data-type='sigdraw'>
            C-DRAW
        </button>";

                // $cpoButton = $query->attcuspo ? "<a href='{$cpoUrl}' download>
                //     <button style='color:#fff;background:#28a745;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;'>
                //         C-PO
                //     </button>
                // </a>" : '';

                // $sigdrawButton = $query->sigdraw ? "<a href='{$sigdrawUrl}' download>
                //     <button style='color:#fff;background:#6c757d;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;'>
                //         C-DRAW
                //     </button>
                // </a>" : '';

                return "
            <div style='display: flex; gap: 5px;'>
                <a href='{$quotUrl}' target='_blank'>
                    <button style='color:#fff;background:#1b5583;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;'>
                        Quot
                    </button>
                </a>
                <a href='{$orfUrl}' target='_blank'>
                    <button style='color:#fff;background:#fe6502;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;'>
                        ORF
                    </button>
                </a>
                {$cpoBtn}
                {$drawBtn}
            </div>";
            })


            // ->addColumn('action', function ($query) {
            //     return "
            //     <a class='dropdown-item orf-status-btn' href='/invoice'>
            //         <button class='btn btn-info'>Generate Invoice</button>
            //     </a>";
            // })

            ->filterColumn('LeadName', function ($query, $keyword) {
                $query->where('leads.LeadName', 'like', "%{$keyword}%");
            })
            ->filterColumn('orfref', function ($query, $keyword) {
                $query->where('orf.orfref', 'like', "%{$keyword}%");
            })
            // ->filterColumn('cs_status', function ($query, $keyword) {
            //     $query->where('orf.cs_status', 'like', "%{$keyword}%");
            // })
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
            ->filterColumn('po_status', function ($query, $keyword) {
                if (stripos($keyword, 'release') !== false) {
                    $query->where('orf.po_status', 'released');
                } elseif (stripos($keyword, 'pend') !== false) {
                    $query->where(function ($q) {
                        $q->whereNull('orf.po_status')->orWhere('orf.po_status', '!=', 'released');
                    });
                } else {
                    $query->where('orf.supplier_po_no', 'like', "%{$keyword}%");
                }
            })
            ->rawColumns(['LeadName', 'view', 'cs_status', 'approval_status', 'productname', 'po_status'])
            ->addIndexColumn()
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Orf $model): QueryBuilder
    {
        // return $model->newQuery()
        //     ->select([
        //         'orf.*',
        //         'leads.LeadName',
        //         'leads.assigned_to',
        //         'cate.category_name as cat_name',
        //         'users.name',
        //         'products.productname',
        //         'product_sub_categories.subcategory_name',
        //     ])
        //     ->where('orf.approval_status', '=', 1)
        //     ->where('orf.cs_status', '=', 1)
        //     ->leftJoin('leads', 'orf.leano', '=', 'leads.id')
        //     ->leftJoin('product_categories_tb as cate', 'cate.id', '=', 'leads.category')
        //     ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
        //     ->leftJoin('products', 'products.id', '=', 'leads.products')
        //     ->leftJoin('users', 'users.id', '=', 'leads.assigned_to');

    $user  = Auth::user();
    $role  = $user->role;

    $query = $model->newQuery()
        ->select([
            'orf.*',
            'leads.LeadName',
            'leads.assigned_to',
            'cate.category_name as cat_name',
            'users.name',
            'products.productname',
            'product_sub_categories.subcategory_name',
        ])
        ->leftJoin('leads', 'orf.leano', '=', 'leads.id')
        ->leftJoin('product_categories_tb as cate', 'cate.id', '=', 'leads.category')
        ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
        ->leftJoin('products', 'products.id', '=', 'leads.products')
        ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
        ->where('orf.approval_status', 1)
        ->where('orf.cs_status', 1);

    /* -------------------------------------------------
     | ROLE BASED FILTER
     ------------------------------------------------- */

    // 🔹 Design Engineer → ONLY Special products
    // if ($role === 'Design Engineer') {
    //     $query->where('cate.sub_category', 'special');
    // }
    if ($role === 'Super Admin') {
        // no condition
    }

    // 🔹 Sales Manager → ONLY their own leads
    elseif ($role === 'Sales Manager') {
        $query->where('leads.assigned_to', $user->id);
    }

    // 🔹 Design Engineer → ONLY special products
    elseif ($role === 'Design Engineer') {
        $query->where('cate.sub_category', 'special');
    }

    // 🔹 Other roles → NO DATA
    else {
        $query->whereRaw('1 = 0');
    }

    // Optional PO Status Filter
    if (request()->filled('po_filter')) {
        $poFilter = request()->get('po_filter');
        if ($poFilter === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('orf.po_status')->orWhere('orf.po_status', 'pending');
            });
        } elseif ($poFilter === 'released') {
            $query->where('orf.po_status', 'released');
        }
    }

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
            ->parameters([
                'dom' => 'Bfrtip',
                // 'buttons' => ['excel'],
                // 'responsive'   => true,
                   'buttons' => [
        [
            'extend' => 'excel',
            'exportOptions' => [
                'columns' => ':not(.no-export)' // 👈 exclude marked columns
            ]
        ]
    ],
                'autoWidth'    => false,
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
            Column::make('LeadName')->title('Customer Name'),
            Column::make('cat_name')->title('Product Segment'),
            // Column::make('subcategory_name')->title('Product Group'),
            // Column::make('productname')->title('Product Model'),
            Column::make('orfref')->title('ORF No'),
            // Column::make('assigned_to')->title('Sales Executive'),
            Column::make('approval_status')->title('ORF Sales Status'),
            Column::make('cs_status')->title('ORF CS Status'),
            Column::computed('po_status')->title('Supplier PO Status')->exportable(true)->printable(false)->addClass('text-center'),
            Column::computed('view')->title('View')->exportable(false)->printable(false)->width(60)->addClass('text-center no-export'),
            // Column::computed('action')->title('Action')->exportable(false)->printable(false)->width(60)->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Orf_' . date('YmdHis');
    }
}
