<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cat_jadwal', function (Blueprint $table) {
            if (! Schema::hasColumn('cat_jadwal', 'bank_paket_id')) {
                $table->unsignedInteger('bank_paket_id')->nullable()->after('admin_id');
            }
            if (! Schema::hasColumn('cat_jadwal', 'mulai')) {
                $table->dateTime('mulai')->nullable()->after('nama');
            }
            if (! Schema::hasColumn('cat_jadwal', 'selesai')) {
                $table->dateTime('selesai')->nullable()->after('mulai');
            }
        });

        if (Schema::hasTable('cat_jadwal_paket')) {
            $jadwal = DB::table('cat_jadwal')->get();
            foreach ($jadwal as $row) {
                $first = DB::table('cat_jadwal_paket')
                    ->where('jadwal_id', $row->id)
                    ->orderBy('urutan')
                    ->first();

                if (! $first) {
                    continue;
                }

                DB::table('cat_jadwal')->where('id', $row->id)->update([
                    'bank_paket_id' => $first->bank_paket_id,
                    'mulai' => $first->mulai ?? $row->mulai ?? now(),
                    'selesai' => $first->selesai ?? $row->selesai ?? now(),
                ]);
            }

            Schema::dropIfExists('cat_jadwal_paket');
        }

        $tanpaToken = DB::table('cat_jadwal')
            ->where(function ($query) {
                $query->whereNull('token')->orWhere('token', '');
            })
            ->get(['id']);

        foreach ($tanpaToken as $row) {
            do {
                $token = Str::upper(Str::random(6));
            } while (DB::table('cat_jadwal')->where('token', $token)->exists());

            DB::table('cat_jadwal')->where('id', $row->id)->update(['token' => $token]);
        }

        $punyaUniqueToken = collect(Schema::getIndexes('cat_jadwal'))
            ->contains(fn (array $index) => in_array('token', $index['columns'], true) && ($index['unique'] ?? false));

        if (! $punyaUniqueToken) {
            Schema::table('cat_jadwal', function (Blueprint $table) {
                $table->unique('token');
            });
        }
    }

    public function down(): void
    {
        Schema::create('cat_jadwal_paket', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('jadwal_id');
            $table->unsignedInteger('bank_paket_id');
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->dateTime('mulai')->nullable();
            $table->dateTime('selesai')->nullable();
            $table->unique(['jadwal_id', 'bank_paket_id']);
        });

        if (Schema::hasColumn('cat_jadwal', 'bank_paket_id')) {
            $rows = DB::table('cat_jadwal')->whereNotNull('bank_paket_id')->get();
            foreach ($rows as $row) {
                DB::table('cat_jadwal_paket')->insert([
                    'jadwal_id' => $row->id,
                    'bank_paket_id' => $row->bank_paket_id,
                    'urutan' => 1,
                    'mulai' => $row->mulai,
                    'selesai' => $row->selesai,
                ]);
            }

            Schema::table('cat_jadwal', function (Blueprint $table) {
                $table->dropColumn(['bank_paket_id', 'mulai', 'selesai']);
            });
        }
    }
};
