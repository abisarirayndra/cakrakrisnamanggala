<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMulaiToDnTesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dn_tes', function (Blueprint $table) {
            $table->datetime('mulai')->after('nilai_pokok');
            $table->datetime('selesai')->after('mulai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dn_tes', function (Blueprint $table) {
            Schema::dropIfExists('dn_tes');
        });
    }
}
