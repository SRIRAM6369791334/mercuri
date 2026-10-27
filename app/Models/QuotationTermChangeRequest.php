<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationTermChangeRequest extends Model
{
    protected $fillable = ['quotation_id', 'type', 'old_term_id', 'new_term_id', 'status', 'requested_by', 'approved_by', 'approved_at', 'remarks'];
    protected $casts = ['approved_at' => 'datetime'];

    public function quotation() { return $this->belongsTo(Quotation::class); }
    public function oldTerm() { return $this->belongsTo(Term::class, 'old_term_id'); }
    public function newTerm() { return $this->belongsTo(Term::class, 'new_term_id'); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}
