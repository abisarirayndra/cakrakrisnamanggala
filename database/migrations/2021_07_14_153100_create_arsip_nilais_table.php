<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArsipNilaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dn_arsipnilai', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode')->unique();
            $table->integer('dn_tes_id')->unsigned();
            $table->foreign('dn_tes_id')
                    ->references('id')
                    ->on('dn_tes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->datetime('tanggal');
            $table->integer('pendidik_id')->unsigned();
            $table->foreign('pendidik_id')
                    ->references('id')
                    ->on('users')
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
        Schema::dropIfExists('dn_arsipnilai');
    }
}
