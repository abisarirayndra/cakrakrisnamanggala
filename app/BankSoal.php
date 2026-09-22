<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankSoal extends Model
{
    protected $table = 'cat_bank_soal';

    protected $fillable = [
        'paket_id',
        'pendidik_id',
        'mapel_id',
        'soal',
        'gambar',
        'poin',
        'kunci',
    ];

    public function paket(): BelongsTo
    {
        return $this->belongsTo(BankPaket::class, 'paket_id');
    }

    public function pendidik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pendidik_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function opsi(): HasMany
    {
        return $this->hasMany(BankOpsi::class, 'bank_soal_id')->orderBy('urutan');
    }
}
