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
}
