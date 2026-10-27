<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendor_details_tb';

    protected $dates = ['deleted_at'];

    public function stateData(){
        return $this->belongsTo(Statelist::class, 'state_id', 'id');
    }

    public function cityData(){
        return $this->belongsTo(Citylist::class, 'city_id', 'id');
    }
}
