<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tema;

class Soal extends Model
{
    
    protected $fillable = [
        'tema_id', 'nomor_soal', 'soal', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e','kunci','skor'
    ];

    public function tema(){
        return $this->belongsTo('App\Tema', 'tema_id');
    }
}
