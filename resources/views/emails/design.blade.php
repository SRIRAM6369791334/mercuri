<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Design Assigned</title>
</head>
<body>
    <h3>Design Assigned</h3>

    <ul>
        <li><strong>Lead ID:</strong> {{ $lead_id }}</li>
        <li><strong>Category:</strong> {{ $category }}</li>
    </ul>

    @if($specifications->count())
        <h4>Design Specifications</h4>
        <ul>
            @foreach($specifications as $spec)
                <li>{{ $spec->spec_value }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html>
