<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Helpers\IndianNumberHelper;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'id',
        'productname',
        'productmodel',
        'hsn',
        'uom',
        'gst',
        'rate',
        'allo_dis',
        'partno',
        'productdes',
        'dispatch_time',
        'Productcategory',
        'quantity',
        'sku',
        'sub_cat_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = self::generateSku($product);
            }
        });
    }

    public static function generateSku($product)
    {
        // Fetch category
        $category = $product->category()->first();
        $categoryCode = $category ? strtoupper(substr($category->category_name, 0, 2)) : 'XX'; // Ensure correct field

        // Get product short code
        $productCode = strtoupper(substr($product->productname, 0, 4)); // Use correct field

        // Generate SKU with random number
        return $categoryCode . '-' . $productCode . '-' . rand(1000, 9999);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'Productcategory', 'id');
    }

    public function subcategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'sub_cat_id', 'id');
    }

    public function uomData()
    {
        return $this->belongsTo(UnitOfMeasurement::class, 'uom', 'id');
    }

    public function stock()
    {
        return $this->hasOne(ProductStock::class);
    }

    public function productStock()
    {
        return $this->hasOne(ProductStock::class, 'product_id', 'id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
    public function specifications()
{
    return $this->hasMany(ProductSpecification::class, 'productid');
}

    public function addStock($productId, $vendorId, $quantity, $remarks = 'Stock purchased', $peId = null)
    {

        StockMovement::create([
            'product_id' => $productId,
            'vendor_id' => $vendorId,
            'type' => 'purchase',
            'quantity' => $quantity,
            'remarks' => $remarks,
            'pe_id' => $peId
        ]);

        ProductStock::updateOrCreate(
            ['product_id' => $productId],
            ['available_stock' => DB::raw("available_stock + $quantity"), 'total_stock' => DB::raw("total_stock + $quantity")]
        );
    }

    // when the purchase entry editing below function will be used
    public function updateStock($productId, $vendorId, $quantity, $remarks = 'Stock purchased', $peId = null, $oldProductId = null, $oldQty = null)
    {
        StockMovement::where('pe_id', $peId)->update([
            'product_id' => $productId,
            'vendor_id' => $vendorId,
            'type' => 'purchase',
            'quantity' => $quantity,
            'remarks' => $remarks,
        ]);

        $oldQty = (float) ($oldQty ?? 0);
        $quantity = (float) ($quantity ?? 0);

        if ($oldProductId && $oldProductId != $productId) {
            ProductStock::where('product_id', $oldProductId)
                ->decrement('available_stock', $oldQty);
            ProductStock::where('product_id', $oldProductId)
                ->decrement('total_stock', $oldQty);

            ProductStock::updateOrCreate(
                ['product_id' => $productId],
                ['available_stock' => DB::raw("available_stock + $quantity"), 'total_stock' => DB::raw("total_stock + $quantity")]
            );
        } else {
            $diff = $quantity - $oldQty;
            if ($diff != 0) {
                ProductStock::updateOrCreate(
                    ['product_id' => $productId],
                    ['available_stock' => DB::raw("available_stock + ($diff)"), 'total_stock' => DB::raw("total_stock + ($diff)")]
                );
            }
        }
    }

    public function reduceStock($productId, $quantity, $remarks = 'Stock used')
    {
        StockMovement::create([
            'product_id' => $productId,
            'type' => 'sale',
            'quantity' => -$quantity,
            'remarks' => $remarks
        ]);

        ProductStock::where('product_id', $productId)
            ->decrement('available_stock', $quantity);
    }

    public function deleteStock($productId, $quantity, $remarks = 'PE Delete')
    {
        $quantity = (float) $quantity;
        StockMovement::create([
            'product_id' => $productId,
            'type' => 'sale',
            'quantity' => -$quantity,
            'remarks' => $remarks
        ]);

        ProductStock::where('product_id', $productId)
            ->decrement('available_stock', $quantity);
        ProductStock::where('product_id', $productId)
            ->decrement('total_stock', $quantity);
    }

    public function adjustStock($productId, $quantity, $remarks = 'Stock adjusted')
    {
        StockMovement::create([
            'product_id' => $productId,
            'type' => 'adjustment',
            'quantity' => $quantity,
            'remarks' => $remarks
        ]);

        ProductStock::where('product_id', $productId)
            ->decrement('available_stock', $quantity);
    }

    public function setRateAttribute($value): void
    {
        if (is_null($value) || $value === '') {
            $this->attributes['rate'] = 0.00;
            return;
        }

        if (is_string($value)) {
            // Strip leading non-digit characters (e.g. "Rs. ", "₹ ", "$ ", "INR ")
            $clean = preg_replace('/^[^\d]*/u', '', $value);
            $clean = str_replace([',', ' '], '', $clean);
            $clean = preg_replace('/[^\d.]/', '', $clean);
            $this->attributes['rate'] = (is_numeric($clean) && $clean !== '') ? (float) $clean : 0.00;
        } else {
            $this->attributes['rate'] = is_numeric($value) ? (float) $value : 0.00;
        }
    }

    public function getFormattedRateAttribute(): string
    {
        return IndianNumberHelper::formatIndianCurrency($this->rate ?? 0);
    }
}
