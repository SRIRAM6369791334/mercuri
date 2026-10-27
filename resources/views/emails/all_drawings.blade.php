<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Design Drawings</title>
</head>
<body style="font-family: Arial, sans-serif; font-size:14px;">

<p>Dear Team,</p>

<p>Please find below all the design drawings shared for your reference.</p>

{{-- @foreach($drawings as $index => $drawing)
<hr>

<p><strong>Drawing {{ $index + 1 }}</strong></p>

<p>
    <a href="{{ asset('storage/'.$drawing->file_path) }}" target="_blank">
        View Drawing
    </a>
</p>

@if($drawing->sales_attach)
<p>
    <strong>Sales Attachment:</strong><br>
    <a href="{{ asset('storage/'.$drawing->sales_attach) }}" target="_blank">
        View Attachment
    </a>
</p>
@endif

@if($drawing->drawing_remarks)
<p>
    <strong>Remarks:</strong><br>
    {{ $drawing->drawing_remarks }}
</p>
@endif

@endforeach --}}

<br>

<p>Regards,<br>
<strong>{{ auth()->user()->name }}</strong></p>

</body>
</html>
