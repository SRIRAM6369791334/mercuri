@extends('layouts.app')

@section('main-content')
    <style>
        .quotation-image-card {
            height: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .quotation-image-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 230px;
            padding: 14px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .quotation-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .quotation-image-empty {
            color: #64748b;
            text-align: center;
        }
    </style>

    <div class="container py-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h4 class="mb-1">Quotation Product Group Images</h4>
                <p class="text-muted mb-0">Add or replace the feature image shown in quotations for each product group.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="search-group-input" class="form-control" placeholder="Search group or segment..." style="width: 240px;">
                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#add_category_modal">
                    <i class="mdi mdi-plus"></i> Add Group
                </button>
                <a href="{{ url('/viewquota') }}" class="btn btn-outline-secondary">Back to Quotations</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info">
            PNG, JPG, JPEG and WEBP images are supported up to 8 MB. If no custom image is uploaded, the existing automatic product-group image remains active.
        </div>

        <div class="row g-4" id="group-cards-container">
            @forelse($productGroups as $group)
                @php
                    $defaultImage = \App\Helpers\QuotationPresentationHelper::defaultProductGroupImage(
                        $group->subcategory_name,
                        $group->product?->productname ?? '',
                    );
                    $currentImage = $group->quotation_image
                        ? asset(ltrim($group->quotation_image, '/'))
                        : ($defaultImage ? asset($defaultImage) : null);
                @endphp

                <div class="col-xl-4 col-md-6 group-card-item"
                     data-group-name="{{ strtolower($group->subcategory_name) }}"
                     data-segment-name="{{ strtolower($group->segmentDetails?->category_name ?? '') }}">
                    <div class="quotation-image-card">
                        <div class="quotation-image-preview" id="preview-container-{{ $group->id }}">
                            @if($currentImage)
                                <img src="{{ $currentImage }}" id="preview-img-{{ $group->id }}" alt="{{ $group->subcategory_name }} quotation image">
                            @else
                                <div class="quotation-image-empty" id="empty-preview-{{ $group->id }}">
                                    <i class="mdi mdi-image-off-outline fs-2"></i>
                                    <div>No image configured</div>
                                </div>
                            @endif
                        </div>

                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <div>
                                    <h5 class="mb-1">{{ $group->subcategory_name }}</h5>
                                    <div class="text-muted small">{{ $group->segmentDetails?->category_name ?? 'No segment' }}</div>
                                </div>
                                <span class="badge {{ $group->quotation_image ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $group->quotation_image ? 'Custom image' : 'Default image' }}
                                </span>
                            </div>

                            <form method="POST" action="{{ route('quotation-group-images.update', $group) }}" enctype="multipart/form-data">
                                @csrf
                                <label for="quotation-image-{{ $group->id }}" class="form-label">
                                    {{ $group->quotation_image ? 'Replace image' : 'Add image' }}
                                </label>
                                <input
                                    id="quotation-image-{{ $group->id }}"
                                    type="file"
                                    name="quotation_image"
                                    class="form-control group-image-input"
                                    data-group-id="{{ $group->id }}"
                                    accept="image/png,image/jpeg,image/webp"
                                    required
                                >
                                <button type="submit" class="btn btn-primary w-100 mt-3">
                                    <i class="mdi mdi-upload me-1"></i> {{ $group->quotation_image ? 'Update Image' : 'Upload Image' }}
                                </button>
                            </form>

                            @if($group->quotation_image)
                                <form method="POST" action="{{ route('quotation-group-images.delete', $group) }}" class="mt-2" onsubmit="return confirm('Are you sure you want to reset this to the default image?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="mdi mdi-restore me-1"></i> Reset to Default Image
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning">No product groups are available.</div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Add Group Modal -->
    <div class="modal fade modal-lg" id="add_category_modal" aria-hidden="true" aria-labelledby="add_group_modalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="add_group_modalLabel">Add Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="/add-sub-category-action">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-lg-6">
                                <div class="form-group mt-2">
                                    <label class="form-label">Product Segment <span style="color:red">*</span></label>
                                    <select class="form-select" id="add_product_segment_id" name="product_segment_id" required>
                                        <option value="">-- Choose Segment --</option>
                                        @if ($status = App\Models\ProductCategory::get())
                                            @foreach ($status as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->category_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mt-2">
                                    <label class="form-label">
                                        Group name <span style="color:red">*</span>
                                    </label>
                                    <input type="text" id="add_sub_category" name="sub_category" class="form-control" placeholder="Type group name..." required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-12">
                                <div class="form-group mt-2">
                                    <label class="form-label">
                                        Product (Multiple Select)
                                    </label>
                                    <select class="form-select select2-add-products" id="add_product_ids" name="product_ids[]" multiple="multiple" style="width: 100%;">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary m-auto d-block mt-4 mb-2">Add Group</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2 on multiple product dropdown
            if ($.fn.select2) {
                $('.select2-add-products').select2({
                    placeholder: '-- Choose Products --',
                    allowClear: true,
                    dropdownParent: $('#add_category_modal')
                });
            }

            // Reset Add Group modal on show
            $('#add_category_modal').on('show.bs.modal', function () {
                $('#add_product_segment_id').val('');
                $('#add_sub_category').val('');
                $('#add_product_ids').empty();
                if ($.fn.select2) {
                    $('#add_product_ids').trigger('change.select2');
                }
            });

            // Function to fetch products via AJAX based on keyword and segment
            function fetchProductsByKeyword(keyword, segmentId, targetSelectEl) {
                if (!segmentId) {
                    $(targetSelectEl).empty();
                    if ($.fn.select2) {
                        $(targetSelectEl).trigger('change.select2');
                    } else {
                        $(targetSelectEl).trigger('change');
                    }
                    return;
                }

                $.ajax({
                    url: '/get-products-by-keyword',
                    type: 'GET',
                    data: { keyword: keyword, segment_id: segmentId },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status) {
                            let currentSelected = $(targetSelectEl).val() || [];
                            if (!Array.isArray(currentSelected)) {
                                currentSelected = [currentSelected];
                            }
                            currentSelected = currentSelected.map(String);

                            $(targetSelectEl).empty();

                            if (res.products && res.products.length > 0) {
                                res.products.forEach(function (prod) {
                                    let isSel = currentSelected.includes(String(prod.id));
                                    let option = new Option(prod.productname, prod.id, isSel, isSel);
                                    $(targetSelectEl).append(option);
                                });
                            }

                            if ($.fn.select2) {
                                $(targetSelectEl).trigger('change.select2');
                            } else {
                                $(targetSelectEl).trigger('change');
                            }
                        }
                    }
                });
            }

            // Trigger product search on typing Group Name or changing Segment in Add Modal
            let addDebounce;
            $('#add_sub_category, #add_product_segment_id').on('input keyup change', function () {
                clearTimeout(addDebounce);
                addDebounce = setTimeout(function () {
                    let keyword = $('#add_sub_category').val().trim();
                    let segmentId = $('#add_product_segment_id').val();
                    fetchProductsByKeyword(keyword, segmentId, '#add_product_ids');
                }, 300);
            });

            // Live search filter on cards
            $('#search-group-input').on('input keyup', function () {
                let q = $(this).val().toLowerCase().trim();
                $('.group-card-item').each(function () {
                    let gName = ($(this).data('group-name') || '').toString();
                    let sName = ($(this).data('segment-name') || '').toString();
                    if (!q || gName.includes(q) || sName.includes(q)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Handle image file input change preview
            document.querySelectorAll('.group-image-input').forEach(function (input) {
                input.addEventListener('change', function (e) {
                    const groupId = this.dataset.groupId;
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (event) {
                            let img = document.getElementById('preview-img-' + groupId);
                            const emptyDiv = document.getElementById('empty-preview-' + groupId);
                            const container = document.getElementById('preview-container-' + groupId);

                            if (!img) {
                                if (emptyDiv) emptyDiv.remove();
                                img = document.createElement('img');
                                img.id = 'preview-img-' + groupId;
                                container.appendChild(img);
                            }
                            img.src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        });
    </script>
@endsection