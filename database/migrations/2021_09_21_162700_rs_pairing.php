<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};

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
            $table->boolean( 'pause_updates' )->default(false)->after('last_updated');
            $table->integer('pair_id')->nullable()->after('id');
            $table->foreign('pair_id')->references('id')->on('routers')->nullOnDelete();

        });

        DB::update('update routers set last_update_started = last_updated' );
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
            $table->dropColumn('pause_updates');

            $table->dropForeign('routers_pair_id_foreign');
            $table->dropColumn('pair_id');
        });
    }
}
