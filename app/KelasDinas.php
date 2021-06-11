<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KelasDinas extends Model
{
    protected $table = 'dn_kelas';
    protected $fillable = ['dn_paket_id','kelas_id'];
}
