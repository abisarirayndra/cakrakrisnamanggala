<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_bank_paket', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pendidik_id');
            $table->unsignedInteger('mapel_id')->nullable();
            $table->string('nama');
            $table->string('tipe', 20);
            $table->timestamps();
        });

        Schema::table('cat_bank_soal', function (Blueprint $table) {
            $table->unsignedInteger('paket_id')->nullable()->after('id');
        });

        $soal = DB::table('cat_bank_soal')->orderBy('id')->get();
        foreach ($soal->groupBy(fn ($row) => $row->pendidik_id.'|'.$row->tipe) as $group) {
            $first = $group->first();
            $paketId = DB::table('cat_bank_paket')->insertGetId([
                'pendidik_id' => $first->pendidik_id,
                'mapel_id' => $first->mapel_id,
                'nama' => $first->tipe === 'pembobotan' ? 'Paket Pembobotan' : 'Paket Jawaban Tunggal',
                'tipe' => $first->tipe,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('cat_bank_soal')->whereIn('id', $group->pluck('id'))->update(['paket_id' => $paketId]);
        }

        Schema::table('cat_bank_soal', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }

    public function down(): void
    {
        Schema::table('cat_bank_soal', function (Blueprint $table) {
            $table->string('tipe', 20)->nullable()->after('mapel_id');
        });

        foreach (DB::table('cat_bank_paket')->get() as $paket) {
            DB::table('cat_bank_soal')->where('paket_id', $paket->id)->update(['tipe' => $paket->tipe]);
        }

        Schema::table('cat_bank_soal', function (Blueprint $table) {
            $table->dropColumn('paket_id');
        });
        Schema::dropIfExists('cat_bank_paket');
    }
};
