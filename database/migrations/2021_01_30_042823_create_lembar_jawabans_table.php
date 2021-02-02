<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLembarJawabansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lembar_jawabans', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->integer('soal_id')->unsigned()->nullable();
            $table->foreign('soal_id')
                    ->references('id')
                    ->on('soals')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->integer('jawaban_id')->unsigned()->nullable();
            $table->foreign('jawaban_id')
                    ->references('id')
                    ->on('skor_jawabans')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lembar_jawabans');
    }
}
