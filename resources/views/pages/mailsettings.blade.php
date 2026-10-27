@extends('layouts.app')
@section('main-content')


    <div class="card">
    <div class="card-header">Mail Settings</div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('mail.settings.update') }}">
            @csrf

            <!-- FROM EMAIL -->
            <div class="mb-3">
                <label class="form-label">From Email</label>
                <input type="email"
                       name="from_email"
                       class="form-control"
                       value="{{ $mailsetting->from_email ?? '' }}"
                       required>
            </div>

            <!-- CC EMAILS -->
            <label class="form-label">CC Emails</label>

            <div id="cc-wrapper">
                @php
                    $ccEmails = isset($mailsetting->cc_email)
                        ? explode(',', $mailsetting->cc_email)
                        : [''];
                @endphp

                @foreach($ccEmails as $cc)
                    <div class="d-flex gap-2 mb-2 cc-row">
                        <input type="email"
                               name="cc_email[]"
                               class="form-control"
                               value="{{ $cc }}"
                               placeholder="CC Email">

                        <button type="button"
                                class="btn btn-danger remove-cc">✕</button>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-secondary mb-3" id="add-cc">
                + Add CC
            </button>

            <br>

            <button type="submit" class="btn btn-primary">
                Save Settings
            </button>
        </form>
    </div>
</div>

@endsection
@section('scripts')
<script>
document.getElementById('add-cc').addEventListener('click', function () {

    let wrapper = document.getElementById('cc-wrapper');

    let row = document.createElement('div');
    row.className = 'd-flex gap-2 mb-2 cc-row';

    row.innerHTML = `
        <input type="email" name="cc_email[]" class="form-control" placeholder="CC Email">
        <button type="button" class="btn btn-danger remove-cc">✕</button>
    `;

    wrapper.appendChild(row);
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-cc')) {
        e.target.closest('.cc-row').remove();
    }
});
</script>

@endsection
