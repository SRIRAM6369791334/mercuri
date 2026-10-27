<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationProduct extends Model
{
    use HasFactory;

    protected $table = 'quotationproduct';

    protected $fillable = [
        'quotationno',
        'leadno',
        'grandtotal',
        'cliname',
        'placeofsupply',
        'shippostalcode',
        'shcitylist',
        'shistate_id',
        'shippingcountry',
        'gstnum',
        'catepro',
        'product',
        'part_no',
        'quanty',
        'allowdis',
        'requestdis',
        'requestdis_amt',
        'revised_disamt',
        'revised_discount',
        'ratee',
        'quto_app',
        'dis_app',
        'qutoremark',
        'disremark',
        'price',
        'priceamt',
        'lbt',
        'tax',
        'taxamt',
        'freight_approval',
        'same_as_billing_address'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'leadno'); // Assuming `leadno` links to `leads.id`
    }

    public function productDetails()
    {
        return $this->belongsTo(Product::class, 'product', 'id');
    }

    public function freight()
    {
        return $this->hasOne(Freight::class, 'quotationno', 'id');
    }

    public function orfDetails()
    {
        return $this->hasOne(Orf::class, 'quoproid', 'id');
    }

    public function accessories()
    {
        return $this->hasMany(QuotationAccessory::class, 'quotationproduct_id');
    }
}
