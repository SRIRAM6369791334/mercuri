<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = ['id_lead', 'quota_id', 'basic_total', 'allow_dis', 'req_dis', 'reqdis_amt','id_customer', 'subtotal', 'tax_total','grand_total','products', 'categorys', 'ga_number', 'quantitys', 'dis_app','quto_app','disremark','revised_discount',
    'freight_approval',
    'same_as_billing_address',

    'billing_address',
    'shipping_address',
    'billing_country',
    'shipping_country',
    'billing_state',
    'shipping_state',
    'billing_city',
    'shipping_city',
    'billing_pincode',
    'shipping_pincode',
    'billing_gst',
    'shipping_gst',

    'default_installation_charge',
    'installation_charge',
    'show_installation_charge',
    'include_installation_in_basic',
    'default_freight_charge',
    'freight_charge',
    'show_freight_charge',
    'include_freight_in_basic',
    'additional_warranty_months',
    'additional_warranty_percentage',
    'additional_warranty_charge',
    'include_warranty_in_basic',
    'charge_approval_status',
    'charge_approved_by',
    'charge_approved_at',
    'sent_to_customer',
    'sent_to_customer_at',
    'sent_to_customer_by',
    'revision_major',
    'revision_minor',
    ];


    public function lead()
    {
        return $this->belongsTo(Lead::class, 'id_lead');
    }

    public function freight()
    {
        return $this->hasOne(Freight::class, 'quotationno');
    }
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($quotation) {
    //         $quotation->quota_id = 'MSE/' . date('Y') . '/' . str_pad($quotation->id, 4, '0', STR_PAD_LEFT);
    //         $quotation->save();
    //     });
    // }
public function orfDetails()
    {
        return $this->hasOne(Orf::class, 'quoproid', 'id');
    }

    public function productDetails()
    {
        return $this->belongsTo(Product::class, 'product', 'id');
    }

    public function termChangeRequests()
    {
        return $this->hasMany(QuotationTermChangeRequest::class, 'quotation_id');
    }

    public function chargeHistories()
    {
        return $this->hasMany(QuotationChargeApprovalHistory::class, 'quotation_id');
    }

    public function quotationProducts()
    {
        return $this->hasMany(QuotationProduct::class, 'quotationno', 'quota_id');
    }

    public function getEffectiveRequestedDiscountAttribute()
    {
        if (!is_null($this->req_dis) && (float) $this->req_dis > 0) {
            return (float) $this->req_dis;
        }
        return (float) ($this->quotationProducts->max('requestdis') ?? 0);
    }
}
