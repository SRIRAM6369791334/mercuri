<h2>Design Engineer Mail</h2>

@foreach($data as $item)
<ul>
    <li><strong>Lead ID:</strong> {{ $item['leadiid'] ?? 'N/A' }}</li>
    <li><strong>Category:</strong> {{ $item['catename'] ?? 'N/A' }}</li>
    <li><strong>Product Name:</strong> {{ $item['proname'] ?? 'N/A' }}</li>
    <li><strong>Design:</strong> {{ $item['design'] ?? 'N/A' }}</li>

    @if(isset($item['basicsupply']))
        <h3>BreakDown</h3>
        <ul>
            <li><strong>Basic Supply:</strong> {{ $item['basicsupply'] ?? 'N/A' }}</li>
            <li><strong>Freight Value:</strong> {{ $item['freightvalue'] ?? 'N/A' }}</li>
            <li><strong>Install Value:</strong> {{ $item['installvalue'] ?? 'N/A' }}</li>
            <li><strong>Price Value:</strong> {{ $item['pricevalue'] ?? 'N/A' }}</li>
        </ul>
    @endif
</ul>
@endforeach

@if(isset($uploadedFiles) && count($uploadedFiles) > 0)
    <h3>Attachments</h3>
    @foreach($uploadedFiles as $file)
        <p><a href="{{ asset($file) }}" target="_blank">View File</a></p>
    @endforeach
@endif

<p>Please find the attached file(s).</p>
