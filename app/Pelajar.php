<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pelajar extends Model
{
    protected $table = 'adm_pelajars';
    protected $fillable = [
        'pelajar_id',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'sekolah',
        'wa',
        'wali',
        'foto',
        'markas_id',
        'nik',
        'nisn',
        'ibu',
        'wa_wali',
        'status_sekolah',
];
}
