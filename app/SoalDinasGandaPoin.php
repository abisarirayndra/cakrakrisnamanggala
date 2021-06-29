<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoalDinasGandaPoin extends Model
{
    protected $table = 'dn_soalgandapoin';
    protected $fillable = [
        'dn_tes_id',
        'soal',
        'opsi_a',
        'poin_a',
        'opsi_b',
        'poin_b',
        'opsi_c',
        'poin_c',
        'opsi_d',
        'poin_d',
        'opsi_e',
        'poin_e',
        
    ];
}
