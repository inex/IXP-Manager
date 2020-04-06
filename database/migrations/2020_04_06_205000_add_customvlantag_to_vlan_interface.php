<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomvlantagToVlanInterface extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('vlaninterface', function (Blueprint $table) {
        $table->integer('customvlantag')->default(0)->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vlaninterface', function (Blueprint $table) {
          $table->dropColumn('customvlantag');
            //
        });
    }
}