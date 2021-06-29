<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoalDinasEssay extends Model
{
    protected $table = 'dn_soalessay';
    protected $fillable = [
        'dn_tes_id','soal'
    ];
}
