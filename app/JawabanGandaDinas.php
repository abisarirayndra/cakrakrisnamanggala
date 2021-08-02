<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JawabanGandaDinas extends Model
{
    protected $table = 'dn_jawabanganda';
    protected $fillable = [
        'pelajar_id',
        'dn_soalganda_id',
        'jawaban',
        'nilai',
        'status'
    ];
}
