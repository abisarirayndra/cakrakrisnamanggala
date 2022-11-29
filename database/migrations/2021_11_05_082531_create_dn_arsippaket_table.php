<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDnArsippaketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dn_arsippaket', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode')->unique();
            $table->integer('dn_paket_id')->unsigned();
            $table->foreign('dn_paket_id')
                    ->references('id')
                    ->on('dn_pakets')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->datetime('tanggal');
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
        Schema::dropIfExists('dn_arsippaket');
    }
}
