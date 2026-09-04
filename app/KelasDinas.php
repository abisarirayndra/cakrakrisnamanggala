<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelasDinas extends Model
{
    protected $table = 'dn_kelas';

    protected $fillable = ['dn_paket_id', 'kelas_id'];

    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketDinas::class, 'dn_paket_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
