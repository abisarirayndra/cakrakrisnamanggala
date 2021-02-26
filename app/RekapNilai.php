<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RekapNilai extends Model
{
    protected $fillable = ['user_id','tema_id','total_nilai'];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function temas(){
        return $this->belongsTo('App\Tema', 'tema_id');
    }
}
