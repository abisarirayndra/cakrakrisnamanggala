<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkorJawabansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skor_jawabans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('soal_id')->unsigned()->nullable();
            $table->foreign('soal_id')
                    ->references('id')
                    ->on('soals')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->string('jawaban', 500);
            $table->string('skor');
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
        Schema::dropIfExists('skor_jawabans');
    }
}
