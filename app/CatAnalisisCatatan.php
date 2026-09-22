<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatAnalisisCatatan extends Model
{
    protected $table = 'cat_analisis_catatan';

    protected $fillable = [
        'jadwal_id',
        'pelajar_id',
        'pendidik_id',
        'catatan',
    ];

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(CatJadwal::class, 'jadwal_id');
    }

    public function pelajar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelajar_id');
    }

    public function pendidik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pendidik_id');
    }
}
