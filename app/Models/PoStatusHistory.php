<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'po_status_history';

    protected $fillable = [
        'po_no',
        'from_status',
        'to_status',
        'action',
        'user_id',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
