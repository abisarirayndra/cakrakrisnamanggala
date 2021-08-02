<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RekapDinas extends Model
{
    protected $table = 'dn_rekapdinas';
    protected $fillable = [
        'dn_paket_id',
        'pelajar_id',
        'twk',
        'tiu',
        'tkp',
        'total_nilai'
    ];
}
