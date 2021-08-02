<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekapTniPolrisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dn_rekap_tnipolri', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pelajar_id')->unsigned();
            $table->foreign('pelajar_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('dn_paket_id')->unsigned();
            $table->foreign('dn_paket_id')
                    ->references('id')
                    ->on('dn_pakets')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->float('bin')->nullable();
            $table->float('bing')->nullable();
            $table->float('mtk')->nullable();
            $table->float('ipu_wk')->nullable();
            $table->float('total_nilai')->nullable();
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
        Schema::dropIfExists('dn_rekap_tnipolri');
    }
}
