<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;
    protected $fillable = [
        'content',
        'termtype',
        'term_approve',
        'whichterm',
        'term_remark',
        'product_id',
        'product_category_id',
        'product_sub_category_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productSubCategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'product_sub_category_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function frieghtDetails(){
        return $this->belongsTo(Freight::class, 'id', 'termscondition');
    }

    public function frieghtWarrantyDetails(){
        return $this->belongsTo(Freight::class, 'id', 'warranty');
    }

}
