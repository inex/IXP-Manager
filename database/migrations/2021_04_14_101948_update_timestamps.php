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

class UpdateTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_keys', function (Blueprint $table) {
            DB::statement("ALTER TABLE api_keys MODIFY COLUMN created_at DATETIME AFTER description");
            $table->timestamp( 'created_at' )->change();
        });

        Schema::table('contact_group', function (Blueprint $table) {
            DB::statement("ALTER TABLE contact_group CHANGE created_at created_at TIMESTAMP NULL");
        });

        Schema::table('cust', function (Blueprint $table) {
            DB::statement("ALTER TABLE cust MODIFY COLUMN created_at DATETIME AFTER peeringdb_oauth");
            DB::statement("ALTER TABLE cust MODIFY COLUMN updated_at DATETIME AFTER created_at");
            $table->timestamp( 'created_at' )->change();
            $table->timestamp( 'updated_at' )->change();
        });

        Schema::table('cust_notes', function (Blueprint $table) {
          DB::statement("ALTER TABLE cust_notes CHANGE created_at created_at TIMESTAMP NULL");
          DB::statement("ALTER TABLE cust_notes CHANGE updated_at updated_at TIMESTAMP NULL");
        });

        Schema::table('cust_tag', function (Blueprint $table) {
            DB::statement("ALTER TABLE cust_tag CHANGE created_at created_at TIMESTAMP NULL");
            DB::statement("ALTER TABLE cust_tag CHANGE updated_at updated_at TIMESTAMP NULL");
        });

        Schema::table('customer_to_users', function (Blueprint $table) {
            DB::statement("ALTER TABLE customer_to_users MODIFY COLUMN created_at DATETIME AFTER last_login_via");
            $table->timestamp( 'created_at' )->change();
        });

        Schema::table('l2address', function (Blueprint $table) {
            $table->timestamp( 'created_at' )->change();
        });

        Schema::table('logos', function (Blueprint $table) {
            DB::statement("ALTER TABLE logos MODIFY COLUMN created_at DATETIME AFTER height");
            $table->timestamp( 'created_at' )->change();
        });

        Schema::table('peering_manager', function (Blueprint $table) {
            $table->timestamp( 'created_at' )->change();
            $table->timestamp( 'updated_at' )->change();
        });

        Schema::table('routers', function (Blueprint $table) {
            DB::statement("ALTER TABLE routers MODIFY COLUMN updated_at DATETIME AFTER created_at");
            $table->timestamp( 'updated_at' )->change();
        });

        Schema::table('user', function (Blueprint $table) {
            DB::statement("ALTER TABLE user MODIFY COLUMN created_at DATETIME AFTER extra_attributes");
            DB::statement("ALTER TABLE user MODIFY COLUMN updated_at DATETIME AFTER created_at");
            $table->timestamp( 'created_at' )->change();
            $table->timestamp( 'updated_at' )->change();
        });

        Schema::table('user_2fa', function (Blueprint $table) {
            $table->timestamp( 'created_at' )->change();
            $table->timestamp( 'updated_at' )->change();
        });

        Schema::table('user_remember_tokens', function (Blueprint $table) {
            DB::statement("ALTER TABLE user_remember_tokens MODIFY COLUMN created_at DATETIME AFTER is_2fa_complete");
            $table->timestamp( 'created_at' )->change();
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
            DB::statement("ALTER TABLE api_keys MODIFY COLUMN created_at DATETIME AFTER description");
            $table->timestamp( 'created_at' )->change();
        });

        Schema::table('contact_group', function (Blueprint $table) {
            DB::statement("ALTER TABLE contact_group CHANGE created_at created_at DATETIME NULL");
        });

        Schema::table('cust', function (Blueprint $table) {
            DB::statement("ALTER TABLE cust MODIFY COLUMN created_at DATETIME AFTER peeringdb_oauth");
            DB::statement("ALTER TABLE cust MODIFY COLUMN updated_at DATETIME AFTER created_at");
            $table->dateTime( 'created_at' )->change();
            $table->dateTime( 'updated_at' )->change();
        });

        Schema::table('cust_notes', function (Blueprint $table) {
            DB::statement("ALTER TABLE cust_notes CHANGE created_at created_at DATETIME NULL");
            DB::statement("ALTER TABLE cust_notes CHANGE updated_at updated_at DATETIME NULL");
        });

        Schema::table('cust_tag', function (Blueprint $table) {
            DB::statement("ALTER TABLE cust_tag CHANGE created_at created_at DATETIME NULL");
            DB::statement("ALTER TABLE cust_tag CHANGE updated_at updated_at DATETIME NULL");
        });

        Schema::table('customer_to_users', function (Blueprint $table) {
            $table->dateTime( 'created_at' )->change();
        });

        Schema::table('l2address', function (Blueprint $table) {
            $table->dateTime( 'created_at' )->change();
        });

        Schema::table('logos', function (Blueprint $table) {
            $table->dateTime( 'created_at' )->change();
        });

        Schema::table('peering_manager', function (Blueprint $table) {
            $table->dateTime( 'created_at' )->change();
            $table->dateTime( 'updated_at' )->change();
        });

        Schema::table('routers', function (Blueprint $table) {
            $table->dateTime( 'updated_at' )->change();
        });

        Schema::table('user', function (Blueprint $table) {
            $table->dateTime( 'created_at' )->change();
            $table->dateTime( 'updated_at' )->change();
        });

        Schema::table('user_2fa', function (Blueprint $table) {
            $table->dateTime( 'created_at' )->change();
            $table->dateTime( 'updated_at' )->change();
        });

        Schema::table('user_remember_tokens', function (Blueprint $table) {
            $table->dateTime( 'created_at' )->change();
        });
    }
}
