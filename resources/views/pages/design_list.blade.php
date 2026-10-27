@extends('layouts.app')
@section('title', 'Output Lists')
@section('main-content')

<div class="container">
    <h4 class="mb-3">My Assigned Designs</h4>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Quotation</th>
                <th>Drawing</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($designs as $design)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>
                        @if($design->quotation)
                            <a href="{{ asset('storage/'.$design->quotation) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                View Quotation
                            </a>
                        @else
                            <span class="text-muted">Not Uploaded</span>
                        @endif
                    </td>

                    <td>
                        @if($design->drawing)
                            <a href="{{ asset('storage/'.$design->drawing) }}"
                               target="_blank" class="btn btn-sm btn-outline-secondary">
                                View Drawing
                            </a>
                        @else
                            <span class="text-muted">Not Uploaded</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('design.edit', $design->id) }}"
                           class="btn btn-sm btn-warning">
                            Edit
                        </a>

                        {{-- <a href="{{ route('design.update.page', $design->id) }}"
                           class="btn btn-sm btn-success">
                            Update
                        </a> --}}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        No designs assigned
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
