@extends('layouts.app')
@section('title', 'Terms and Conditions Details')
@section('main-content')
    <style>
        #terms-table {
            width: 100% !important;
        }

        #terms-table th {
            font-weight: 600;
            font-size: 13px;
            background-color: #f8f9fa;
            vertical-align: middle;
        }

        #terms-table td {
            vertical-align: middle;
            font-size: 13px;
        }

        .terms-content-cell {
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .terms-content-preview {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            line-height: 1.4;
        }

        .vendor-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            flex-wrap: nowrap;
        }
    </style>

    @can('terms-and-condition-display')
        <section class="section leadsection container">
            <div class=" form-card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="mt-2">Terms Details</h5>
                            </div>
                            <div class="col-lg-4">

                            </div>
                            <div class="col-lg-2">
                                @can('terms-and-condition-create')
                                    <a href="/addterms" class="btn btn-primary d-block add-employee-btn">Add Terms Content</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container-fluid mt-4 mb-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="terms-table" class="table table-bordered table-striped table-hover align-middle w-100 mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 6%; text-align: center;">S.No</th>
                            <th style="width: 14%; text-align: center;">Invoice</th>
                            <th style="width: 14%; text-align: center;">Terms Type</th>
                            <th style="width: 20%; text-align: center;">Segment / Group</th>
                            <th style="width: 34%; text-align: start;">Content</th>
                            @canany(['terms-and-condition-edit', 'terms-and-condition-delete'])
                                <th style="width: 12%; text-align: center;">Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($terms as $index => $term)
                            <tr>
                                <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                
                                <td class="text-center">
                                    @if ($term->whichterm == 1)
                                        Sales Quotation
                                    @elseif ($term->whichterm == 2)
                                        PO Invoice
                                    @else
                                        Unknown
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($term->termtype == 1)
                                        <span class="fw-semibold text-dark">Payment Terms</span>
                                    @elseif ($term->termtype == 2)
                                        <span class="fw-semibold text-dark">Warranty Terms</span>
                                    @else
                                        Unknown
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($term->category)
                                        <span class="badge bg-secondary mb-1" style="font-size: 11px;">{{ $term->category->category_name }}</span>
                                    @else
                                        <span class="badge bg-light text-dark border mb-1" style="font-size: 11px;">General</span>
                                    @endif
                                    @if ($term->termtype == 2)
                                        @php
                                            $groupName = $term->productSubCategory?->subcategory_name ?: (\App\Helpers\QuotationPresentationHelper::productGroupName($term->product));
                                        @endphp
                                        @if(!empty($groupName) && $groupName !== 'Product')
                                            <div class="small text-primary fw-semibold mt-1" style="font-size: 12px; line-height: 1.2;">{{ $groupName }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="terms-content-cell text-start">
                                    @php
                                        $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($term->content))));
                                        $previewContent = $plainContent !== ''
                                            ? \Illuminate\Support\Str::limit($plainContent, 160)
                                            : (str_contains($term->content, '<img') ? 'Image content added' : '-');
                                    @endphp
                                    <div class="terms-content-preview" title="{{ $plainContent !== '' ? $plainContent : $previewContent }}">
                                        {{ $previewContent }}
                                    </div>
                                </td>
                                @canany(['terms-and-condition-edit', 'terms-and-condition-delete'])
                                    <td class="text-center">
                                        <div class="vendor-action">
                                            @can('terms-and-condition-edit')
                                                <a href="{{ route('terms.edit', $term->id) }}" class="btn btn-info btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('terms-and-condition-delete')
                                                <form method="POST" action="{{ url('/terms/delete/' . $term->id) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this term?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @endforeach
                    </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @endcan

@endsection

@section("scripts")
    <script>
        let table = new DataTable('#terms-table', {
            autoWidth: false,
            columnDefs: [
                { targets: 0, className: 'text-center' },
                { targets: -1, orderable: false, className: 'text-center' }
            ]
        });
    </script>
@endsection