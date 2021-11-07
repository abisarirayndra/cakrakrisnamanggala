<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pelajar extends Model
{
    protected $table = 'adm_pelajars';
    protected $fillable = ['user_id','kelas_id'];
}
