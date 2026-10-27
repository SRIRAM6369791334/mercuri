<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $table = "products_stock_tb";

    protected $fillable = ['product_id', 'total_stock', 'available_stock', 'used_stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
