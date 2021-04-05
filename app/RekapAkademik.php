<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RekapAkademik extends Model
{
    protected $fillable = ['user_id','paket_id','nilai_mtk','nilai_ipu','nilai_bing','nilai_bi','nilai_akademik'];
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function paket(){
        return $this->belongsTo('App\PaketSoal', 'paket_id');
    }
}
