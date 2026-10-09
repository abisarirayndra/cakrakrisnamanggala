<?php

use App\Support\AbsensiStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['adm_absensi_pendidik', 'adm_absensi_pelajar'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)
                ->where('status', AbsensiStatus::SAKIT)
                ->whereNotNull('datang')
                ->update(['status' => AbsensiStatus::HADIR]);
        }
    }

    public function down(): void
    {
    }
};
