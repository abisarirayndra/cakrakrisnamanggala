<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapNilai extends Model
{
    protected $fillable = [
        'user_id',
        'paket_id',
        'tema_id',
        'nilai_mtk',
        'nilai_ipu',
        'nilai_bing',
        'nilai_bi',
        'kumpul_mtk',
        'kumpul_ipu',
        'kumpul_bing',
        'kumpul_bi',
        'total_nilai',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tema(): BelongsTo
    {
        return $this->belongsTo(Tema::class, 'tema_id');
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketSoal::class, 'paket_id');
    }
}
