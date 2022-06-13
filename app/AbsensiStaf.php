<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbsensiStaf extends Model
{
   protected $table = 'adm_absensi_staf';
   protected $fillable = [
    'staf_id','datang','pulang','status','jurnal'
   ];
}
