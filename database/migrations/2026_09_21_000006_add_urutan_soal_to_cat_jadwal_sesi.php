<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cat_jadwal_sesi') || Schema::hasColumn('cat_jadwal_sesi', 'urutan_soal')) {
            return;
        }

        Schema::table('cat_jadwal_sesi', function (Blueprint $table) {
            $table->json('urutan_soal')->nullable()->after('nilai');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cat_jadwal_sesi') || ! Schema::hasColumn('cat_jadwal_sesi', 'urutan_soal')) {
            return;
        }

        Schema::table('cat_jadwal_sesi', function (Blueprint $table) {
            $table->dropColumn('urutan_soal');
        });
    }
};
