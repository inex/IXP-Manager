<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExportToIxfVlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vlan', function (Blueprint $table) {
            $table->tinyInteger( 'export_to_ixf' )->after( 'peering_manager' )->nullable( false )->default(1);
        });

        \IXP\Models\Vlan::wherePrivate(1)->update(['export_to_ixf' => false]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vlan', function (Blueprint $table) {
            $table->dropColumn('export_to_ixf');
        });
    }
}
