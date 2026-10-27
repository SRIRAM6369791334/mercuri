<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

     protected $fillable = [
        'employee_id',
        'aadhaar_file',
        'aadhaar_expiry',
        'pan_file',
        'pan_expiry',
    ];
}
