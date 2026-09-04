<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanGandaDinas extends Model
{
    protected $table = 'dn_jawabanganda';

    protected $fillable = [
        'pelajar_id',
        'dn_soalganda_id',
        'jawaban',
        'nilai',
        'status',
    ];

    public function pelajar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelajar_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalDinasGanda::class, 'dn_soalganda_id');
    }
}
