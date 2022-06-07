<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'adm_jadwal';
    protected $fillable = [
        'staf_id','mapel_id','pendidik_id','kelas_id','mulai','selesai'
    ];
}
