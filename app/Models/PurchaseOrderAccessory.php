<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderAccessory extends Model
{
    protected $table = 'po_line_accessories';

    protected $fillable = [
        'purchase_order_line_id',
        'accessory_id',
        'name',
        'part_no',
        'hsn',
        'unit_price',
        'quantity',
        'total',
    ];

    public function purchaseOrderLine()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_line_id');
    }
}
