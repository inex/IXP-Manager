<?php

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

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
