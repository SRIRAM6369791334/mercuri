<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "purchase_order_tb";

    protected $fillable = [
        'cat_id',  
        'po_no',  
        'orf_id',
        'quotation_id',
        'product_id',  
        'vendor_id',  
        'product_qty',  
        'unit_price',  
        'original_order_price',
        'product_total_price',  
        'original_total_price',
        'price_variance',
        'variance_percentage',
        'gst_amount',  
        'sub_total',  
        'total_amount',  
        'product_description',  
        'roundoff',
        'files',
        'drawing_attachment',
        'quote_attachments',
        'terms_condition_id',
        'lead_time',
        'payment_terms',
        'warranty_terms',
        'special_instructions',
        'shipping_type',
        'shipping_address',
        'shipping_gst',
        'supplier_billing_address',
        'supplier_gst',
        'discount_percentage',
        'discount_amount',
        'freight_charges',
        'unloading_charges',
        'installation_scope',
        'hide_brand_name'
    ];

    protected $dates = ['deleted_at'];

    public function categoryDetails(){
        return $this->belongsTo(ProductCategory::class, 'cat_id', 'id');
    }

    public function productDetails(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function vendorDetails(){
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function allVendorDetails(){
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id')->withTrashed();
    }

    public function termsConditionDetails(){
        return $this->belongsTo(Term::class, 'terms_condition_id', 'id');
    }

    public function productStock(){
        return $this->belongsTo(ProductStock::class, 'product_id', 'product_id');
    }

    public function orf(){
        return $this->belongsTo(Orf::class, 'orf_id', 'id');
    }

    public function quotation(){
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }
}
