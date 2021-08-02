<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJawabanToDnJawabangandaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dn_jawabanganda', function (Blueprint $table) {
            $table->string('jawaban', 1)->after('dn_soalganda_id');
            $table->integer('nilai')->after('jawaban');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dn_jawabanganda', function (Blueprint $table) {
            //
        });
    }
}
