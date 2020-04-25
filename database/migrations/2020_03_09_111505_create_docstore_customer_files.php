<?php

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

class CreateDocstoreCustomerFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docstore_customer_files', function (Blueprint $table) {
            $table->bigIncrements('id' );
            $table->integer('cust_id' )->nullable(false );
            $table->bigInteger('docstore_customer_directory_id' )->nullable(true )->unsigned();
            $table->string('name',100);
            $table->string('disk',100 )->default('docstore_customers');
            $table->string('path',255 )->default('');
            $table->string('sha256',64 )->nullable();
            $table->text('description' )->nullable();
            $table->smallInteger('min_privs' );

            $table->dateTime( 'file_last_updated' );

            // we're not using a FK constraint here as users can be deleted without deleting files.
            $table->integer('created_by')->nullable();

            $table->foreign('cust_id' )->references('id' )->on('cust' );
            $table->foreign('docstore_customer_directory_id' )->references('id' )->on('docstore_customer_directories' );

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
        Schema::dropIfExists('docstore_customer_files');
    }
}
