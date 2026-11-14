<h2>ORF CS Status Notification</h2>
@if(!empty($data['customer_name']))
    <p><strong>Customer Name:</strong> {{ $data['customer_name'] }}</p>
@endif
<p><strong>ORF ID:</strong> {{ $data['orf_id'] }}</p>
<p><strong>CS Status:</strong> {{ ucfirst($data['status']) }}</p>
<p><strong>Dispatch Date:</strong> {{ $data['dispatchdate'] }}</p>
@if($data['status'] == 'rejected')
    <p><strong>Rejection Remark:</strong> {{ $data['csreject_remark'] }}</p>
@endif

<p><strong>Updated By:</strong> {{ $data['auth_user'] }}</p>

<p>Thank you!</p>
