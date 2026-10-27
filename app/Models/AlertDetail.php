<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertDetail extends Model
{
    use HasFactory;

    protected $table = "alert_details_tb";

    protected $fillable = ['type', 'alert_detail'];
}
