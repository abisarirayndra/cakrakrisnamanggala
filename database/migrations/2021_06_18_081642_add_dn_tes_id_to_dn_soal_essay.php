<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDnTesIdToDnSoalEssay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dn_soalessay', function (Blueprint $table) {
            $table->integer('dn_tes_id')->unsigned();
            $table->foreign('dn_tes_id')
                    ->references('id')
                    ->on('dn_tes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dn_soalessay', function (Blueprint $table) {
            Schema::dropIfExists('dn_soalessay');
        });
    }
}
