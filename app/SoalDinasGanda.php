<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoalDinasGanda extends Model
{
    protected $table = 'dn_soalganda';
    protected $fillable = [
        'dn_tes_id','soal','opsi_a','opsi_b','opsi_c','opsi_d','opsi_e','kunci'
    ];
}
