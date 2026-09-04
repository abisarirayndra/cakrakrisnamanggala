<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tema extends Model
{
    protected $fillable = [
        'judul_tes',
        'mapel_id',
        'jumlah_soal',
        'paket_id',
        'mulai',
        'tenggat',
        'kelas_id',
        'pengajar_id',
        'status',
        'jenis',
    ];

    protected function casts(): array
    {
        return [
            'mulai' => 'datetime',
            'tenggat' => 'datetime',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengajar_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketSoal::class, 'paket_id');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'tema_id');
    }
}
