<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArsipNilai extends Model
{
    protected $table = 'dn_arsipnilai';

    protected $fillable = [
        'kode',
        'dn_tes_id',
        'tanggal',
        'pendidik_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function tes(): BelongsTo
    {
        return $this->belongsTo(TesDinas::class, 'dn_tes_id');
    }

    public function pendidik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pendidik_id');
    }
}
