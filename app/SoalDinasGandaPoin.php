<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoalDinasGandaPoin extends Model
{
    protected $table = 'dn_soalgandapoin';

    protected $fillable = [
        'dn_tes_id',
        'nomor_soal',
        'soal',
        'opsi_a',
        'poin_a',
        'opsi_b',
        'poin_b',
        'opsi_c',
        'poin_c',
        'opsi_d',
        'poin_d',
        'opsi_e',
        'poin_e',
    ];

    public function tes(): BelongsTo
    {
        return $this->belongsTo(TesDinas::class, 'dn_tes_id');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(JawabanGandaPoinDinas::class, 'dn_soalgandapoin_id');
    }
}
