<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendor_addresses';

    protected $fillable = [
        'vendor_id',
        'address_type',
        'address',
        'gst_number',
        'country_id',
        'state_id',
        'city_id',
        'postal_code',
        'contact_person',
        'contact_number',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function state()
    {
        return $this->belongsTo(Statelist::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(Citylist::class, 'city_id');
    }
}
