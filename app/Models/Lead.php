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

    protected $casts = [
        'converteddate' => 'datetime',
    ];

}


