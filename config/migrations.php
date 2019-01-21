<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */
    'table'     => 'migrations',
    /*
    |--------------------------------------------------------------------------
    | Migration Directory
    |--------------------------------------------------------------------------
    |
    | This directory is where all migrations will be stored
    |
    */
    'directory' => database_path('migrations'),
    /*
    |--------------------------------------------------------------------------
    | Migration Namespace
    |--------------------------------------------------------------------------
    |
    | This namespace will be used on all migrations
    |
    */
    'namespace' => 'Database\\Migrations',
    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | Tables which are filtered by Regular Expression. You optionally
    | exclude or limit to certain tables. The default will
    | filter all tables.
    |
    */
    'schema'    => [
        'filter' => '/^(?).*$/'
    ]
];
