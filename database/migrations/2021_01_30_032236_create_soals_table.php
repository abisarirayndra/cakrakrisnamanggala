<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tema_id')->unsigned()->nullable();
            $table->foreign('tema_id')
                    ->references('id')
                    ->on('temas')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->string('nomor_soal');
            $table->string('soal', 500)->nullable();
            $table->string('opsi_a', 500)->nullable();
            $table->string('opsi_b', 500)->nullable();
            $table->string('opsi_c', 500)->nullable();
            $table->string('opsi_d', 500)->nullable();
            $table->string('opsi_e', 500)->nullable();
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
        Schema::dropIfExists('soals');
    }
}
