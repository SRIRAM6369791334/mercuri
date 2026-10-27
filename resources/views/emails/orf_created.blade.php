<h1>New ORF Created</h1>
<p><strong>ORF Reference:</strong> {{ $data['orf_ref'] }}</p>
<p><strong>Quotation No:</strong> {{ $data['quotation_no'] }}</p>
<p><strong>Customer:</strong> {{ $data['customer'] }}</p>
<p><strong>ORF Date:</strong> {{ $data['orf_date'] }}</p>
<p><strong>PO Date:</strong> {{ $data['po_date'] }}</p>
<p><strong>Special Instructions:</strong> {{ $data['special_instructions'] }}</p>

@if($data['attcuspo'])
    <p><strong>Customer PO:</strong> <a href="{{ asset($data['attcuspo']) }}" target="_blank">View File</a></p>
@endif

@if($data['sigdraw'])
    <p><strong>Signature/Draw:</strong> <a href="{{ asset($data['sigdraw']) }}" target="_blank">View File</a></p>
@endif
