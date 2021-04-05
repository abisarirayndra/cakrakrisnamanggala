<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNilaisToRekapNilaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rekap_nilais', function (Blueprint $table) {
            $table->integer('nilai_mtk')->after('tema_id')->nullable();
            $table->integer('nilai_bi')->after('nilai_mtk')->nullable();
            $table->integer('nilai_bing')->after('nilai_bi')->nullable();
            $table->integer('nilai_ipu')->after('nilai_bing')->nullable();
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
            Schema::dropIfExists('rekap_nilais');
        });
    }
}
