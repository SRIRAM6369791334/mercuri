<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    protected $fillable = ['product_category_id', 'product_id', 'name', 'part_no', 'hsn', 'price', 'tax_percent', 'status'];

    public static function availableForProduct(Product $product): Collection
    {
        $columns = ['id', 'name', 'part_no', 'hsn', 'price', 'tax_percent'];
        $directAccessories = static::where('product_id', $product->id)
            ->where('status', 'active')
            ->get($columns);

        if ($directAccessories->isNotEmpty()) {
            return $directAccessories;
        }

        $categoryId = $product->Productcategory ?: ($product->productcategory ?? null);
        if (!$categoryId) {
            return new Collection();
        }

        return static::where('product_category_id', $categoryId)
            ->where('status', 'active')
            ->get($columns)
            ->unique('name')
            ->values();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
