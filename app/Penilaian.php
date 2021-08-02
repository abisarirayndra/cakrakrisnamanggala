<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table = 'dn_penilaians';
    protected $fillable = [
        'dn_tes_id',
        'pelajar_id',
        'nilai',
        'akumulasi',
        'status',
    ];
}
