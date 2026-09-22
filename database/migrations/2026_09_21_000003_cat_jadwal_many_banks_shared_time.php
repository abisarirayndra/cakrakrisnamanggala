<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cat_jadwal_paket')) {
            Schema::create('cat_jadwal_paket', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('jadwal_id');
                $table->unsignedInteger('bank_paket_id');
                $table->unsignedTinyInteger('urutan')->default(1);
                $table->unique(['jadwal_id', 'bank_paket_id']);
            });
        }

        if (Schema::hasColumn('cat_jadwal', 'bank_paket_id')) {
            $rows = DB::table('cat_jadwal')->whereNotNull('bank_paket_id')->get(['id', 'bank_paket_id']);
            foreach ($rows as $row) {
                $sudah = DB::table('cat_jadwal_paket')
                    ->where('jadwal_id', $row->id)
                    ->where('bank_paket_id', $row->bank_paket_id)
                    ->exists();

                if ($sudah) {
                    continue;
                }

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
    }

    public function down(): void
    {
        Schema::table('cat_jadwal', function (Blueprint $table) {
            if (! Schema::hasColumn('cat_jadwal', 'bank_paket_id')) {
                $table->unsignedInteger('bank_paket_id')->nullable()->after('admin_id');
            }
        });

        if (Schema::hasTable('cat_jadwal_paket')) {
            $rows = DB::table('cat_jadwal_paket')->orderBy('urutan')->get();
            foreach ($rows as $row) {
                DB::table('cat_jadwal')->where('id', $row->jadwal_id)->whereNull('bank_paket_id')->update([
                    'bank_paket_id' => $row->bank_paket_id,
                ]);
            }
        }

        Schema::dropIfExists('cat_jadwal_paket');
    }
};
