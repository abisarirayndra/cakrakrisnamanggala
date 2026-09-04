<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mapel extends Model
{
    public function tes(): HasMany
    {
        return $this->hasMany(TesDinas::class, 'mapel_id');
    }

    public function tema(): HasMany
    {
        return $this->hasMany(Tema::class, 'mapel_id');
    }

    public function pendidik(): HasMany
    {
        return $this->hasMany(Pendidik::class, 'mapel_id');
    }
}
