<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignSpecification extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_id',
        'design_id',
        'specification','spec_name','spec_value'
    ];
}
