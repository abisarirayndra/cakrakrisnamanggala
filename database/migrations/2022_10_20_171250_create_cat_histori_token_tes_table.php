<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatHistoriTokenTesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_histori_token_tes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tes_id')->unsigned()->nullable();
            $table->foreign('tes_id')
                    ->references('id')
                    ->on('dn_tes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->string('token')->unique();
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
        Schema::dropIfExists('cat_histori_token_tes');
    }
}
