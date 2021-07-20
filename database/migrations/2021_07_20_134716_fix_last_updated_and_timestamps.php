<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixLastUpdatedAndTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routers', function (Blueprint $table) {
            if( !Schema::hasColumn( 'routers', 'last_updated' ) ) {
                $table->dateTime( 'last_updated' )->nullable();
            } else {
                $table->dateTime( 'last_updated' )->change();
                $table->dateTime( 'last_updated' )->nullable()->change();
            }

            if( !Schema::hasColumn( 'routers', 'created_at' ) ) {
                $table->timestamp( 'created_at' )->nullable();
            } else {
                $table->timestamp( 'created_at' )->nullable()->change();
            }

            if( !Schema::hasColumn( 'routers', 'updated_at' ) ) {
                $table->timestamp( 'updated_at' )->nullable();
            } else {
                $table->timestamp( 'updated_at' )->nullable()->change();
            }

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
            //
        });
    }
}
