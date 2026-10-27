<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAsset extends Model
{
    use HasFactory;

     protected $table = 'employeeassets'; // optional if your table follows Laravel naming conventions

    protected $fillable = [
        'employee_id',
        'asset_id',
        'quantity',
        'assigned_date',
        'remarks',
        'status'
    ];

    // Relationship to Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Relationship to Asset
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
