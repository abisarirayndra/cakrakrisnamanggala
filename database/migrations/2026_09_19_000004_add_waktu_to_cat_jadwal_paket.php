<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cat_jadwal_paket', function (Blueprint $table) {
            if (! Schema::hasColumn('cat_jadwal_paket', 'mulai')) {
                $table->dateTime('mulai')->nullable();
            }
            if (! Schema::hasColumn('cat_jadwal_paket', 'selesai')) {
                $table->dateTime('selesai')->nullable();
            }
        });

        if (Schema::hasColumn('cat_jadwal', 'mulai')) {
            $jadwal = DB::table('cat_jadwal')->get(['id', 'mulai', 'selesai']);
            foreach ($jadwal as $row) {
                DB::table('cat_jadwal_paket')
                    ->where('jadwal_id', $row->id)
                    ->whereNull('mulai')
                    ->update([
                        'mulai' => $row->mulai,
                        'selesai' => $row->selesai,
                    ]);
            }

            Schema::table('cat_jadwal', function (Blueprint $table) {
                $table->dropColumn(['mulai', 'selesai']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('cat_jadwal', function (Blueprint $table) {
            if (! Schema::hasColumn('cat_jadwal', 'mulai')) {
                $table->dateTime('mulai')->nullable();
                $table->dateTime('selesai')->nullable();
            }
        });

        Schema::table('cat_jadwal_paket', function (Blueprint $table) {
            if (Schema::hasColumn('cat_jadwal_paket', 'mulai')) {
                $table->dropColumn(['mulai', 'selesai']);
            }
        });
    }
};
