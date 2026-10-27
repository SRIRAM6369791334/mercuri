<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeExperience extends Model
{
    use HasFactory;

     protected $fillable = [
        'employee_id',
        'exp_start_date',
        'exp_end_date',
        'period',
        'role',
        'company',
        'empdesignation',
    ];
}
