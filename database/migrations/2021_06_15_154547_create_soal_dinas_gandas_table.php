<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoalDinasGandasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dn_soalganda', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dn_tes_id')->unsigned();
            $table->foreign('dn_tes_id')
                    ->references('id')
                    ->on('dn_tes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->text('soal');
            $table->text('opsi_a');
            $table->text('opsi_b');
            $table->text('opsi_c');
            $table->text('opsi_d');
            $table->text('opsi_e');
            $table->string('kunci');
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
        Schema::dropIfExists('dn_soalganda');
    }
}
