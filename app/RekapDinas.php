<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RekapDinas extends Model
{
    protected $table = 'dn_rekapdinas';
    protected $fillable = [
        'kode_arsip',
        'dn_paket_id',
        'pelajar_id',
        'nilai_twk',
        'nilai_tiu',
        'nilai_tkp',
        'twk',
        'tiu',
        'tkp',
        'total_nilai'
    ];
}
