<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TesDinas extends Model
{
    protected $table = 'dn_tes';

    protected $fillable = [
        'dn_paket_id',
        'mapel_id',
        'nilai_pokok',
        'mulai',
        'selesai',
        'durasi',
        'pengajar_id',
        'token',
    ];

    protected function casts(): array
    {
        return [
            'mulai' => 'datetime',
            'selesai' => 'datetime',
        ];
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketDinas::class, 'dn_paket_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengajar_id');
    }

    public function soalGanda(): HasMany
    {
        return $this->hasMany(SoalDinasGanda::class, 'dn_tes_id');
    }

    public function soalGandaPoin(): HasMany
    {
        return $this->hasMany(SoalDinasGandaPoin::class, 'dn_tes_id');
    }

    public function soalEssay(): HasMany
    {
        return $this->hasMany(SoalDinasEssay::class, 'dn_tes_id');
    }
}
