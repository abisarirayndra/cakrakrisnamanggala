<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DurasiTes extends Model
{
    protected $table = 'dn_durasites';
    protected $fillable = [
        'dn_tes_id',
        'pelajar_id',
        'durasi',
    ];
}
