<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orf extends Model
{
    use HasFactory;

    protected $table = 'orf';

    protected $fillable = [
        'tranref','quoproid' , 'leano', 'quono', 'custo', 'specialproduct','quotadate', 'orfref', 'orfdate',
        'cusporef', 'podate', 'attcuspo', 'approval_status', 'cs_status', 'reject_remark', 'csreject_remark','sigdraw', 'crddate', 'specialins',
        'po_status', 'supplier_po_no', 'po_released_at'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'leano'); // Assuming `leadno` links to `leads.id`
    }
    public function quopro()
    {
        return $this->belongsTo(QuotationProduct::class, 'quoproid'); // Assuming `leadno` links to `leads.id`
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quoproid');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'orf_id');
    }

    // Orf.php
public function attachments()
{
    return $this->hasMany(OrfAttachment::class, 'orf_id');
}

}
