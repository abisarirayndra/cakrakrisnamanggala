<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoalDinasGanda extends Model
{
    protected $table = 'dn_soalganda';

    protected $fillable = [
        'dn_tes_id',
        'nomor_soal',
        'soal',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'kunci',
    ];

    public function tes(): BelongsTo
    {
        return $this->belongsTo(TesDinas::class, 'dn_tes_id');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(JawabanGandaDinas::class, 'dn_soalganda_id');
    }
}
