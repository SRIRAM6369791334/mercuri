<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'punch_in_time',
        'punch_in_image',
        'punch_in_location',
        'punch_out_time',
        'punch_out_image',
        'punch_out_location',
        'total_work_duration',
    ];

    // Relationship: Attendance belongs to Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
