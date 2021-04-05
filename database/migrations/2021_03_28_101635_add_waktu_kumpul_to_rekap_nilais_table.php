<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWaktuKumpulToRekapNilaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rekap_nilais', function (Blueprint $table) {
            $table->datetime('kumpul_mtk')->after('nilai_mtk')->nullable();
            $table->datetime('kumpul_bi')->after('nilai_bi')->nullable();
            $table->datetime('kumpul_bing')->after('nilai_bing')->nullable();
            $table->datetime('kumpul_ipu')->after('nilai_ipu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rekap_nilais', function (Blueprint $table) {
            //
        });
    }
}
