<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_bank_soal', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pendidik_id');
            $table->unsignedInteger('mapel_id')->nullable();
            $table->string('tipe', 20);
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
            $table->integer('poin')->default(0);
            $table->unsignedTinyInteger('urutan');
            $table->timestamps();
            $table->unique(['bank_soal_id', 'kode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_bank_opsi');
        Schema::dropIfExists('cat_bank_soal');
    }
};
