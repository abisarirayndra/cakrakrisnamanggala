<?php

namespace App;

use App\Support\BankSoalBentuk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankPaket extends Model
{
    protected $table = 'cat_bank_paket';

    protected $fillable = [
        'pendidik_id',
        'mapel_id',
        'nama',
        'tipe',
        'bentuk',
    ];

    public function pendidik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pendidik_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function soal(): HasMany
    {
        return $this->hasMany(BankSoal::class, 'paket_id');
    }

    public function jadwal(): BelongsToMany
    {
        return $this->belongsToMany(CatJadwal::class, 'cat_jadwal_paket', 'bank_paket_id', 'jadwal_id')
            ->withPivot('urutan');
    }

    public function isMatematis(): bool
    {
        return ($this->bentuk ?: BankSoalBentuk::MATEMATIS) === BankSoalBentuk::MATEMATIS;
    }
}
