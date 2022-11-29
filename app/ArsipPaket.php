<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArsipPaket extends Model
{
    protected $table = 'dn_arsippaket';
    protected $fillable = [
        'kode',
        'dn_paket_id',
        'tanggal',
    ];
}
