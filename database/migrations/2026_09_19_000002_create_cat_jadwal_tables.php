<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_jadwal', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('admin_id');
            $table->unsignedInteger('bank_paket_id');
            $table->string('nama');
            $table->dateTime('mulai');
            $table->dateTime('selesai');
            $table->string('token', 8)->nullable();
            $table->timestamps();
        });

        Schema::create('cat_jadwal_kelas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('kelas_id');
            $table->unique(['jadwal_id', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_jadwal_kelas');
        Schema::dropIfExists('cat_jadwal');
    }
};
