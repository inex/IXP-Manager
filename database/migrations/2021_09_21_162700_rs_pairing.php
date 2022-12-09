<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RsPairing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->datetime( 'last_update_started' )->nullable()->after('skip_md5');

            $table->integer('pair_id')->nullable()->after('id');
            $table->foreign('pair_id')->references('id')->on('routers')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropColumn('last_update_started');

            $table->dropForeign('routers_pair_id_foreign');
            $table->dropColumn('pair_id');
        });
    }
}
