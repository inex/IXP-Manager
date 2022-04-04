<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColoPPTypePatchPanel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patch_panel', function (Blueprint $table) {
            $table->tinyInteger( 'colo_pp_type' )->after( 'active' )->nullable( false )->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patch_panel', function (Blueprint $table) {
            $table->dropColumn('colo_pp_type');
        });

    }
}
