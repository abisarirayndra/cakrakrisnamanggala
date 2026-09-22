<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cat_bank_paket', function (Blueprint $table) {
            $table->string('bentuk', 20)->default('matematis')->after('tipe');
        });
    }

    public function down(): void
    {
        Schema::table('cat_bank_paket', function (Blueprint $table) {
            $table->dropColumn('bentuk');
        });
    }
};
