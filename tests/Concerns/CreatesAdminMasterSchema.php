<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreatesAdminMasterSchema
{
    protected function setUpAdminMasterSchema(): void
    {
        Schema::dropIfExists('adm_absensi_pendidik');
        Schema::dropIfExists('adm_absensi_pelajar');
        Schema::dropIfExists('adm_jadwal');
        Schema::dropIfExists('temas');
        Schema::dropIfExists('dn_tes');
        Schema::dropIfExists('admin_markas');
        Schema::dropIfExists('adm_pelajars');
        Schema::dropIfExists('adm_pendidik');
        Schema::dropIfExists('mapels');
        Schema::dropIfExists('kelas');
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

        Schema::create('kelas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama');
            $table->unsignedInteger('markas_id')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_markas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('markas_id');
            $table->timestamps();
            $table->unique(['user_id', 'markas_id']);
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

        Schema::create('mapels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mapel');
        });

        Schema::create('adm_pendidik', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pendidik_id');
            $table->unsignedInteger('mapel_id')->nullable();
            $table->unsignedInteger('markas_id')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik')->nullable();
            $table->string('nip')->nullable();
            $table->string('alamat')->nullable();
            $table->string('wa')->nullable();
            $table->string('ibu')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        Schema::create('dn_tes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mapel_id')->nullable();
        });

        Schema::create('temas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mapel_id')->nullable();
        });

        Schema::create('adm_jadwal', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('staf_id')->nullable();
            $table->unsignedInteger('mapel_id')->nullable();
            $table->unsignedInteger('pendidik_id')->nullable();
            $table->unsignedInteger('kelas_id')->nullable();
            $table->dateTime('mulai')->nullable();
            $table->dateTime('selesai')->nullable();
            $table->timestamps();
        });

        Schema::create('adm_absensi_pelajar', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('pelajar_id')->nullable();
            $table->dateTime('datang')->nullable();
            $table->dateTime('pulang')->nullable();
            $table->integer('status')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('adm_absensi_pendidik', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('pendidik_id')->nullable();
            $table->dateTime('datang')->nullable();
            $table->dateTime('pulang')->nullable();
            $table->integer('status')->nullable();
            $table->string('keterangan')->nullable();
            $table->text('jurnal')->nullable();
            $table->timestamps();
        });
    }
}
