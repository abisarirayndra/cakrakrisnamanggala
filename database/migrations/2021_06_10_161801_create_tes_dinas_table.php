<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTesDinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dn_tes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dn_paket_id')->unsigned();
            $table->foreign('dn_paket_id')
                    ->references('id')
                    ->on('dn_pakets')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('mapel_id')->unsigned();
            $table->foreign('mapel_id')
                    ->references('id')
                    ->on('mapels')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('nilai_pokok');
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
        Schema::dropIfExists('dn_tes');
    }
}
