<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_jadwal_paket', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('bank_paket_id');
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->unique(['jadwal_id', 'bank_paket_id']);
        });

        if (Schema::hasColumn('cat_jadwal', 'bank_paket_id')) {
            $rows = DB::table('cat_jadwal')->whereNotNull('bank_paket_id')->get(['id', 'bank_paket_id']);
            foreach ($rows as $row) {
                DB::table('cat_jadwal_paket')->insert([
                    'jadwal_id' => $row->id,
                    'bank_paket_id' => $row->bank_paket_id,
                    'urutan' => 1,
                ]);
            }

            Schema::table('cat_jadwal', function (Blueprint $table) {
                $table->dropColumn('bank_paket_id');
            });
        }

        Schema::dropIfExists('cat_jadwal_kelas');
    }

    public function down(): void
    {
        Schema::table('cat_jadwal', function (Blueprint $table) {
            if (! Schema::hasColumn('cat_jadwal', 'bank_paket_id')) {
                $table->unsignedInteger('bank_paket_id')->nullable();
            }
        });

        Schema::create('cat_jadwal_kelas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('kelas_id');
            $table->unique(['jadwal_id', 'kelas_id']);
        });

        Schema::dropIfExists('cat_jadwal_paket');
    }
};
