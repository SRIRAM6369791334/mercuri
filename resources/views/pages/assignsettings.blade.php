@extends('layouts.app')
@section('main-content')
<div class="card">
    <div class="card-header">Lead Assignment Settings</div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('assign.settings.update') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">MHE Special → Assign To</label>
                <select name="mhe_special_user_id" class="form-select">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            @selected(optional($assignSetting)->mhe_special_user_id == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Racking → Assign To</label>
                <select name="racking_user_id" class="form-select">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            @selected(optional($assignSetting)->racking_user_id == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Pallet → Assign To</label>
                <select name="pallet_user_id" class="form-select">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            @selected(optional($assignSetting)->pallet_user_id == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-primary">Save Assignment</button>
        </form>
    </div>
</div>
@endsection
