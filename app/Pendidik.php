<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendidik extends Model
{
    protected $table = 'adm_pendidik';

    protected $fillable = [
        'pendidik_id',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'nip',
        'alamat',
        'mapel_id',
        'wa',
        'ibu',
        'foto',
        'cv',
        'status_dapodik',
        'markas_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pendidik_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function markas(): BelongsTo
    {
        return $this->belongsTo(Markas::class, 'markas_id');
    }
}
