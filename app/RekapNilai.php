<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RekapNilai extends Model
{
    protected $fillable = ['user_id','paket_id','tema_id','nilai_mtk','nilai_ipu','nilai_bing','nilai_bi','kumpul_mtk','kumpul_ipu','kumpul_bing','kumpul_bi','total_nilai'];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function tema(){
        return $this->belongsTo('App\Tema', 'tema_id');
    }
    public function paket(){
        return $this->belongsTo('App\PaketSoal', 'paket_id');
    }
}
