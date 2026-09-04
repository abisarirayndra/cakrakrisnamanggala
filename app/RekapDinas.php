<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapDinas extends Model
{
    protected $table = 'dn_rekapdinas';

    protected $fillable = [
        'kode_arsip',
        'dn_paket_id',
        'pelajar_id',
        'nilai_twk',
        'nilai_tiu',
        'nilai_tkp',
        'twk',
        'tiu',
        'tkp',
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
