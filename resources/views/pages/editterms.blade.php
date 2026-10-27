@extends('layouts.app')
@section('title', 'Add Terms Details')
@section('main-content')

    <section class="section leadsection">
        <div class="container form-card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mt-2">Add Terms Details</h5>
                        </div>
                        <div class="col-lg-4">
                        </div>
                        <div class="col-lg-2">
                            <a onclick="history.back()" class="btn btn-primary d-block add-employee-btn">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('terms.update', $term->id) }}">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    {{-- <div class="col-lg-4">
                        <div class="form-group mt-4">
                            <label for="whichterm" class="form-label">Invoice Type<span style="color:red">*</span></label>
                            <select class="form-select" name="whichterm" required>
                                <option value="">-- Choose Invoice --</option>
                                <option value="1" {{ $term->whichterm == 1 ? 'selected' : '' }}>Sales Quotation</option>
                                <option value="2" {{ $term->whichterm == 2 ? 'selected' : '' }}>PO Invoice</option>
                            </select>
                        </div>
                    </div> --}}
                    <div class="col-lg-4">
                        <div class="form-group mt-4">
                            <label for="termtype" class="form-label">Terms Type <span style="color:red">*</span></label>
                            <select class="form-select" name="termtype" id="termtype" required>
                                <option value="">-- Choose Terms Type --</option>
                                <option value="1" {{ $term->termtype == 1 ? 'selected' : '' }}>Payment Terms</option>
                                <option value="2" {{ $term->termtype == 2 ? 'selected' : '' }}>Warranty Terms</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group mt-4">
                            <label for="product_category_id" class="form-label">Product Segment</label>
                            <select class="form-select" name="product_category_id" id="segment">
                                <option value="">-- Choose Segment --</option>
                                @foreach(App\Models\ProductCategory::all() as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $term->product_category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4" id="productGroupDiv" style="{{ $term->termtype == 2 ? '' : 'display:none;' }}">
                        <div class="form-group mt-4">
                            <label for="product_group_id" class="form-label">Product Group</label>
                            <select class="form-select" name="product_sub_category_id" id="product_group_id">
                                <option value="">-- All Groups / General --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="form-group mt-4">
                            <label for="content" class="form-label">Content <span style="color:red">*</span></label>
                            <textarea name="content" class="form-control" required>{{ $term->content }}</textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary m-auto d-block add-employee-btn mt-4 mb-2">
                    Update Content
                </button>
            </form>
    </section>
@endsection
@section('scripts')
<script>
$(document).ready(function () {

    function loadProductGroups(segmentId, selectedGroup = '') {
        let termType = $('#termtype').val();

        if (termType == '2' && segmentId) {
            $('#productGroupDiv').show();

            $.get('/subcategories/by-segment/' + segmentId, function(groups) {
                $('#product_group_id').html('<option value="">-- All Groups / General --</option>');

                $.each(groups, function(index, group) {
                    let selected = (group.id == selectedGroup) ? 'selected' : '';
                    $('#product_group_id').append(
                        '<option value="' + group.id + '" ' + selected + '>' + group.subcategory_name + '</option>'
                    );
                });
            });
        } else {
            $('#productGroupDiv').hide();
            $('#product_group_id').html('<option value="">-- All Groups / General --</option>');
        }
    }

    // Change event
    $('#segment, #termtype').change(function () {
        loadProductGroups($('#segment').val());
    });

    // Edit page initial load
    let segmentId = $('#segment').val();
    let selectedGroup = "{{ $term->product_sub_category_id }}";

    if (segmentId != '') {
        loadProductGroups(segmentId, selectedGroup);
    }
});
</script>
@endsection
