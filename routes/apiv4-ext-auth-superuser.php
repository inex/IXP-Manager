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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - External
|--------------------------------------------------------------------------
|
| ** EXTERNAL ROUTES **
|
| These routes are intended for authenticated superusers (e.g. via API key)
| from an external source. I.e. no cookie, no CSRF, no AJAX.
|
|
*/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// DNS ARPA Entries
//
Route::group( [  'prefix' => 'dns/arpa' ], function() {
    // Returns plain text from the given template (api/v4/dns/[template]):
    Route::get('{vlan}/{protocol}/{template}',  'DnsController@arpaTemplated');
    // Returns JSON object:
    Route::get('{vlan}/{protocol}',             'DnsController@arpa');
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Users
//
Route::group( [  'prefix' => 'user' ], function() {
    // Returns all users (or users with given integer privilege) as JSON
    Route::get('json',         'UserController@json');
    Route::get('json/{priv}',  'UserController@json');

    // Returns all users (or users with given integer privilege) as a formatted template (e.g. for TACACS)
    // see: http://docs.ixpmanager.org/features/tacacs/
    Route::get( 'formatted',                   'UserController@formatted' );
    Route::get( 'formatted/{priv}',            'UserController@formatted' );
    Route::get( 'formatted/{priv}/{template}', 'UserController@formatted' );
    Route::post('formatted',                   'UserController@formatted' );
    Route::post('formatted/{priv}',            'UserController@formatted' );
    Route::post('formatted/{priv}/{template}', 'UserController@formatted' );
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Mailing Lists
//
Route::group( [  'prefix' => 'mailing-list' ], function() {
    Route::get( 'subscribers/{listname}',      'MailingListController@subscribers' );
    Route::get( 'subscribers/json/{listname}', 'MailingListController@subscribers' );

    Route::get( 'unsubscribed/{listname}',      'MailingListController@unsubscribed' );
    Route::get( 'unsubscribed/json/{listname}', 'MailingListController@unsubscribed' );

    Route::post( 'init/{listname}',      'MailingListController@init' );
    Route::post( 'init/json/{listname}', 'MailingListController@init' );
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Routers
//
Route::group( [  'prefix' => 'router' ], function() {
    // Generate router configuration:
    Route::get('gen_config/{handle}',    'RouterController@genConfig'    );
    Route::get('gen-config/{handle}',    'RouterController@genConfig'    )->name( 'apiv4-router-gen-config' );

    // Get / set a routers last updated time:
    Route::post('updated/{handle}',                          'RouterController@setLastUpdated'          );
    Route::get('updated/{handle}',                           'RouterController@getLastUpdated'          );
    Route::get('updated',                                    'RouterController@getAllLastUpdated'       );
    Route::get('updated-before/{threshold}',                 'RouterController@getAllLastUpdatedBefore' );
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Sflow Receiver
//
Route::group( [  'prefix' => 'sflow-receivers' ], function() {
    Route::get('pretag.map',        'SflowReceiverController@pretagMap'         );
    Route::get('receivers.lst',     'SflowReceiverController@receiversLst'      );
});

Route::get('sflow-receivers.{format}',         'SflowReceiverController@getReceiverList'   );




///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Vlan Interface
//
Route::get( 'vlan-interface/l2-addresses/{vli}',                'VlanInterfaceController@getL2A' );
Route::get( 'sflow-db-mapper/learned-macs',                     'VlanInterfaceController@sflowLearnedMacs' );
Route::get( 'sflow-db-mapper/configured-macs',                  'VlanInterfaceController@sflowConfiguredMacs' );

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Nagios
//
Route::group( [ 'prefix' => 'nagios' ], function() {
    Route::get(  'customers/{vlan}/{protocol}',            'NagiosController@customers' );
    Route::post( 'customers/{vlan}/{protocol}',            'NagiosController@customers' );
    Route::get(  'customers/{vlan}/{protocol}/{template}', 'NagiosController@customers' );
    Route::post( 'customers/{vlan}/{protocol}/{template}', 'NagiosController@customers' );

    Route::get(  'switches/{infra}',                        'NagiosController@switches' );
    Route::post( 'switches/{infra}',                        'NagiosController@switches' );
    Route::get(  'switches/{infra}/{template}',             'NagiosController@switches' );
    Route::post( 'switches/{infra}/{template}',             'NagiosController@switches' );

    Route::get(  'birdseye-daemons',                        'NagiosController@birdseyeDaemons');
    Route::post( 'birdseye-daemons',                        'NagiosController@birdseyeDaemons');
    Route::get(  'birdseye-daemons/{template}',             'NagiosController@birdseyeDaemons');
    Route::post( 'birdseye-daemons/{template}',             'NagiosController@birdseyeDaemons');
    Route::get(  'birdseye-daemons/{template}/{vlan}',      'NagiosController@birdseyeDaemons');
    Route::post( 'birdseye-daemons/{template}/{vlan}',      'NagiosController@birdseyeDaemons');

    Route::get(  'birdseye-bgp-sessions/{vlan}/{protocol}/{type}',            'NagiosController@birdseyeBgpSessions');
    Route::post( 'birdseye-bgp-sessions/{vlan}/{protocol}/{type}',            'NagiosController@birdseyeBgpSessions');
    Route::get(  'birdseye-bgp-sessions/{vlan}/{protocol}/{type}/{template}', 'NagiosController@birdseyeBgpSessions');
    Route::post( 'birdseye-bgp-sessions/{vlan}/{protocol}/{type}/{template}', 'NagiosController@birdseyeBgpSessions');
});