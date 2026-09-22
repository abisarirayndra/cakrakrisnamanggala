<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreatesCatBankSchema
{
    protected function setUpCatBankSchema(): void
    {
        Schema::dropIfExists('cat_bank_opsi');
        Schema::dropIfExists('cat_bank_soal');
        Schema::dropIfExists('cat_bank_paket');

        Schema::create('cat_bank_paket', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pendidik_id');
            $table->unsignedInteger('mapel_id')->nullable();
            $table->string('nama');
            $table->string('tipe', 20);
            $table->string('bentuk', 20)->default('matematis');
            $table->timestamps();
        });

        Schema::create('cat_bank_soal', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('paket_id');
            $table->unsignedInteger('pendidik_id');
            $table->unsignedInteger('mapel_id')->nullable();
            $table->text('soal');
            $table->string('gambar')->nullable();
            $table->unsignedInteger('poin')->nullable();
            $table->string('kunci', 1)->nullable();
            $table->timestamps();
        });

        Schema::create('cat_bank_opsi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bank_soal_id');
            $table->string('kode', 1);
            $table->text('teks');
            $table->string('gambar')->nullable();
            $table->integer('poin')->default(0);
            $table->unsignedTinyInteger('urutan');
            $table->timestamps();
            $table->unique(['bank_soal_id', 'kode']);
        });

        Schema::dropIfExists('cat_jadwal_paket');
        Schema::dropIfExists('cat_jadwal_kelas');
        Schema::dropIfExists('cat_jadwal');

        Schema::create('cat_jadwal', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('admin_id');
            $table->string('nama');
            $table->dateTime('mulai');
            $table->dateTime('selesai');
            $table->string('token', 8)->unique();
            $table->timestamps();
        });

        Schema::create('cat_jadwal_paket', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('bank_paket_id');
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->unique(['jadwal_id', 'bank_paket_id']);
        });

        Schema::dropIfExists('cat_jadwal_jawaban');
        Schema::dropIfExists('cat_jadwal_sesi');

        Schema::create('cat_jadwal_sesi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('pelajar_id');
            $table->unsignedInteger('bank_paket_id');
            $table->string('status', 20)->default('berjalan');
            $table->integer('nilai')->nullable();
            $table->json('urutan_soal')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['jadwal_id', 'pelajar_id', 'bank_paket_id']);
        });

        Schema::create('cat_jadwal_jawaban', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sesi_id');
            $table->unsignedInteger('bank_soal_id');
            $table->string('kode', 1)->nullable();
            $table->integer('poin')->default(0);
            $table->timestamps();
            $table->unique(['sesi_id', 'bank_soal_id']);
        });

        Schema::dropIfExists('cat_analisis_catatan');
        Schema::create('cat_analisis_catatan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('pelajar_id');
            $table->unsignedInteger('pendidik_id');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['jadwal_id', 'pelajar_id', 'pendidik_id']);
        });
    }
}
