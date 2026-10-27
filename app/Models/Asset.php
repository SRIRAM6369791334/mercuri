<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

     protected $fillable = [
        'asset_name',
        'asset_type',
        'quantity',
        'serial_number',
        'assigned_to',
        'purchase_date',
        'cost',
        'description',
        'status',
    ];

    // In Asset.php
public function employee()
{
    return $this->belongsTo(Employee::class, 'assigned_to');
}

}
