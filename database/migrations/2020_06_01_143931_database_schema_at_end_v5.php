<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DatabaseSchemaAtEndV5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // check if the database already exists
        if( Schema::hasTable('cust') ) {
            echo "*** Looks like IXP Manager database already exists. Treating this as an upgrade and marking migration as done with no action.\n";
            return;
        }

        echo "*** Importing base IXP Manager schema...\n";
        DB::connection()->getPdo()->exec( file_get_contents( database_path('schema/2021-as-at-end-v5.sql') ) );

        Artisan::call('update:reset-mysql-views' );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        echo "*** There is no downgrade possible for the base schema. Drop your database manually...\n";
    }
}
