<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statelist extends Model
{
    use HasFactory;

    protected $table = 'state_list';
    protected $guarded = [];
}
