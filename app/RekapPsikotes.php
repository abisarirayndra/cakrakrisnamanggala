<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RekapPsikotes extends Model
{
    protected $table = 'dn_rekap_psikotes';
    protected $fillable = [
        'dn_paket_id',
        'pelajar_id',
        'verbal',
        'numerik',
        'figural',
        'total_nilai',
    ];
}
