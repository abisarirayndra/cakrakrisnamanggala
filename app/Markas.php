<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Markas extends Model
{
    protected $table = 'adm_markas';

    protected $fillable = ['markas'];

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'admin_markas', 'markas_id', 'user_id');
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'markas_id');
    }

    public function pelajar(): HasMany
    {
        return $this->hasMany(Pelajar::class, 'markas_id');
    }

    public function pendidik(): HasMany
    {
        return $this->hasMany(Pendidik::class, 'markas_id');
    }

    public function whatsappAdminUrl(): ?string
    {
        return match ($this->markas) {
            'Banyuwangi' => 'https://wa.link/avyrxr',
            'Genteng' => 'https://wa.link/kvov6u',
            'Jember' => 'https://wa.link/dw5alz',
            'PDM - Smadatara' => 'https://wa.link/heeujh',
            'PDM - Smanda' => 'https://wa.link/pqljun',
            default => null,
        };
    }

    public static function whatsappAdminForName(?string $nama): ?string
    {
        $markas = new self();
        $markas->markas = $nama;

        return $markas->whatsappAdminUrl();
    }
}
