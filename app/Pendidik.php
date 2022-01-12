<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pendidik extends Model
{
    protected $table = 'adm_pendidik';
    protected $fillable = [
        'pendidik_id',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'nip',
        'alamat',
        'mapel_id',
        'wa',
        'ibu',
        'foto',
        'cv',
        'status_dapodik',
        'markas'
    ];
}
