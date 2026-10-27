<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignSpecificationMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'type',
        'spec_name'
    ];

    public function category()
{
    return $this->belongsTo(ProductCategory::class, 'category_id');
}

}
