<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationChargeApprovalHistory;
use Illuminate\Http\Request;

class QuotationChargeApprovalController extends Controller
{
    public function pending()
    {
        $quotations = Quotation::with('lead')
            ->whereRaw('quotations.id = (
                SELECT MAX(q2.id) 
                FROM quotations as q2 
                WHERE q2.id_lead = quotations.id_lead
            )')
            ->where('charge_approval_status', 'Pending Approval')
            ->latest()
            ->get();
        return view('pages.quotation.charge_approvals', compact('quotations'));
    }

    public function decide(Request $request, Quotation $quotation)
    {
        abort_unless($this->canApprove(), 403);
        abort_unless($quotation->charge_approval_status === 'Pending Approval', 422, 'This quotation is not awaiting charge approval.');
        $data = $request->validate(['decision' => 'required|in:Approved,Rejected', 'remarks' => 'required_if:decision,Rejected|nullable|string|max:2000']);

        $updateData = [
            'charge_approval_status' => $data['decision'],
            'charge_approved_by' => auth()->id(),
            'charge_approved_at' => now(),
        ];

        if ($data['decision'] === 'Approved') {
            $hasOtherPending = \App\Models\QuotationTermChangeRequest::where('quotation_id', $quotation->id)->where('status', 'Pending Approval')->exists()
                || $quotation->dis_app == 1
                || $quotation->freight_approval == 1;
            if (!$hasOtherPending) {
                $updateData['quto_app'] = 2;
            }
        }

        $quotation->update($updateData);
        $this->history($quotation, $data['decision'], $data['decision'], $data['remarks'] ?? null);

        return back()->with('success', "Quotation charge request {$data['decision']}.");
    }

    public function pendingTerms()
    {
        abort_unless($this->canApprove(), 403);
        $requests = \App\Models\QuotationTermChangeRequest::with([
            'quotation.lead',
            'oldTerm',
            'newTerm',
            'requester'
        ])->where('status', 'Pending Approval')->latest()->get();

        return view('pages.quotation.term_approvals', compact('requests'));
    }

    public function decideTerms(Request $request, $id)
    {
        abort_unless($this->canApprove(), 403);

        $changeRequest = \App\Models\QuotationTermChangeRequest::findOrFail($id);
        abort_unless($changeRequest->status === 'Pending Approval', 422, 'This request has already been processed.');

        $data = $request->validate([
            'decision' => 'required|in:Approved,Rejected',
            'remarks' => 'required_if:decision,Rejected|nullable|string|max:2000'
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $changeRequest->update([
                'status' => $data['decision'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'remarks' => $data['remarks'] ?? null,
            ]);

            if ($data['decision'] === 'Approved') {
                $freight = \App\Models\Freight::where('quotationno', $changeRequest->quotation_id)->first();
                if ($freight) {
                    if ($changeRequest->type === 'payment_term') {
                        $freight->update(['termscondition' => $changeRequest->new_term_id]);
                    } elseif ($changeRequest->type === 'warranty') {
                        $freight->update(['warranty' => $changeRequest->new_term_id]);
                    }
                }

                // Mark the new term as approved in the terms master table
                // so it appears in future dropdown selections (term_approve: 0 = approved)
                $newTerm = \App\Models\Term::find($changeRequest->new_term_id);
                if ($newTerm && $newTerm->term_approve != 0) {
                    $newTerm->update(['term_approve' => 0]);
                }

                // Update quotation quto_app to 2 if no other approvals pending
                $quot = \App\Models\Quotation::find($changeRequest->quotation_id);
                if ($quot) {
                    $hasOtherPending = \App\Models\QuotationTermChangeRequest::where('quotation_id', $quot->id)->where('status', 'Pending Approval')->where('id', '!=', $changeRequest->id)->exists()
                        || $quot->dis_app == 1
                        || $quot->freight_approval == 1
                        || in_array($quot->charge_approval_status, ['Pending Approval', 'Pending']);
                    if (!$hasOtherPending) {
                        $quot->update(['quto_app' => 2]);
                    }
                }
            } else {
                // Rejected — mark the new term as rejected (term_approve: 2 = rejected)
                $newTerm = \App\Models\Term::find($changeRequest->new_term_id);
                if ($newTerm && $newTerm->term_approve == 1) {
                    $newTerm->update(['term_approve' => 2]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', "Quotation term request {$data['decision']}.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    private function canApprove(): bool
    {
        return in_array(auth()->user()->role, ['Super Admin', 'Admin', 'Director', 'Manager'], true);
    }

    private function history(Quotation $quotation, string $action, string $status, ?string $remarks = null): void
    {
        QuotationChargeApprovalHistory::create([
            'quotation_id' => $quotation->id, 'action' => $action, 'status' => $status,
            'default_installation_charge' => $quotation->default_installation_charge,
            'installation_charge' => $quotation->installation_charge,
            'default_freight_charge' => $quotation->default_freight_charge,
            'freight_charge' => $quotation->freight_charge,
            'acted_by' => auth()->id(), 'remarks' => $remarks,
        ]);
    }
}