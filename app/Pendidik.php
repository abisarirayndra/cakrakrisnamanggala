<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendidik extends Model
{
    public const DEFAULT_PASSWORD = 'pendidik123';

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

    public function namaMarkas(): ?string
    {
        $related = $this->relations['markas'] ?? null;

        if ($related instanceof Markas) {
            return $related->markas;
        }

        if ($this->markas_id === null) {
            return null;
        }

        return Markas::query()->whereKey($this->markas_id)->value('markas');
    }
}
