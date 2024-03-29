<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama', 'email', 'password','role_id','whatsapp','nomor_registrasi','kelas_id','token_reset'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isSuper(){
        if($this->role_id == 1){
            return true;
        }
        return false;
    }
    public function isAdmin(){
        if($this->role_id == 2){
            return true;
        }
        return false;
    }
    public function isPengajar(){
        if($this->role_id == 3){
            return true;
        }
        return false;
    }
    public function isPelajar(){
        if($this->role_id == 4){
            return true;
        }
        return false;
    }
    public function isPendaftar(){
        if($this->role_id == 5){
            return true;
        }
        return false;
    }
    public function isStafAdmin(){
        if($this->role_id == 7){
            return true;
        }
        return false;
    }
}
