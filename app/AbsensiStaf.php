<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiStaf extends Model
{
    protected $table = 'adm_absensi_staf';

    protected $fillable = [
        'staf_id',
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

    public function staf(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staf_id');
    }
}
