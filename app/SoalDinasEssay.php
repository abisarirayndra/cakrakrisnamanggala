<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoalDinasEssay extends Model
{
    protected $table = 'dn_soalessay';

    protected $fillable = [
        'dn_tes_id',
        'soal',
        'nomor_soal',
    ];

    public function tes(): BelongsTo
    {
        return $this->belongsTo(TesDinas::class, 'dn_tes_id');
    }
}
