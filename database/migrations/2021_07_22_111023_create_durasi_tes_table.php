<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDurasiTesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dn_durasites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dn_tes_id')->unsigned();
            $table->foreign('dn_tes_id')
                    ->references('id')
                    ->on('dn_tes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('pelajar_id')->unsigned();
            $table->foreign('pelajar_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('durasi');
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
        Schema::dropIfExists('dn_durasites');
    }
}
