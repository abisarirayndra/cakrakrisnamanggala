<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapPsikotes extends Model
{
    protected $table = 'dn_rekap_psikotes';

    protected $fillable = [
        'kode_arsip',
        'dn_paket_id',
        'pelajar_id',
        'verbal',
        'numerik',
        'figural',
        'total_nilai',
    ];

    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketDinas::class, 'dn_paket_id');
    }

    public function pelajar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelajar_id');
    }
}
