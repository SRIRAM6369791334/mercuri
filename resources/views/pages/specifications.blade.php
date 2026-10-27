@extends('layouts.app')
@section('main-content')



<form action="{{ route('design.spec.master.store') }}" method="POST">
    @csrf

    <!-- Category -->
    <div class="mb-3">
        <label class="form-label">Product Category <span class="text-danger">*</span></label>
        <select name="category_id" class="form-select" required>
            <option value="">Select Category</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">
                    {{ $cat->category_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Type -->
    <div class="mb-3">
        <label class="form-label">Type <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            <option value="">Select Type</option>
            <option value="mhe">MHE</option>
            <option value="rack">Rack</option>
            <option value="pallet">Pallet</option>
        </select>
    </div>

    <!-- Specifications -->
    <div class="mb-3">
        <label class="form-label">Specifications <span class="text-danger">*</span></label>

        <div id="specWrapper">
            <div class="input-group mb-2">
                <input type="text" name="spec_name[]" class="form-control"
                       placeholder="Enter specification (e.g. Load Capacity)" required>
                <button type="button" class="btn btn-success addSpec">+</button>
            </div>
        </div>
    </div>

    <button class="btn btn-primary">Save Specifications</button>
</form>

<hr>

<h4 class="mt-4">Existing Design Specifications</h4>

@if(isset($specs) && count($specs))
    @foreach($specs as $categoryId => $types)
        @php
            $categoryName = $types->first()->first()->category->category_name ?? 'Unknown';
        @endphp

        <div class="card mt-3">
            <div class="card-header bg-light">
                <strong>Category:</strong> {{ $categoryName }}
            </div>

            <div class="card-body">
                @foreach($types as $type => $items)
                    <h6 class="text-primary text-uppercase">
                        Type: {{ $type }}
                    </h6>

                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="60%">Specification</th>
                                <th width="40%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $spec)
                                <tr>
                                    <td>{{ $spec->spec_name }}</td>
                                    <td>
                                        <a href="{{ route('design.spec.master.edit', $spec->id) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form action="{{ route('design.spec.master.delete', $spec->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this specification?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
    @endforeach
@else
    <p class="text-muted">No specifications found.</p>
@endif


@endsection
@section('scripts')
<script>
document.addEventListener('click', function (e) {

    // Add spec
    if (e.target.classList.contains('addSpec')) {
        let html = `
            <div class="input-group mb-2">
                <input type="text" name="spec_name[]" class="form-control"
                       placeholder="Enter specification" required>
                <button type="button" class="btn btn-danger removeSpec">−</button>
            </div>`;
        document.getElementById('specWrapper')
            .insertAdjacentHTML('beforeend', html);
    }

    // Remove spec
    if (e.target.classList.contains('removeSpec')) {
        e.target.closest('.input-group').remove();
    }
});
</script>
@endsection
