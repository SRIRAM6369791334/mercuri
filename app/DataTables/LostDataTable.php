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

class LostDataTable extends DataTable
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
            ->addColumn('assigned_to', function ($query) {
                return $query->name ?? "-";
            })
            ->addColumn('lead_id', function ($query) {
                return $query->lead_id ?? "-";
            })
            ->addColumn('remarks', function ($query) {
                return $query->remarks ?? "-";
            })
            ->addColumn('lead_status', function ($query) {
                $bgColor = '#052c65';
                $textColor = '#000000';


                if ($query->status_name == "HOT") {
                    $bgColor = '#20c997';
                    $textColor = '#ffffff';
                } elseif ($query->status_name == "WARM") {
                    $bgColor = '#ffc107';
                } elseif ($query->status_name == "LOST") {
                    $bgColor = '#dc3545';
                    $textColor = '#ffffff';
                } elseif ($query->status_name == "HOLD") {
                    $bgColor = '#052c65';
                    $textColor = '#ffffff';
                }elseif ($query->status_name == "COLD"){
                    $bgColor = '#0dcaf0';
                }

                $style = 'background:'.$bgColor.';padding: 4px 0px;border-radius: 3px;color:'. $textColor. ';';

                $statusName = $query->status_name ?? '-';
                $status_id = $query->status_id ?? '-';
                $lead_id = $query->id ?? '-';
                return "<div class='lead_status_div cursor_pointer' data-leadid='{$lead_id}' data-leadstatusid='{$status_id}' data-bs-toggle='modal' data-bs-target='#statusModal' style='{$style}'>{$statusName}</div>";
            })
            ->addColumn('LeadName', function ($query) {
                return $query->LeadName ?? "-";
            })
            ->addColumn('move_to', function ($query) {
                $lead_id = $query->id ?? '-';
                return "<div data-bs-toggle='modal' data-leadid='{$lead_id}' data-bs-target='#statusModal' style='width: 120px' class='btn btn-primary lead_won'>Click to Win</div>";
            })
            // ->addColumn('LeadName', function ($query) {
            //     $url = url('/singlelead/' . $query->id);
            //     return '<a href="' . $url . '">' . e($query->LeadName) . '</a>';
            // })
            ->addColumn('action', function ($query) {
                $editUrl = url('/lead-edit/' . $query->id);
                $deleteUrl = route('leads.softDelete', ['id' => $query->id]);
                if (auth()->user()->canAny(['leads-edit', 'leads-delete'])) {
                    $action = "
                <div class='dropdown'>
                    <button style='color:#fff;background:#000;' class='btn px-18 py-11 text-primary-light' type='button' data-bs-toggle='dropdown'>
                        <i class='mdi mdi-chevron-down'></i>
                    </button>
                    <ul class='dropdown-menu'>";
                    if ($query->proceed == 1) {
                        $action .= "
                            <li><a class='dropdown-item change-status-btn' href='javascript:void(0)' data-bs-toggle='modal' data-bs-target='#statusModal' data-ids='{$query->id}'>Change Status</a></li>
                            <li><a class='dropdown-item lead-status-btn' href='javascript:void(0)' data-bs-toggle='modal' data-bs-target='#leadStatusModal' data-ids='{$query->id}' data-rema='{$query->remarks}' data-stup='{$query->status_updatedate}' data-lests='{$query->status_name}'>Lead Status</a></li>";
                    }

                    if (Auth::user()->can('leads-edit')) {
                        $action .= "<li><a class='dropdown-item' href='{$editUrl}'>Edit</a></li>";
                    }

                    if (Auth::user()->can('leads-delete')) {
                        $action .= "<li>
                                <a href='javascript:void(0)' class='dropdown-item delete-btn'
                                data-id='{$query->id}'
                                data-url='{$deleteUrl}'>Delete</a>
                            </li>";
                    }

                    $action .= "</ul></div>";

                    return $action;
                }
            })
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
            ->filterColumn('assigned_to', function ($query, $keyword) {
                $query->where('users.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('lead_id', function ($query, $keyword) {
                $query->where('leads.lead_id', 'like', "%{$keyword}%");
            })
            ->filterColumn('remarks', function ($query, $keyword) {
                $query->where('leads.remarks', 'like', "%{$keyword}%");
            })
            ->filterColumn('lead_status', function ($query, $keyword) {
                $query->where('status.dropdowndata', 'like', "%{$keyword}%");
            })
            ->rawColumns(['LeadName', 'action', 'lead_status','move_to'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    // public function query(Lead $model): QueryBuilder
    // {
    //     return $model->newQuery()
    //         ->select([
    //             'leads.*',
    //             'source.dropdowndata as source_name',
    //             'status.dropdowndata as status_name',
    //             'statusid.id as status_id',
    //             'city_list.city_name',
    //             'users.name'
    //         ])
    //         ->leftJoin('dropdowndatas as source', 'source.id', '=', 'leads.Leadsource')
    //         ->leftJoin('dropdowndatas as status', 'status.id', '=', 'leads.Leadstatus')
    //         ->leftJoin('dropdowndatas as statusid', 'statusid.id', '=', 'leads.Leadstatus')
    //         ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
    //         ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
    //         ->where(function ($query) {
    //             $query->where('leads.Leadstatus', '=', 351)
    //                 ->orWhereNull('leads.Leadstatus');
    //         })
    //         ->orderBy('leads.created_at', 'desc');
    // }

    public function query(Lead $model): QueryBuilder
{
    return $model->newQuery()
        ->select([
            'leads.*',
            'source.dropdowndata as source_name',
            'status.dropdowndata as status_name',
            'statusid.id as status_id',
            'city_list.city_name',
            'users.name'
        ])
        ->leftJoin('dropdowndatas as source', 'source.id', '=', 'leads.Leadsource')
        ->leftJoin('dropdowndatas as status', 'status.id', '=', 'leads.Leadstatus')
        ->leftJoin('dropdowndatas as statusid', 'statusid.id', '=', 'leads.Leadstatus')
        ->leftJoin('city_list', 'city_list.id', '=', 'leads.citylist')
        ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')

        ->where(function ($query) {

            // ✅ Case 1: Leadstatus = 351 (always show)
            $query->where('leads.Leadstatus', 351)

                  // ✅ Case 2: Leadstatus != 351 AND quto_status IN (3,4,5)
                  ->orWhere(function ($q) {
                      $q->where('leads.Leadstatus', '!=', 351)
                        ->whereIn('leads.quto_status', [3, 4, 5]);
                  });
        })

        ->orderBy('leads.created_at', 'desc');
}



    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('leads')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->serverSide(true) // Enable server-side processing
            ->processing(true) // Show processing indicator
           ->parameters([
            'dom' => 'Bfrtip',
            'buttons' => ['excel'],
            'scrollX' => true,
            'autoWidth' => false,

            // ⭐ CRITICAL ALIGNMENT FIX
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
        Column::make('Entrydate')->title('Entry Date')->width(100),
        Column::make('LeadName')->title('Customer Name')->width(220),
        Column::make('citylist')->title('City')->width(120),
        Column::make('contactname')->title('Contact Name')->width(160),
        Column::make('phonumber')->title('Contact No')->width(140),
        Column::make('email')->title('Email')->width(220),
        Column::make('assigned_to')->title('Sales Manager')->width(180),
        Column::make('lead_id')->title('Enq Ref No')->width(140),
        Column::make('remarks')->title('Remarks')->width(220),
        // Column::make('lead_status')->title('Lead Status')->width(140),
        // Column::make('move_to')->title('Move To')->width(140),
    ];
}

    /**
     * Get the dataTable columns definition.
     */
    // public function getColumns(): array
    // {
    //     return [
    //         Column::make('Entrydate')->title('Entry Date'),
    //         Column::make('LeadName')->title('Customer Name'),
    //         Column::make('citylist')->title('City'),
    //         Column::make('contactname')->title('Contact Name'),
    //         Column::make('phonumber')->title('Contact No'),
    //         Column::make('email')->title('Email'),
    //         Column::make('assigned_to')->title('Sales Manager'),
    //         Column::make('lead_id')->title('Enq Ref No'),
    //         Column::make('lead_status')->title('Lead Status'),
    //         // Column::make('move_to')->title('Move to'),
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
        return 'Lost_' . date('YmdHis');
    }
}
