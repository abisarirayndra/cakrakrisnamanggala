<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Kelas;

class PaketDinas extends Model
{
    protected $table = 'dn_pakets';
    protected $fillable = [
        'nama_paket',
        'kelas'
    ];

    public function kelas(){
        return $this->belongsTo('App\Kelas', 'kelas');
    }
}
