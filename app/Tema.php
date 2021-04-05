<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Kelas;
use App\User;
use App\Mapel;

class Tema extends Model
{
    protected $fillable = ['judul_tes','mapel_id','jumlah_soal','paket_id','mulai','tenggat','kelas_id','pengajar_id','status','jenis'];

    public function kelas(){
        return $this->belongsTo('App\Kelas', 'kelas_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'pengajar_id');
    }

    public function mapel(){
        return $this->belongsTo('App\Mapel', 'mapel_id');
    }

    public function paket(){
        return $this->belongsTo('App\PaketSoal', 'paket_id');
    }

    
}
