<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TesDinas extends Model
{
    protected $table = 'dn_tes';
    protected $fillable = [
        'dn_paket_id',
        'mapel_id',
        'nilai_pokok',
        'mulai',
        'selesai',
        'pengajar_id',
    ];
}
