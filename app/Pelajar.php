<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelajar extends Model
{
    protected $table = 'adm_pelajars';

    protected $fillable = [
        'pelajar_id',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'sekolah',
        'wa',
        'wali',
        'foto',
        'markas_id',
        'nik',
        'nisn',
        'ibu',
        'wa_wali',
        'status_sekolah',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelajar_id');
    }

    public function markas(): BelongsTo
    {
        return $this->belongsTo(Markas::class, 'markas_id');
    }
}
