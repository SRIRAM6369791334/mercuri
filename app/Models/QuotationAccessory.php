<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationAccessory extends Model
{
    protected $table = 'quotation_accessories';

    protected $fillable = [
        'quotation_id',
        'quotationproduct_id',
        'quotationno',
        'leadno',
        'accessory_id',
        'name',
        'part_no',
        'hsn',
        'price',
        'quantity',
        'total',
    ];

    public function product()
    {
        return $this->belongsTo(QuotationProduct::class, 'quotationproduct_id');
    }

    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'accessory_id');
    }
}