<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RekapTniPolri extends Model
{
    protected $table = 'dn_rekap_tnipolri';
    protected $fillable = [
        'dn_paket_id',
        'pelajar_id',
        'bin',
        'bing',
        'mtk',
        'ipu_wk',
        'total_nilai'
    ];
}
