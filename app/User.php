<?php

namespace App;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role_id',
        'is_super_admin',
        'whatsapp',
        'nomor_registrasi',
        'kelas_id',
        'token_reset',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_super_admin' => 'boolean',
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function pelajar(): HasOne
    {
        return $this->hasOne(Pelajar::class, 'pelajar_id');
    }

    public function pendidik(): HasOne
    {
        return $this->hasOne(Pendidik::class, 'pendidik_id');
    }

    public function markas(): BelongsToMany
    {
        return $this->belongsToMany(Markas::class, 'admin_markas', 'user_id', 'markas_id');
    }

    public function markasIds(): array
    {
        return array_map('intval', $this->markas()->pluck('adm_markas.id')->all());
    }

    public function isSuper(): bool
    {
        return (int) $this->role_id === 1;
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function isAdmin(): bool
    {
        return (int) $this->role_id === 2;
    }

    public function isPengajar(): bool
    {
        return (int) $this->role_id === 3;
    }

    public function isPelajar(): bool
    {
        return (int) $this->role_id === 4;
    }

    public function isPendaftar(): bool
    {
        return (int) $this->role_id === 5;
    }

    public function isStafAdmin(): bool
    {
        return $this->isAdmin();
    }

    public function dashboardRouteName(): ?string
    {
        return match ((int) $this->role_id) {
            2, 7 => 'admin.beranda',
            3 => 'pendidik.dinas.beranda',
            4 => 'pelajar.dinas.beranda',
            5 => 'pendaftar.profil',
            default => null,
        };
    }
}
