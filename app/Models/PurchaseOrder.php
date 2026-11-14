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
        'hide_brand_name',
        'mercuriis_branch',
        'hsn_code',
        'part_no',
        'advance_percentage',
        'advance_amount',
        'advance_status',
        'sir_approved_by',
        'sir_approved_at',
        'sir_remark',
        'finance_released_by',
        'finance_released_at',
        'finance_utr_no',
        'finance_payment_mode',
        'finance_remark',
        'calculated_due_date',
        'supplier_work_order_no',
        'supplier_dispatch_date',
        'technical_specification',
        'additional_file',
        'production_milestone',
        'supplier_address_id',
        'customer_site_id',
        'dispatch_reminder_sent_at',
        'po_date',
        'gst_percentage',
        'overall_discount_percentage',
        'overall_discount_amount',
        'taxable_amount',
        'committed_dispatch_date',
        'actual_dispatch_date',
        'lifecycle_status',
    ];

    protected $dates = ['deleted_at'];

    public function supplierAddress(){
        return $this->belongsTo(VendorAddress::class, 'supplier_address_id', 'id')->withTrashed();
    }

    public function customerSite(){
        return $this->belongsTo(CustomerAddress::class, 'customer_site_id', 'id');
    }

    public function statusHistories(){
        return $this->hasMany(PoStatusHistory::class, 'po_no', 'po_no')->orderBy('created_at', 'desc');
    }

    public function accessories()
    {
        return $this->hasMany(PurchaseOrderAccessory::class, 'purchase_order_line_id');
    }

    public function getGrandTotalAttribute()
    {
        if (!empty($this->po_no)) {
            $lines = PurchaseOrder::where('po_no', $this->po_no)->get();
            if ($lines->count() > 1) {
                $sumLineTotal = $lines->sum('total_amount');
                return round($sumLineTotal + (float)$this->freight_charges + (float)$this->unloading_charges + (float)$this->roundoff, 2);
            }
        }
        return round((float)$this->total_amount + (float)$this->freight_charges + (float)$this->unloading_charges + (float)$this->roundoff, 2);
    }

    public function setGrandTotalAttribute($value)
    {
        $this->attributes['total_amount'] = $value;
    }

    public function getAdvanceUtrNumberAttribute()
    {
        return $this->finance_utr_no;
    }

    public function setAdvanceUtrNumberAttribute($value)
    {
        $this->attributes['finance_utr_no'] = $value;
    }

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

    public function sirApprover(){
        return $this->belongsTo(User::class, 'sir_approved_by', 'id');
    }

    public function financeUser(){
        return $this->belongsTo(User::class, 'finance_released_by', 'id');
    }
}
