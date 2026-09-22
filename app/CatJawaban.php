<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatJawaban extends Model
{
    protected $table = 'cat_jadwal_jawaban';

    protected $fillable = [
        'sesi_id',
        'bank_soal_id',
        'kode',
        'poin',
    ];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(CatSesi::class, 'sesi_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }
}
