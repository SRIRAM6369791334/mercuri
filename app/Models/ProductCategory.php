<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    public const SEGMENT_TYPES = [
        'mhe' => 'MHE',
        'rack' => 'RACK',
        'pallet' => 'Pallet',
        'spares' => 'Spares',
    ];

    public const WORKFLOW_TYPES = [
        'special' => 'Special',
        'standard' => 'Standard',
    ];

    protected $table = "product_categories_tb";
    protected $fillable = [
        'category_name',
        'type',
        'sub_category',
        'dispatch_time',
        'product_keyword',
        'keyword_dispatch_time',
    ];

    public function subCategories()
    {
        return $this->hasMany(ProductSubCategory::class, 'cat_id');
    }
}
