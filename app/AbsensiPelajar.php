<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbsensiPelajar extends Model
{
    protected $table = 'adm_absensi_pelajar';
    protected $fillable = [
        'jadwal_id','pelajar_id','datang','pulang','status','keterangan'
    ];
}
