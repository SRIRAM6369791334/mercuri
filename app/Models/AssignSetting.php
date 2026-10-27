<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'mhe_special_user_id',
        'racking_user_id',
        'pallet_user_id',
    ];

    public function mheUser()
    {
        return $this->belongsTo(User::class, 'mhe_special_user_id');
    }

    public function rackingUser()
    {
        return $this->belongsTo(User::class, 'racking_user_id');
    }

    public function palletUser()
    {
        return $this->belongsTo(User::class, 'pallet_user_id');
    }
}
