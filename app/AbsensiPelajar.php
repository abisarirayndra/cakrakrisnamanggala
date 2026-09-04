<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiPelajar extends Model
{
    protected $table = 'adm_absensi_pelajar';

    protected $fillable = [
        'jadwal_id',
        'pelajar_id',
        'datang',
        'pulang',
        'status',
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

    public function pelajar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelajar_id');
    }
}
