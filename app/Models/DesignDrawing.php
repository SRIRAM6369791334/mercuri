<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignDrawing extends Model
{
    use HasFactory;
    protected $fillable = [
        'design_id',
        'file_path',
        'drawing_app',
    'drawing_remarks',
    'sales_attach',
    ];
}
