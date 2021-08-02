<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JawabanGandaPoinDinas extends Model
{
    protected $table = 'dn_jawabangandapoin';
    protected $fillable = [
        'pelajar_id',
        'dn_soalgandapoin_id',
        'jawaban',
        'nilai',
        'status'
    ];
}
