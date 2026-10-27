<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeBank extends Model
{
    use HasFactory;

     protected $fillable = [
        'employee_id',
        'bankname',
        'branchname',
        'accholdername',
        'acc_number',
        'acc_no',
        'ifsc_code',
    ];
}
