<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leads';

    protected $dates = ['deleted_at'];

    protected $guarded = [];


    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function leadstatus()
    {
        return $this->belongsTo(Dropdowndata::class, 'Leadstatus');
    }
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function categoryDetails()
    {
        return $this->belongsTo(ProductCategory::class, 'category');
    }

    public function productSubCategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'subcategory_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'products');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'products');
    }

    public function cityData()
    {
        return $this->belongsTo(Citylist::class, 'citylist');
    }

    public function stateData()
    {
        return $this->belongsTo(Statelist::class, 'state_id');
    }

    public function getCityNameAttribute()
    {
        return $this->cityData?->city_name ?? '';
    }

    public function getStateNameAttribute()
    {
        return $this->stateData?->state ?? '';
    }

    protected $casts = [
        'converteddate' => 'datetime',
    ];

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'lead_id', 'id');
    }

}


