<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmAbsensiPendidikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_absensi_pendidik', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jadwal_id')->unsigned();
            $table->foreign('jadwal_id')
                    ->references('id')
                    ->on('adm_jadwal')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('pendidik_id')->unsigned();
            $table->foreign('pendidik_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->datetime('datang');
            $table->datetime('pulang')->nullable();
            $table->integer('status');
            $table->text('jurnal')->nullable();
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
        Schema::dropIfExists('adm_absensi_pendidik');
    }
}
