<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmAbsensiStafTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_absensi_staf', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staf_id')->unsigned();
            $table->foreign('staf_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->datetime('datang');
            $table->datetime('pulang')->nullable();
            $table->integer('status');
            $table->text('jurnal');
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
        Schema::dropIfExists('adm_absensi_staf');
    }
}
