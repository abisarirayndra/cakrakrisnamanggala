<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPengajarIdToDnTesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dn_tes', function (Blueprint $table) {
            $table->integer('pengajar_id')->unsigned()->after('selesai');
            $table->foreign('pengajar_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dn_tes', function (Blueprint $table) {
            Schema::dropIfExists('dn_tes');
        });
    }
}
