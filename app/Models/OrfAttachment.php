<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrfAttachment extends Model
{
    use HasFactory;

    protected $table = 'orf_attachments';

    protected $fillable = [
        'orf_id',
        'type',       // attcuspo | sigdraw
        'file_path',
    ];
}
