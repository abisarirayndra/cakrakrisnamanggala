<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMapelIdToRekapNilaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rekap_nilais', function (Blueprint $table) {
            $table->integer('mapel_id')->unsigned()->nullable()->after('tema_id');
            $table->foreign('mapel_id')
                    ->references('id')
                    ->on('mapels')
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
        Schema::table('rekap_nilais', function (Blueprint $table) {
            Schema::dropIfExists('rekap_nilais');
        });
    }
}
