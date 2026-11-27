<?php

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

/**
 * Records the external system's own reference for something created through the provisioning
 * API, so that a repeated request is recognised as a repeat.
 *
 * An ordering system that times out waiting for a response has no way to tell whether the
 * member was created or not. Without a record of "we already did this one", its retry creates
 * a second member. With it, the retry returns the first result.
 *
 * Kept as its own table rather than a column on `cust` so the customer table stays untouched:
 * a merge of an upstream release must never have to reconcile a schema change of ours.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create( 'provisioning_references', function( Blueprint $table ) {
            $table->id();

            // the caller's own identifier - an order number, a ticket id, whatever it uses:
            $table->string( 'reference', 191 );

            // what the reference produced:
            $table->string( 'model_type', 191 );
            $table->unsignedInteger( 'model_id' );

            $table->unsignedInteger( 'created_by' )->nullable();
            $table->timestamps();

            // one reference maps to exactly one thing of a given kind. Scoping the uniqueness
            // by model_type lets a single order reference both the member it created and the
            // connection, without either colliding:
            $table->unique( [ 'reference', 'model_type' ], 'provisioning_references_unique' );
            $table->index( [ 'model_type', 'model_id' ], 'provisioning_references_model' );
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists( 'provisioning_references' );
    }
};
