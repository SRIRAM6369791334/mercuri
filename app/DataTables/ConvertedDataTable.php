<?php

namespace App\DataTables;

use App\Models\AdditionalPOOrder;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;


class ConvertedDataTable extends DataTable
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
            ->addColumn('Entrydate', function ($query) {
                return $query->Entrydate ? \Carbon\Carbon::parse($query->Entrydate)->format('d-m-Y') : "-";
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
            ->addColumn('email', function ($query) {
                return $query->email ?? "-";
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
            // ->addColumn('subcategory_name', function ($query) {
            //     return $query->subcategory_name ?? "-";
            // })
            // ->addColumn('productname', function ($query) {
            //     $productname = $query->productname ?? "-";
            //     $rate = $query->rate ?? "-";
            //     return "<div data-bs-toggle='tooltip' data-bs-placement='top' title='{$rate}' class='dt-wrap' style='width: 250px'>{$productname}</div>";
            // })
            // ->addColumn('category_name', function ($query) {
            //     return $query->category_name ?? "-";
            // })
            // ->addColumn('productname', function ($query) {
            //     $productname = $query->productname ?? "-";
            //     $rate = $query->rate ?? "-";
            //     return "<div data-bs-toggle='tooltip' data-bs-placement='top' title='{$rate}' style='width: 250px'>{$productname}</div>";
            // })
            ->addColumn('assigned_to', function ($query) {
                return $query->name ?? "-";
            })
            ->addColumn('lead_id', function ($query) {
                return $query->lead_id ?? "-";
            })
            // ->addColumn('LeadName', function ($query) {
            //     $url = url('/singlequota/' . $query->id);
            //     return '<a href="' . $url . '">' . e($query->LeadName) . '</a>';
            // })
            ->addColumn('LeadName', function ($query) {
                return $query->LeadName ?? "-";
            })
// ->addColumn('design_status', function ($row) {

//     // Pending / Completed button
//     if (empty($row->design_status)) {

//         // If designer assigned → clickable Pending
//         if (!empty($row->design_user)) {
//             $statusBtn = '<button
//                             class="btn btn-sm btn-warning upload-design-btn"
//                             data-design-id="'.$row->design_id.'">
//                             Pending
//                           </button>';
//         }
//         // If no designer → disabled Pending
//         else {
//             $statusBtn = '<button class="btn btn-sm btn-secondary" disabled>
//                             Pending
//                           </button>';
//         }

//     } else {
//         $statusBtn = '<span class="badge bg-success">Completed</span>';
//     }

//     // Eye button (always visible)
//     $eyeBtn = '<button
//                     class="btn btn-sm btn-outline-secondary ms-1 view-design-specs"
//                     data-design-id="'.$row->design_id.'"
//                     title="View Specifications">
//                     <i class="mdi mdi-eye"></i>
//                </button>';

//     return $statusBtn . ' ' . $eyeBtn;
// })
->addColumn('design_status', function ($row) {

    // COMPLETED → Move to quotation
    if ($row->design_status == 1) {

        return '
            <button
                class="btn btn-sm btn-success move-quotation-btn"
                data-design-id="'.$row->design_id.'">
                Move to Quotation
            </button>

            <button
                class="btn btn-sm btn-outline-secondary ms-1 view-design-specs"
                data-design-id="'.$row->design_id.'"
                title="View Specifications">
                 View Input
            </button>
        ';
    }
// <i class="mdi mdi-eye"></i>
    // PENDING
    if (!empty($row->design_user)) {
        // $statusBtn = '
        //     <button
        //         class="btn btn-sm btn-warning upload-design-btn"
        //         data-design-id="'.$row->design_id.'">
        //         Move to Sales
        //     </button>';
         if ($row->drawing_approved === 'approved') {
        $statusBtn = '
            <button
                class="btn btn-sm btn-success upload-design-btn"
                data-design-id="'.$row->design_id.'">
                Move to Sales
            </button>';
    }

    // ⏳ DRAWING PENDING → UPLOAD DRAWING
    elseif ($row->drawing_approved === 'pending') {
        if ($row->drawing_count > 0) {
        $statusBtn = '
            <button class="btn btn-sm btn-secondary drawing-approval-btn"
        data-design-id="'.$row->design_id.'">
    Drawing Approval Pending
</button>';
    }
    // no drawings yet → allow upload
    else {
        $statusBtn = '
            <button
                class="btn btn-sm btn-warning upload-drawing-btn"
                data-design-id="'.$row->design_id.'">
                Upload Drawing
            </button>';
    }
        // $statusBtn = '
        //     <button
        //         class="btn btn-sm btn-warning upload-drawing-btn"
        //         data-design-id="'.$row->design_id.'">
        //         Upload Drawing
        //     </button>';
    }

    // ❌ DRAWING REJECTED
    elseif ($row->drawing_approved === 'rejected') {
        $statusBtn = '
            <button class="btn btn-sm btn-danger drawing-rejected-btn"
            data-design-id="'.$row->design_id.'">
        Drawing Rejected
    </button>';
    }else {
        $statusBtn = '
            <button
                class="btn btn-sm btn-warning upload-design-btn"
                data-design-id="'.$row->design_id.'">
                Move to Sales
            </button>';
    }
    } else {
        $statusBtn = '
            <button class="btn btn-sm btn-secondary" disabled>
                Pending
            </button>';
    }

    $eyeBtn = '
        <button
            class="btn btn-sm btn-outline-secondary ms-1 view-design-specs"
            data-design-id="'.$row->design_id.'">
           View Input
        </button>';

    return $statusBtn . $eyeBtn;
})
// <i class="mdi mdi-eye"></i>
// ->addColumn('design_status', function ($row) {

//     $statusBtn = empty($row->design_status)
//         ? '<button class="btn btn-sm btn-warning">Pending</button>'
//         : '<span class="badge bg-success">Completed</span>';

//     $eyeBtn = '<button
//                     class="btn btn-sm btn-outline-secondary ms-1 view-design-specs"
//                     data-design-id="'.$row->design_id.'"
//                     title="View Specifications">
//                     <i class="mdi mdi-eye"></i>
//                </button>';

//     return $statusBtn . $eyeBtn;
// })


// ->addColumn('design_user', function ($row) {

//     if (empty($row->design_user)) {
//         return '<button
//                     class="btn btn-sm btn-primary assign-designer-btn"
//                     data-design-id="'.$row->id.'">
//                     Assign Designer
//                 </button>';
//     }

//     return '<span class="badge bg-info">'.$row->designer_name.'</span>';
// })
->addColumn('design_user', function ($row) {

    // DESIGN ENGINEER → Assign + Return
    if (
        empty($row->design_user) &&
        $row->return_status == 0 &&
        Auth::id() == $row->assigndesign
    ) {
        return '
            <button class="btn btn-sm btn-primary assign-designer-btn me-1"
                data-design-id="'.$row->design_id.'">
                Assign Designer
            </button>

            <button class="btn btn-sm btn-danger return-design-btn"
                data-design-id="'.$row->design_id.'">
                Reject
            </button>
        ';
    }

     if (
        !empty($row->design_user) &&
        $row->return_status == 0 &&
        Auth::id() == $row->design_user
    ) {
        return '
            <span class="badge bg-info me-2">'.$row->designer_name.'</span>
            <button class="btn btn-sm btn-danger return-design-btn"
                data-design-id="'.$row->design_id.'">
                Reject
            </button>
        ';
    }

    // ✅ EDIT → Returned → Lead Owner (NOT role-based)
    if (
        $row->return_status == 1 &&
        Auth::id() == $row->assigned_to
    ) {
        return '
            <button class="btn btn-sm btn-warning edit-design-btn"
                data-design-id="'.$row->design_id.'">
                Rejected
            </button>
        ';
    }

    // Designer badge
    if (!empty($row->design_user)) {
        return '<span class="badge bg-info">'.$row->designer_name.'</span>';
    }

    return '-';
})


// ->addColumn('design_user', function ($row) {

//     // Only when no designer AND not returned
//     if (empty($row->design_user) && $row->return_status == 0) {

//         return '
//             <button
//                 class="btn btn-sm btn-primary assign-designer-btn me-1"
//                 data-design-id="'.$row->design_id.'">
//                 Assign Designer
//             </button>

//             <button
//                 class="btn btn-sm btn-danger return-design-btn"
//                 data-design-id="'.$row->design_id.'">
//                 Return
//             </button>
//         ';
//     }

//     // If designer assigned
//     if (!empty($row->design_user)) {
//         return '<span class="badge bg-info">'.$row->designer_name.'</span>';
//     }

//     return '-';
// })


            // ->addColumn('action', function ($query) {
            //     return "
            //         <div class='dropdown'>
            //             <button style='color:#fff;background:#000;' class='btn px-18 py-11 text-primary-light' type='button' data-bs-toggle='dropdown'>
            //                 <i class='mdi mdi-chevron-down'></i>
            //             </button>
            //             <ul class='dropdown-menu'>
            //                 <li><a class='dropdown-item change-status-btn' href='javascript:void(0)'  data-bs-toggle='modal' data-bs-target='#statusModal' data-id='{$query->id}'>Change Status</a></li>
            //                 <li><a class='dropdown-item' target='_blank' href='javascript:void(0)'>View</a></li>
            //                 <li><a class='dropdown-item' href='javascript:void(0)'>Delete</a></li>
            //             </ul>
            //         </div>";
            // })
            ->filterColumn('Entrydate', function ($query, $keyword) {
                $query->where('leads.Entrydate', 'like', "%{$keyword}%");
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
            ->filterColumn('email', function ($query, $keyword) {
                $query->where('leads.email', 'like', "%{$keyword}%");
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
            ->filterColumn('assigned_to', function ($query, $keyword) {
                $query->where('users.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('lead_id', function ($query, $keyword) {
                $query->where('leads.lead_id', 'like', "%{$keyword}%");
            })
            ->rawColumns(['LeadName', 'productname','action', 'design_status', 'design_user'])
            ->setRowId('id');


    }

    /**
     * Get the query source of dataTable.
     */
    // public function query(Lead $model): QueryBuilder
    // {
    //     $userId = Auth::user()->id;
    //     $query = $model->newQuery()
    //         ->select([
    //             'leads.*',
    //             'source.dropdowndata as source_name',
    //             'status.dropdowndata as status_name',
    //             'city_list.city_name',
    //             'users.name',
    //             'designs.assigndesign',
    //             'designs.design_status',
    //             'designer.name as designer_name',
    //             'designs.design_user',
    //              'product_categories_tb.category_name',
    //             'product_categories_tb.sub_category',
    //             'product_sub_categories.subcategory_name',
    //             'products.productname',
    //             'products.rate',
    //         ])
    //         ->leftJoin('dropdowndatas as source', 'source.id', '=', 'leads.Leadsource')
    //         ->leftJoin('dropdowndatas as status', 'status.id', '=', 'leads.Leadstatus')
    //         ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
    //         ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
    //         ->leftJoin('designs', 'designs.idlead', '=', 'leads.id')
    //         // ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
    //         ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
    //         ->leftJoin('products', 'products.id', '=', 'leads.products')
    //         ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
    //         ->leftJoin('users as designer', 'designer.id', '=', 'designs.design_user')
    //         ->where('product_categories_tb.sub_category', 'special')
    //         ->where('leads.Leadstatus', '=', 343)
    //         ->where(function ($query) {
    //             $query->whereNotIn('leads.quota_proceed', [1, 0])
    //                 ->orWhereNull('leads.quota_proceed');
    //         })
    //         ->distinct();

    //     if (Auth::user()->role == "Design Engineer") {
    //         $query->where('designs.assigndesign', '=', $userId);
    //     }

    //     $query->orderBy('leads.updated_at', 'desc');

    //     return $query;
    // }

    public function query(Lead $model): QueryBuilder
{
    $userId = Auth::id();
    $role   = Auth::user()->role;

    $query = $model->newQuery()
        ->select([
            'leads.*',
            'source.dropdowndata as source_name',
            'status.dropdowndata as status_name',
            'city_list.city_name',

            'users.name as sales_manager_name',

            'designs.assigndesign',
            'designs.design_status',
            'designs.design_user',
            'designs.id as design_id',
            'designs.return_status',
            'designs.drawing_approved',
             DB::raw('
        (SELECT COUNT(*)
         FROM design_drawings
         WHERE design_drawings.design_id = designs.id
        ) as drawing_count
    '),

            'designer.name as designer_name',

            'product_categories_tb.category_name',
            'product_categories_tb.sub_category',
            'product_sub_categories.subcategory_name',
            'products.productname',
            'products.rate',
        ])
        ->leftJoin('dropdowndatas as source', 'source.id', '=', 'leads.Leadsource')
        ->leftJoin('dropdowndatas as status', 'status.id', '=', 'leads.Leadstatus')
        ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
        ->leftJoin('users', 'users.id', '=', 'leads.assigned_to') // Sales
        ->leftJoin('designs', 'designs.idlead', '=', 'leads.id')
        ->leftJoin('users as designer', 'designer.id', '=', 'designs.design_user')
        ->leftJoin('product_categories_tb', 'product_categories_tb.id', '=', 'leads.category')
        ->leftJoin('products', 'products.id', '=', 'leads.products')
        ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'leads.subcategory_id')
        ->where('product_categories_tb.sub_category', 'special')
        ->where('leads.Leadstatus', 343)

        ->where(function ($q) {
            $q->whereNotIn('leads.quota_proceed', [1, 0])
              ->orWhereNull('leads.quota_proceed');
        });

    /* ---------------- ROLE BASED FILTERING ---------------- */

    // 1️⃣ Super Admin → NO FILTER
    if ($role === 'Super Admin') {
        // show all
    }

    // 2️⃣ Design Engineer (TL view – unassigned)
    elseif ($role === 'Design Engineer') {

        $query->where('designs.return_status', 0)
        ->where(function ($q) use ($userId) {

            // TL pending designs
            $q->where(function ($sub) use ($userId) {
                $sub->where('designs.assigndesign', $userId)
                    ->whereNull('designs.design_status')
                    ->whereNull('designs.design_user');
            })

            // OR designs assigned to me
            ->orWhere(function ($sub) use ($userId) {
                $sub->where('designs.design_user', $userId)
                    ->whereNull('designs.design_status');
            });
        });
    }


    // 3️⃣ Sales user
    elseif ($role === 'Sales Manager') {

        $query
        // ->where('designs.return_status', 1)
        // ->where('designs.design_status', 1)
        //       ->whereNotNull('designs.assigndesign')
              ->where('leads.assigned_to', $userId);
    }

    // 4️⃣ Other roles → NO DATA
    else {
        $query->whereRaw('1 = 0');
    }

    return $query->orderBy('leads.updated_at', 'desc')->distinct();
}


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('leadsd')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->serverSide(true) // Enable server-side processing
            ->processing(true) // Show processing indicator
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => ['excel'],
                'scrollX'      => true,
                // 'buttons' => ['excel', 'print', 'pdf'],
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
        Column::make('Entrydate')->title('Entry Date')->width(90),
        Column::make('LeadName')->title('Customer Name')->width(220),
        Column::make('citylist')->title('City')->width(120),
        Column::make('contactname')->title('Contact Name')->width(140),
        Column::make('phonumber')->title('Contact No')->width(130),
        Column::make('category_name')->title('Segment')->width(150),
        Column::make('assigned_to')->title('Sales Manager')->width(160),
        Column::make('lead_id')->title('Enq Ref No')->width(120),
        Column::make('design_status')
            ->title('Design Status')
            ->orderable(false)
            ->searchable(false)
            ->width(120),

        Column::make('design_user')
            ->title('Assigned Designer')
            ->orderable(false)
            ->searchable(false)
            ->width(180),
    ];
}

    // public function getColumns(): array
    // {
    //     return [
    //         Column::make('Entrydate')->title('Entry Date'),
    //         Column::make('LeadName')->title('Customer Name'),
    //         Column::make('citylist')->title('City'),
    //         // Column::make('email')->title('Email'),
    //         Column::make('contactname')->title('Contact Name'),
    //         Column::make('phonumber')->title('Contact No'),
    //          Column::make('category_name')->title('Segment'),
    //         // Column::make('subcategory_name')->title('Group'),
    //         // Column::make('productname')->title('Model'),
    //         Column::make('assigned_to')->title('Sales Manager'),
    //         Column::make('lead_id')->title('Enq Ref No'),
    //         // Column::make('Leadsource')->title('Enquiry Source'),
    //         // Column::make('Leadstatus')->title('Enquiry Status'),
    //         // Column::computed('action')->title('Action')->exportable(false)->printable(false)->width(60)->addClass('text-center'),
    //     ];
    // }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Converted_' . date('YmdHis');
    }
}
