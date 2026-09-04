<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DurasiTes extends Model
{
    protected $table = 'dn_durasites';

    protected $fillable = [
        'dn_tes_id',
        'pelajar_id',
        'durasi',
    ];

    public function tes(): BelongsTo
    {
        return $this->belongsTo(TesDinas::class, 'dn_tes_id');
    }

    public function pelajar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelajar_id');
    }
}
