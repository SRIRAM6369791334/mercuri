<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAdvance extends Model
{
    use HasFactory;
     protected $fillable = ['employee_id', 'amount', 'reason', 'advance_date', 'status'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
