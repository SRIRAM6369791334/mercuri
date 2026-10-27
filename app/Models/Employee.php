<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    public function bank() {
    return $this->hasOne(EmployeeBank::class);
}

public function emergencyContacts() {
    return $this->hasMany(EmployeeEmergencyContact::class);
}

public function educations() {
    return $this->hasMany(EmployeeEducation::class);
}

public function experiences() {
    return $this->hasMany(EmployeeExperience::class);
}

public function documents() {
    return $this->hasOne(EmployeeDocument::class);
}

// public function assets()
// {
//     return $this->belongsTo(Asset::class);
// }
public function assets()
{
    return $this->hasMany(EmployeeAsset::class, 'employee_id');
}


}
