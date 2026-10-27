<h2>ORF Status Notification</h2>
<p><strong>ORF ID:</strong> {{ $data['orf_id'] }}</p>
<p><strong>Status:</strong> {{ ucfirst($data['status']) }}</p>
@if($data['status'] == 'rejected')
    <p><strong>Rejection Remark:</strong> {{ $data['reject_remark'] }}</p>
@endif

<p><strong>Updated By:</strong> {{ $data['auth_user'] }}</p>

<p>Thank you!</p>
