@extends('layouts.app')
@section('title', 'Edit Design Files')

@section('main-content')
<div class="container">
    <h4 class="mb-3">Edit Design Files</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST"
          action="{{ route('design.update.files', $design->id) }}"
          enctype="multipart/form-data">
        @csrf

        {{-- Quotation --}}
        <div class="mb-3">
            <label class="form-label">Quotation</label><br>

            @if($design->quotation)
                <a href="{{ asset('storage/'.$design->quotation) }}"
                   target="_blank"
                   class="btn btn-sm btn-outline-primary mb-2">
                    View Current Quotation
                </a>
            @endif

            <input type="file"
                   name="quotation"
                   class="form-control"
                   accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx">
        </div>

        {{-- Drawing --}}
        <div class="mb-3">
            <label class="form-label">Drawing</label><br>

            @if($design->drawing)
                <a href="{{ asset('storage/'.$design->drawing) }}"
                   target="_blank"
                   class="btn btn-sm btn-outline-secondary mb-2">
                    View Current Drawing
                </a>
            @endif

            <input type="file"
                   name="drawing"
                   class="form-control"
                   accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx">
        </div>

        <button class="btn btn-success">Update Files</button>
        <a href="{{ route('design.engineer.list') }}"
           class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
