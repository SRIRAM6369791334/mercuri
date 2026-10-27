<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;
    protected $fillable = ['idlead', 'idcustomer', 'product', 'category', 'ganumber', 'quantity', 'assigndesign', 'design_status', 'design_user', 'drawing_approved', 'drawing_remarks','quotation', 'drawing', 'design_input', 'return_status', 'return_remarks'];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'idlead');
    }
}
