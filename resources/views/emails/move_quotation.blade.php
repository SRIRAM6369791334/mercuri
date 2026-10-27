<h1>Move Design to Quotation Confirmation</h1>

<p><strong>Lead ID:</strong> {{ $mailData['leadid'] }}</p>

<ul>
    @foreach($mailData['products'] as $index => $product)
        <li>
            <strong>Product:</strong> {{ $product }} <br>
            <strong>Category:</strong> {{ $mailData['categorys'][$index] }} <br>
            <strong>Quantity:</strong> {{ $mailData['quantitys'][$index] }}
        </li>
    @endforeach
</ul>

@if($mailData['quota_doc'])
    <p><strong>Quota Document:</strong>
        <a href="{{ asset($mailData['quota_doc']) }}" target="_blank">View Document</a>
    </p>
@endif
