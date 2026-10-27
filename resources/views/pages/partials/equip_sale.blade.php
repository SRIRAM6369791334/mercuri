<table class="table table-bordered display responsive nowrap" id="pendingTasks">
    <thead>
        <tr>
            <th>Lead ID</th>
            <th>Lead Name</th>
            <th>Category</th>
            <th>Products</th>
            <th>Converted Date</th>
            <th>Total Value</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($leads as $lead)
            <tr>
                <td>{{ $lead->lead_id }}</td>
                <td>{{ $lead->LeadName }}</td>
                <td>{{ $lead->category_name ?? 'N/A' }}</td>
                <td>{{ $lead->productname ?? 'N/A' }}</td>
                <td>{{ $lead->converteddate ? \Carbon\Carbon::parse($lead->converteddate)->format('d-m-Y') : 'N/A' }}</td>
                <td>{{ $lead->grandtotal ?? 'N/A' }}</td>
                <td>{{ $lead->stage ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">No leads available</td>
            </tr>
        @endforelse
    </tbody>
</table>
