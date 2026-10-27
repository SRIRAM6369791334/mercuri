<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationStatus extends Model
{
    use HasFactory;

    protected $table = 'quotation_status';

    // Specify the columns that are mass assignable
    protected $fillable = ['name'];

    // Define the timestamps if needed (Laravel does this automatically)
    public $timestamps = true;
}
 