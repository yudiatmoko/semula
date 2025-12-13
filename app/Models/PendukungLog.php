<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendukungLog extends Model
{
    protected $fillable = [
        'admin_id',
        'aksi',
        'perubahan',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
