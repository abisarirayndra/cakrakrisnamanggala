<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanGandaPoinDinas extends Model
{
    protected $table = 'dn_jawabangandapoin';

    protected $fillable = [
        'pelajar_id',
        'dn_soalgandapoin_id',
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
        return $this->belongsTo(SoalDinasGandaPoin::class, 'dn_soalgandapoin_id');
    }
}
