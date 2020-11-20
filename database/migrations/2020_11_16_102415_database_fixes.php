<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use IXP\Models\PatchPanelPort;

class DatabaseFixes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Change the default value of the chargeable field to CHARGEABLE_NO
        Schema::table('patch_panel_port',function ( $table ) {
            $table->integer( 'chargeable' )->default( PatchPanelPort::CHARGEABLE_NO )->change();
        });
        // Update Patch panel ports that have chargeable set to 0 to 2 (CHARGEABLE_NO)
        DB::table('patch_panel_port')->where('chargeable' , 0 )
            ->update( [ 'chargeable' => PatchPanelPort::CHARGEABLE_NO ] );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
