<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapAkademik extends Model
{
    protected $fillable = [
        'user_id',
        'paket_id',
        'nilai_mtk',
        'nilai_ipu',
        'nilai_bing',
        'nilai_bi',
        'nilai_akademik',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketSoal::class, 'paket_id');
    }
}
