<!DOCTYPE html>
<html>
<head>
    <title>New Lead Created</title>
</head>
<body>
    <h2>{{ $lead->lead_id }} New Lead Created</h2>
    <p><strong>Lead ID:</strong> {{ $lead->lead_id }}</p>
    <p><strong>Name:</strong> {{ $lead->LeadName }}</p>
    <p><strong>Email:</strong> {{ $lead->email }}</p>
    <p><strong>Assigned To:</strong> {{ $lead->assignedTo->name ?? 'Not Assigned' }}</p>
    <p><strong>Status:</strong> {{ $lead->leadstatus->drodwondata ?? 'Status not Updates' }}</p>
    <p><strong>Entry Date:</strong> {{ $lead->Entrydate }}</p>
</body>
</html>

