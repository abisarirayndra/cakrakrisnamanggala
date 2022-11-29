<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RekapTniPolri extends Model
{
    protected $table = 'dn_rekap_tnipolri';
    protected $fillable = [
        'kode_arsip',
        'dn_paket_id',
        'pelajar_id',
        'bin',
        'bing',
        'mtk',
        'ipu_wk',
        'nilai_bin',
        'nilai_bing',
        'nilai_mtk',
        'nilai_ipu',
        'total_nilai',
    ];
}
