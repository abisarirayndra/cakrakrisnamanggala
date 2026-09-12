<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'markas_id',
    ];

    public function markas(): BelongsTo
    {
        return $this->belongsTo(Markas::class, 'markas_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'kelas_id');
    }
}
