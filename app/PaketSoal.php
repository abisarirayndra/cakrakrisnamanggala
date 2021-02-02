<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Kelas;

class PaketSoal extends Model
{
    protected $fillable = [
        'nama_paket',
        'user_id',
        'kelas_id',
        'status',
    ];

    public function kelas(){
        return $this->belongsTo('App\Kelas', 'kelas_id');
    }
}
