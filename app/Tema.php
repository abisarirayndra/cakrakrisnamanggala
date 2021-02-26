<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Kelas;
use App\User;

class Tema extends Model
{
    protected $fillable = ['tema','jumlah_soal','paket_id','mulai','tenggat','kelas_id','pengajar_id','status','jenis'];

    public function kelas(){
        return $this->belongsTo('App\Kelas', 'kelas_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'pengajar_id');
    }

    
}
