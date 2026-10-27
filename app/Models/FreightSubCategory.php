<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightSubCategory extends Model
{
    use HasFactory;

    protected $table = "freight_sub_category";

    protected $fillable = ['freight_cat_id', 'sub_category', 'product_category_id'];
}
