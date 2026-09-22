<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cat_jadwal_sesi')) {
            return;
        }

        Schema::table('cat_jadwal_sesi', function (Blueprint $table) {
            if (! Schema::hasColumn('cat_jadwal_sesi', 'bank_paket_id')) {
                $table->unsignedInteger('bank_paket_id')->nullable()->after('pelajar_id');
            }
        });

        $rows = DB::table('cat_jadwal_sesi')->whereNull('bank_paket_id')->get(['id', 'jadwal_id']);
        foreach ($rows as $row) {
            $first = DB::table('cat_jadwal_paket')
                ->where('jadwal_id', $row->jadwal_id)
                ->orderBy('urutan')
                ->first();

            if (! $first) {
                continue;
            }

            DB::table('cat_jadwal_sesi')->where('id', $row->id)->update([
                'bank_paket_id' => $first->bank_paket_id,
            ]);
        }

        $indexes = collect(Schema::getIndexes('cat_jadwal_sesi'));
        $lama = $indexes->first(function (array $index) {
            return ($index['unique'] ?? false)
                && $index['columns'] === ['jadwal_id', 'pelajar_id'];
        });
        if ($lama) {
            Schema::table('cat_jadwal_sesi', function (Blueprint $table) use ($lama) {
                $table->dropUnique($lama['name']);
            });
        }

        $baru = $indexes->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && $index['columns'] === ['jadwal_id', 'pelajar_id', 'bank_paket_id'];
        });
        if (! $baru) {
            Schema::table('cat_jadwal_sesi', function (Blueprint $table) {
                $table->unique(['jadwal_id', 'pelajar_id', 'bank_paket_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('cat_jadwal_sesi', function (Blueprint $table) {
            $table->dropUnique(['jadwal_id', 'pelajar_id', 'bank_paket_id']);
            $table->dropColumn('bank_paket_id');
            $table->unique(['jadwal_id', 'pelajar_id']);
        });
    }
};
