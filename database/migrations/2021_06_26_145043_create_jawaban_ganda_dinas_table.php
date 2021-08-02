<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJawabanGandaDinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dn_jawabanganda', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pelajar_id')->unsigned();
            $table->foreign('pelajar_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('dn_soalganda_id')->unsigned();
            $table->foreign('dn_soalganda_id')
                    ->references('id')
                    ->on('dn_soalganda')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dn_jawabanganda');
    }
}
