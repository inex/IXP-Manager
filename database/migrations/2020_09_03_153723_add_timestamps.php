<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cabinet', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('infrastructure', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('vlan', function (Blueprint $table) {
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
        Schema::table('cabinet', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('infrastructure', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('vlan', function (Blueprint $table) {
            $table->dropTimestamps();
        });


    }
}
