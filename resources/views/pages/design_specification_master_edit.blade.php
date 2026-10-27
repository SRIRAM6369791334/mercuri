@extends('layouts.app')

@section('main-content')
    <h4 class="mb-3">Edit Design Specification</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('design.spec.master.update', $spec->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Product Category</label>
            <input type="text" class="form-control" value="{{ $spec->category->category_name ?? 'Unknown' }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Type</label>
            <input type="text" class="form-control text-uppercase" value="{{ $spec->type }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Specification <span class="text-danger">*</span></label>
            <input type="text" name="spec_name" class="form-control" value="{{ old('spec_name', $spec->spec_name) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Specification</button>
        <a href="{{ route('design.spec.master.create') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
