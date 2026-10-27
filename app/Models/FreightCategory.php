<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightCategory extends Model
{
    use HasFactory;

    protected $table = "freight_category";

    protected $fillable = ['categoty', 'product_category_id'];
}
