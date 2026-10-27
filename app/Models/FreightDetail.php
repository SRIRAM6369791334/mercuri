<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightDetail extends Model
{
    use HasFactory;

    public function category()
{
    return $this->belongsTo(\App\Models\FreightCategory::class, 'freight_cat_id');
}

public function subcategory()
{
    return $this->belongsTo(\App\Models\FreightSubCategory::class, 'freight_sub_cat_id');
}

}
