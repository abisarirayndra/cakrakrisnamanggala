<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaketDinas extends Model
{
    protected $table = 'dn_pakets';

    protected $fillable = [
        'nama_paket',
        'kelas',
        'status',
        'kategori',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas');
    }

    public function tes(): HasMany
    {
        return $this->hasMany(TesDinas::class, 'dn_paket_id');
    }

    public function kelasDinas(): HasMany
    {
        return $this->hasMany(KelasDinas::class, 'dn_paket_id');
    }
}
