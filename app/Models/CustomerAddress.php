<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $table = 'customer_addresses';

    protected $fillable = [
        'customer_id',
        'lead_id',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
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
