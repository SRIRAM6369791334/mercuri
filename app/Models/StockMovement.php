<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = "stock_movements_tb";

    protected $fillable = ['product_id', 'vendor_id', 'pe_id', 'type', 'quantity', 'remarks'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function vendors()
    {
        return $this->belongsTo(Vendor::class,"vendor_id","id");
    }
}
