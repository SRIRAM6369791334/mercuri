<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customername',
        'assigned_user',
        'cusid',
        'contact_name',
        'cus_email',
        'country_code',
        'contact_no',
        'designation',
        'department',
        'address',
        'country',
        'city',
        'state',
        'postalcode',
        'gstno',
        'soft_d',
        'communication_address',
    ];

    public function getCityNameAttribute()
    {
        if (isset($this->attributes['city_name']) && !empty($this->attributes['city_name'])) {
            return $this->attributes['city_name'];
        }
        if (empty($this->city)) {
            return null;
        }
        if (is_numeric($this->city)) {
            $city = \Illuminate\Support\Facades\DB::table('city_list')->where('id', $this->city)->first();
            return $city ? $city->city_name : $this->city;
        }
        return $this->city;
    }

    public function getStateNameAttribute()
    {
        if (empty($this->state)) {
            return null;
        }
        if (is_numeric($this->state)) {
            $state = \Illuminate\Support\Facades\DB::table('state_list')->where('id', $this->state)->first();
            return $state ? ($state->state ?? $state->state_name ?? $this->state) : $this->state;
        }
        return $this->state;
    }

    public function getLocationDisplayAttribute()
    {
        $city = $this->city_name;
        $pin = $this->postalcode;

        if ($city && $pin) {
            return $city . ' - ' . $pin;
        }
        if ($city) {
            return $city;
        }
        if ($pin) {
            return $pin;
        }
        return '-';
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id', 'id');
    }
}
