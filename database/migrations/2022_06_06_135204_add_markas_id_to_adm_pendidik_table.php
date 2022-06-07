<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarkasIdToAdmPendidikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adm_pendidik', function (Blueprint $table) {
            $table->integer('markas_id')->unsigned()->nullable()->after('status_dapodik');
            $table->foreign('markas_id')
                    ->references('id')
                    ->on('adm_markas')
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
        Schema::table('adm_pendidik', function (Blueprint $table) {
            //
        });
    }
}
