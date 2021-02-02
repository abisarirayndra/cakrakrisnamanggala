<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekapNilaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekap_nilais', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->integer('paket_id')->unsigned()->nullable();
            $table->foreign('paket_id')
                    ->references('id')
                    ->on('paket_soals')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->integer('tema_id')->unsigned()->nullable();
            $table->foreign('tema_id')
                    ->references('id')
                    ->on('temas')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->integer('total_nilai');
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
        Schema::dropIfExists('rekap_nilais');
    }
}
