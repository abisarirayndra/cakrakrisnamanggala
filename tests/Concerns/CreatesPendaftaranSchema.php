<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreatesPendaftaranSchema
{
    protected function setUpPendaftaranSchema(): void
    {
        Schema::dropIfExists('adm_pelajars');
        Schema::dropIfExists('adm_markas');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama');
            $table->string('nomor_registrasi')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedInteger('role_id')->nullable();
            $table->boolean('is_super_admin')->default(false);
            $table->string('whatsapp')->nullable();
            $table->unsignedInteger('kelas_id')->nullable();
            $table->string('token_reset')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('adm_markas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('markas');
            $table->timestamps();
        });

        Schema::create('adm_pelajars', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pelajar_id');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->string('sekolah')->nullable();
            $table->integer('status_sekolah')->nullable();
            $table->string('wa')->nullable();
            $table->string('wali')->nullable();
            $table->string('foto')->nullable();
            $table->unsignedInteger('markas_id')->nullable();
            $table->string('nik')->nullable();
            $table->string('nisn')->nullable();
            $table->string('ibu')->nullable();
            $table->string('wa_wali')->nullable();
            $table->timestamps();
        });
    }
}
