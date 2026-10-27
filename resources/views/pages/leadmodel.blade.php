<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Change Lead Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm" method="POST" action="{{ route('leads.updateStatusdf') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lead_id" id="lead_id">
                    <label for="lead_status">Select Status</label>
                    <select name="lead_status" id="lead_status" class="form-control">
                        <option value="1">New</option>
                        <option value="2">In Progress</option>
                        <option value="3">Closed</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
