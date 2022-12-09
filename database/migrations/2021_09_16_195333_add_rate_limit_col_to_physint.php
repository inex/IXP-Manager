<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRateLimitColToPhysint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('physicalinterface', function (Blueprint $table) {
            $table->integer( 'rate_limit' )->after( 'duplex' )->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('physicalinterface', function (Blueprint $table) {
            $table->dropColumn('rate_limit');
        });
    }
}
