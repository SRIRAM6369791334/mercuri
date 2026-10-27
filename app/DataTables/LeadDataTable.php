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

class LeadDataTable extends DataTable
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
                } elseif ($query->status_name == "COLD") {
                    $bgColor = '#0dcaf0';
                }

                $style = 'background:' . $bgColor . ';padding: 4px 0px;border-radius: 3px;color:' . $textColor . ';';

                $statusName = $query->status_name ?? '-';
                $status_id = $query->status_id ?? '-';
                $lead_id = $query->id ?? '-';
                return "<div class='lead_status_div cursor_pointer' data-lleadid='{$lead_id}' data-lleadstatusid='{$status_id}' data-bs-toggle='modal' data-bs-target='#astatusModal' style='{$style}'>{$statusName}</div>";
            })
            ->addColumn('LeadName', function ($query) {
                return $query->LeadName ?? "-";
            })
//             ->addColumn('create_quotation', function ($query) {
//     $lead_id = $query->id;

//     $url = url('createquota/' . $lead_id);

//     return "<a href='{$url}' class='btn btn-success btn-sm'>
//                 Create Quotation
//             </a>";
// })
            ->addColumn('move_to', function ($query) {
                $missing = [];
                if (empty(trim((string) ($query->LeadName ?? '')))) {
                    $missing[] = 'Customer / Company Name';
                }
                if (empty(trim((string) ($query->contactname ?? '')))) {
                    $missing[] = 'Contact Person Name';
                }
                if (empty(trim((string) ($query->phonumber ?? '')))) {
                    $missing[] = 'Contact Number';
                }
                if (empty(trim((string) ($query->email ?? '')))) {
                    $missing[] = 'Email Address';
                }
                if (empty(trim((string) ($query->billaddress ?? '')))) {
                    $missing[] = 'Billing Address';
                }
                if (empty($query->state_id)) {
                    $missing[] = 'Billing State';
                }
                if (empty($query->citylist)) {
                    $missing[] = 'Billing City';
                }
                if (empty(trim((string) ($query->postalcode ?? '')))) {
                    $missing[] = 'Billing Postal Code';
                }

                $isComplete = empty($missing) ? 1 : 0;
                $missingStr = e(implode(', ', $missing));
                $lead_id = $query->id ?? '-';
                $editUrl = url('/lead-edit/' . $lead_id);
                $customerName = e((string) ($query->LeadName ?? 'N/A'));
                $enqRef = e((string) ($query->lead_id ?? ('LEAD' . str_pad($lead_id, 4, '0', STR_PAD_LEFT))));

                return "<button type='button' data-leadid='{$lead_id}' data-complete='{$isComplete}' data-missing='{$missingStr}' data-edit-url='{$editUrl}' data-customer='{$customerName}' data-ref='{$enqRef}' style='width: 130px' class='btn btn-primary lead_won'>Move to Enquiry</button>";
            })
            // ->addColumn('LeadName', function ($query) {
            //     $url = url('/singlelead/' . $query->id);
            //     return '<a href="' . $url . '">' . e($query->LeadName) . '</a>';
            // })
            ->addColumn('action', function ($query) {
                $editUrl = url('/lead-edit/' . $query->id);
                $deleteUrl = route('leads.softDelete', ['id' => $query->id]);
                if (auth()->user()->canAny(['leads-edit', 'leads-delete'])) {
                    $action = "";

                    if (Auth::user()->can('leads-edit')) {
                        $action .= "<a class='btn-warning btn mb-2' href='{$editUrl}'>Edit</a> ";
                    }

                    if (Auth::user()->can('leads-delete')) {
                        $action .= "<a href='javascript:void(0)' class='btn-danger btn mb-2 delete-btn'
                                data-id='{$query->id}'
                                data-url='{$deleteUrl}'>Delete</a>";
                    }

                    $action .= "</div>";

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
            ->filterColumn('lead_status', function ($query, $keyword) {
                $query->where('status.dropdowndata', 'like', "%{$keyword}%");
            })
            ->rawColumns(['create_quotation' ,'LeadName', 'action', 'lead_status', 'move_to'])
            ->addIndexColumn()
            ->setRowId('id');
    }
    public function query(Lead $model): QueryBuilder
    {
        $query = $model->newQuery()
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
                $query->whereNotIn('leads.Leadstatus', [343, 351])
                    ->orWhereNull('leads.Leadstatus');
            });

        // Apply role-based filter
        if (Auth::user()->role !== 'Super Admin') {
            $query->where('leads.assigned_to', Auth::id());
        }

        return $query->orderBy('leads.created_at', 'desc');
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
                'buttons' => ['excel', 'csv'],
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
        $columns = [
            Column::computed('DT_RowIndex')
                ->title('S.No.')->orderable(false),
            Column::make('Entrydate')->title('Entry Date'),
            Column::make('LeadName')->title('Customer Name'),
            Column::make('citylist')->title('City'),
            Column::make('contactname')->title('Contact Name'),
            Column::make('phonumber')->title('Contact No'),
            Column::make('email')->title('Email'),
        ];

        // 👉 Show "Sales Manager" only if role is Super Admin
        if (Auth::user()->role === 'Super Admin') {
            $columns[] = Column::make('assigned_to')->title('Sales Manager');
        }

        $columns = array_merge($columns, [
            Column::make('lead_id')->title('Enq Ref No'),
            Column::make('lead_status')->title('Lead Status'),
            // Column::make('create_quotation')->title('Create Quotation'),
            Column::make('move_to')->title('Move to'),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->width(60)->addClass('text-center'),
        ]);

        return $columns;
    }


    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Lead_' . date('YmdHis');
    }
}
