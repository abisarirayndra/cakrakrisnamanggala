<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArsipPaket extends Model
{
    protected $table = 'dn_arsippaket';

    protected $fillable = [
        'kode',
        'dn_paket_id',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketDinas::class, 'dn_paket_id');
    }
}
