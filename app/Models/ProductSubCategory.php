<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubCategory extends Model
{
    use HasFactory;

    public function segmentDetails(){
        return $this->belongsTo(ProductCategory::class, 'cat_id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    protected $fillable = [
        'cat_id',
        'subcategory_name',
        'product_id',
        'terms',
        'quotation_image',
    ];
}
