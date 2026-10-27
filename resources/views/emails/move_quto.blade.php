<h2>Lead Approved for Quotation</h2>
<p><strong>Lead ID:</strong> {{ $mailData['lead_id'] }}</p>
<p><strong>Category:</strong> {{ $mailData['category'] }}</p>
<p><strong>Product:</strong> {{ $mailData['product'] }}</p>

@if($mailData['quota_doc'])
    <p><strong>Document:</strong> <a href="{{ asset('storage/' . $mailData['quota_doc']) }}" target="_blank">View Document</a></p>
@else
    <p>No document available.</p>
@endif

<p>Thank you!</p>
