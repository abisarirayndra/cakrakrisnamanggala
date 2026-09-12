<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jadwal extends Model
{
    protected $table = 'adm_jadwal';

    protected $fillable = [
        'staf_id',
        'mapel_id',
        'pendidik_id',
        'kelas_id',
        'mulai',
        'selesai',
    ];

    protected function casts(): array
    {
        return [
            'mulai' => 'datetime',
            'selesai' => 'datetime',
        ];
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function pendidik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pendidik_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function absensiPelajar(): HasMany
    {
        return $this->hasMany(AbsensiPelajar::class, 'jadwal_id');
    }

    public function absensiPendidik(): HasMany
    {
        return $this->hasMany(AbsensiPendidik::class, 'jadwal_id');
    }

    public function sudahAdaAbsensi(): bool
    {
        return $this->absensiPelajar()->exists() || $this->absensiPendidik()->exists();
    }
}
