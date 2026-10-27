<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holidaydb extends Model
{
    use HasFactory;
    protected $table = 'holiday_tb';
    public $timestamps = false;
}
