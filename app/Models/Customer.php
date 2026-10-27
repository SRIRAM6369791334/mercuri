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
}
