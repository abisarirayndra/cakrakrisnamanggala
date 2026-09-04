<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiPendidik extends Model
{
    protected $table = 'adm_absensi_pendidik';

    protected $fillable = [
        'jadwal_id',
        'pendidik_id',
        'datang',
        'pulang',
        'status',
        'jurnal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'datang' => 'datetime',
            'pulang' => 'datetime',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function pendidik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pendidik_id');
    }
}
