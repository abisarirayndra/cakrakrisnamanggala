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

    public static function buktiPendaftaran(int $id): self
    {
        return static::query()
            ->select(
                'adm_markas.markas',
                'adm_pelajars.id',
                'adm_pelajars.nik',
                'adm_pelajars.nisn',
                'adm_pelajars.tempat_lahir',
                'adm_pelajars.tanggal_lahir',
                'adm_pelajars.alamat',
                'adm_pelajars.sekolah',
                'adm_pelajars.status_sekolah',
                'adm_pelajars.wa',
                'adm_pelajars.wali',
                'adm_pelajars.wa_wali',
                'adm_pelajars.ibu',
                'adm_pelajars.created_at',
                'adm_pelajars.foto',
                'users.nama',
                'users.email'
            )
            ->join('adm_markas', 'adm_markas.id', '=', 'adm_pelajars.markas_id')
            ->join('users', 'users.id', '=', 'adm_pelajars.pelajar_id')
            ->where('adm_pelajars.id', $id)
            ->firstOrFail();
    }

    public function statusSekolahLabel(): string
    {
        return (int) $this->status_sekolah === 1 ? 'Lulus' : 'Belum Lulus';
    }
}
