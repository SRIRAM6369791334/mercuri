<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freight extends Model
{
    use HasFactory;
    protected $table = 'freight';

    protected $fillable = [
        'leadnumber',
        'quotationno',
        'termscondition',
        'dov',
        'dispatch_time',
        'freight',
        'exwork',
        'freightextra',
        'unloading',
        'remarks',
        'currency',
        'warranty',
        'installation',
        'agent',
        'amount',
        'freight_approve',
        'freight_detail_id',
        'freight_sub_category_id',
        'freight_category_id',
    ];

    public function quotationDetails(){
        return $this->belongsTo(QuotationProduct::class, 'quotationno', 'id');
    }

    public function freightDetail()
    {
        return $this->belongsTo(FreightDetail::class, 'freight_detail_id', 'id');
    }

    public function termsCondition()
    {
        return $this->belongsTo(Term::class, 'termscondition', 'id');
    }

    public function warrantyTerm()
    {
        return $this->belongsTo(Term::class, 'warranty');
    }

    public function freightCategory()
    {
        return $this->belongsTo(FreightCategory::class, 'freight_category_id');
    }

    public function freightSubCategory()
    {
        return $this->belongsTo(FreightSubCategory::class, 'freight_sub_category_id');
    }

    public function installationScope()
    {
        return $this->belongsTo(QuotationInstallationScope::class, 'installation');
    }

    public function unloadingOption()
    {
        return $this->belongsTo(QuotationUnloadingOption::class, 'unloading');
    }
}
