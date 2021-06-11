<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaketDinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dn_pakets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_paket');
            $table->integer('kelas')->unsigned();
            $table->foreign('kelas')
                        ->references('id')
                        ->on('kelas')
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
        Schema::dropIfExists('dn_pakets');
    }
}
