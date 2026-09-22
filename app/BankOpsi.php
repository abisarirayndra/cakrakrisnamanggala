<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankOpsi extends Model
{
    protected $table = 'cat_bank_opsi';

    protected $fillable = [
        'bank_soal_id',
        'kode',
        'teks',
        'gambar',
        'poin',
        'urutan',
    ];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }
}
