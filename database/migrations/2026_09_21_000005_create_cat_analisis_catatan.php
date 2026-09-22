<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('cat_analisis_catatan');
    }
};
