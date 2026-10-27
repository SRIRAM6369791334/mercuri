<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationChargeApprovalHistory extends Model
{
    protected $fillable = ['quotation_id', 'action', 'status', 'default_installation_charge', 'installation_charge', 'default_freight_charge', 'freight_charge', 'acted_by', 'remarks'];

    public function actor()
    {
        return $this->belongsTo(User::class, 'acted_by');
    }
}
