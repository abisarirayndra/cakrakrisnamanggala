<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbsensiPendidik extends Model
{
   protected $table = 'adm_absensi_pendidik';
   protected $fillable = [
    'jadwal_id','pendidik_id','datang','pulang','status','jurnal'
   ];
}
