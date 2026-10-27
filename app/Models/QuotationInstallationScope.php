<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationInstallationScope extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sort_order', 'is_active'];
}
