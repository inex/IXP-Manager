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

class DeleteIxpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_to_ixp', function (Blueprint $table) {
            $table->drop();
        });

        $infrastructure_fks = array_column(
            Schema::getConnection()->select('SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE '
                . 'WHERE TABLE_SCHEMA = "' . ( config('database.connections.mysql.database') ?? '' ) . '" AND TABLE_NAME = "infrastructure" AND '
                . 'REFERENCED_TABLE_SCHEMA = "' . ( config('database.connections.mysql.database') ?? '' ) . '" AND REFERENCED_TABLE_NAME = "ixp"'
            ), 'CONSTRAINT_NAME'
        );

        foreach( $infrastructure_fks as $fk ) {
            Schema::table( 'infrastructure', function( Blueprint $table ) use ( $fk ) {
                $table->dropForeign( $fk );
            } );
        }

        Schema::table( 'infrastructure', function( Blueprint $table ) {
            $table->dropUnique( 'IXPSN' );
            $table->dropColumn( 'ixp_id' );
        } );

        $traffic_daily_fks = array_column(
            Schema::getConnection()->select('SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE '
                . 'WHERE TABLE_SCHEMA = "' . ( config('database.connections.mysql.database') ?? '' ) . '" AND TABLE_NAME = "traffic_daily" AND '
                . 'REFERENCED_TABLE_SCHEMA = "' . ( config('database.connections.mysql.database') ?? '' ) . '" AND REFERENCED_TABLE_NAME = "ixp"'
            ), 'CONSTRAINT_NAME'
        );

        foreach( $traffic_daily_fks as $fk ) {
            Schema::table( 'traffic_daily', function( Blueprint $table ) use ( $fk ) {
                $table->dropForeign( $fk );
                $table->dropColumn( 'ixp_id' );
            } );
        }

        Schema::table('ixp', function (Blueprint $table) {
            $table->drop();
        });
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
