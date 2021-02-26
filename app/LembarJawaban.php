<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LembarJawaban extends Model
{
    protected $fillable = [
        'user_id','soal_id','jawaban','skor'
    ];
}
