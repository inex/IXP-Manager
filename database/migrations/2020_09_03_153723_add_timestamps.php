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

class AddTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->renameColumn( 'created', 'created_at' );
            $table->timestamp( 'updated_at' )->nullable();
        });

        Schema::table('bgp_sessions', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('cabinet', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('company_billing_detail', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('company_registration_detail', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('console_server', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('consoleserverconnection', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('contact', function (Blueprint $table) {
            $table->renameColumn( 'created', 'created_at' );
            $table->renameColumn( 'lastupdated', 'updated_at' );
        });

        Schema::table('contact_group', function (Blueprint $table) {
            $table->renameColumn( 'created', 'created_at' );
            $table->timestamp( 'updated_at' )->nullable();
        });

        Schema::table('contact_to_group', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('corebundles', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('coreinterfaces', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('corelinks', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('cust', function (Blueprint $table) {
            $table->renameColumn( 'created', 'created_at' );
            $table->renameColumn( 'lastupdated', 'updated_at' );
        });

        Schema::table('cust', function (Blueprint $table) {
            $table->dateTime( 'created_at' )->change();
            $table->dateTime( 'updated_at' )->change();
        });

        Schema::table('cust_notes', function (Blueprint $table) {
            $table->renameColumn( 'created', 'created_at' );
            $table->renameColumn( 'updated', 'updated_at' );
        });

        Schema::table('customer_to_users', function (Blueprint $table) {
            $table->timestamp( 'updated_at' )->nullable();
        });

        Schema::table('custkit', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('cust_tag', function (Blueprint $table) {
            $table->renameColumn( 'created', 'created_at' );
            $table->renameColumn( 'updated', 'updated_at' );
        });

        Schema::table('cust_to_cust_tag', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('infrastructure', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('ipv4address', function (Blueprint $table) {
             $table->timestamps();
        });

        Schema::table('ipv6address', function (Blueprint $table) {
             $table->timestamps();
        });

        Schema::table('irrdb_asn', function (Blueprint $table) {
             $table->timestamps();
        });

        Schema::table('irrdbconfig', function (Blueprint $table) {
             $table->timestamps();
        });

        Schema::table('irrdb_prefix', function (Blueprint $table) {
             $table->timestamps();
        });

        Schema::table('l2address', function (Blueprint $table) {
            $table->renameColumn( 'created', 'created_at' );
            $table->timestamp( 'updated_at' )->nullable();
        });

        Schema::table('location', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('logos', function (Blueprint $table) {
            $table->renameColumn( 'uploaded_at', 'created_at' );
            $table->timestamp( 'updated_at' )->nullable();
        });

        Schema::table('macaddress', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('networkinfo', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('oui', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('patch_panel', function (Blueprint $table) {
            $table->timestamps();
            $table->date( 'installation_date' )->change();
        });

        Schema::table('patch_panel_port', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('patch_panel_port_file', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('patch_panel_port_history', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('patch_panel_port_history_file', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('peering_manager', function (Blueprint $table) {
            $table->renameColumn( 'updated', 'updated_at' );
            $table->renameColumn( 'created', 'created_at' );
        });

        Schema::table('physicalinterface', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('routers', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('sflow_receiver', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('switch', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('switchport', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('traffic_daily', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('traffic_daily_phys_ints', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('user', function (Blueprint $table) {
            $table->renameColumn( 'lastupdated', 'updated_at' );
            $table->renameColumn( 'created', 'created_at' );
        });

        Schema::table('user_logins', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('user_remember_tokens', function (Blueprint $table) {
            $table->renameColumn( 'created', 'created_at' );
            $table->timestamp( 'updated_at' )->nullable();
        });

        Schema::table('vendor', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('virtualinterface', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('vlaninterface', function (Blueprint $table) {
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
        Schema::table('api_keys', function (Blueprint $table) {
            $table->renameColumn( 'created_at', 'created' );
            $table->dropColumn( 'updated_at' );
        });

        Schema::table('bgp_sessions', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('cabinet', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('company_billing_detail', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('company_registration_detail', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('console_server', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('consoleserverconnection', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('contact', function (Blueprint $table) {
            $table->renameColumn( 'created_at', 'created' );
            $table->renameColumn( 'updated_at', 'lastupdated' );
        });

        Schema::table('contact_group', function (Blueprint $table) {
            $table->renameColumn( 'created_at', 'created' );
            $table->dropColumn( 'updated_at' );
        });

        Schema::table('contact_to_group', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('corebundles', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('coreinterfaces', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('corelinks', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('cust', function (Blueprint $table) {
            $table->renameColumn( 'created_at', 'created' );
            $table->renameColumn( 'updated_at', 'lastupdated' );
        });

        Schema::table('cust', function (Blueprint $table) {
            $table->date( 'created' )->change();
            $table->date( 'lastupdated' )->change();
        });

        Schema::table('cust_notes', function (Blueprint $table) {
            $table->renameColumn( 'created_at', 'created' );
            $table->renameColumn( 'updated_at', 'updated' );
        });

        Schema::table('customer_to_users', function (Blueprint $table) {
            $table->dropColumn( 'updated_at' );
        });

        Schema::table('custkit', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('cust_tag', function (Blueprint $table) {
            $table->renameColumn( 'created_at', 'created' );
            $table->renameColumn( 'updated_at', 'updated' );
        });

        Schema::table('cust_to_cust_tag', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('infrastructure', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('ipv4address', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('ipv6address', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('irrdb_asn', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('irrdbconfig', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('irrdb_prefix', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('l2address', function (Blueprint $table) {
            $table->renameColumn( 'created_at', 'created' );
            $table->dropColumn( 'updated_at' );
        });

        Schema::table('location', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('logos', function (Blueprint $table) {
            $table->renameColumn( 'created_at', 'uploaded_at' );
            $table->dropColumn( 'updated_at' );
        });

        Schema::table('macaddress', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('networkinfo', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('oui', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('patch_panel', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('patch_panel_port', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('patch_panel_port_file', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('patch_panel_port_history', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('patch_panel_port_history_file', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('peering_manager', function (Blueprint $table) {
            $table->renameColumn( 'updated_at', 'updated' );
            $table->renameColumn( 'created_at', 'created' );
        });

        Schema::table('physicalinterface', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('routers', function (Blueprint $table) {
            $table->renameColumn( 'updated_at', 'last_updated' );
            $table->dropColumn( 'created_at' );
        });

        Schema::table('sflow_receiver', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('switch', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('switchport', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('traffic_daily', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('traffic_daily_phys_ints', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('user', function (Blueprint $table) {
            $table->renameColumn( 'updated_at', 'lastupdated' );
            $table->renameColumn( 'created_at', 'created' );
        });

        Schema::table('user_logins', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('user_remember_tokens', function (Blueprint $table) {
            $table->renameColumn( 'created_at', 'created' );
            $table->removeColumn( 'updated_at' );
        });

        Schema::table('vendor', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('virtualinterface', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('vlaninterface', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('vlan', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
}
