<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cat_sesi') && Schema::hasColumn('cat_sesi', 'jadwal_id')) {
            Schema::drop('cat_sesi');
        }

        if (! Schema::hasTable('cat_jadwal_sesi')) {
            Schema::create('cat_jadwal_sesi', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('jadwal_id');
                $table->unsignedInteger('pelajar_id');
                $table->string('status', 20)->default('berjalan');
                $table->integer('nilai')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();
                $table->unique(['jadwal_id', 'pelajar_id']);
            });
        }

        if (! Schema::hasTable('cat_jadwal_jawaban')) {
            Schema::create('cat_jadwal_jawaban', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('sesi_id');
                $table->unsignedInteger('bank_soal_id');
                $table->string('kode', 1)->nullable();
                $table->integer('poin')->default(0);
                $table->timestamps();
                $table->unique(['sesi_id', 'bank_soal_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_jadwal_jawaban');
        Schema::dropIfExists('cat_jadwal_sesi');
    }
};
