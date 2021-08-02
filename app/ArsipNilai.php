<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArsipNilai extends Model
{
    protected $table = 'dn_arsipnilai';
    protected $fillable = [
        'kode',
        'dn_tes_id',
        'tanggal',
        'pendidik_id',
    ];
}
