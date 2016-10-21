<?php

namespace IXP\Http\Controllers\Services\Grapher;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use IXP\Http\Requests;
use IXP\Http\Controllers\Controller;

use Grapher as GrapherService;

use Carbon\Carbon;

/**
 * Grapher Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Api extends Controller
{
    /**
     * the grapher service
     * @var \IXP\Services\Grapher
     */
    private $grapher;

    /**
     * Constructor
     */
    public function __construct( Request $request, GrapherService $grapher ) {

        // FIXME tmp barryo
        if( isset($_SERVER['REMOTE_ADDR'] ) && !in_array( $_SERVER['REMOTE_ADDR'], config('ixp_tools.safe_ips' ) ) ) {
            abort(403, 'Unauthorised access.');
        }

        $this->grapher = $grapher;
    }

    /**
     * Grapher accessor
     * @return \IXP\Services\Grapher
     */
    private function grapher(): GrapherService {
        return $this->grapher;
    }

    public function generateConfiguration( Request $request ): Response {

        $grapher = GrapherService::backend( 'mrtg' );
        if( !$grapher->isConfigurationRequired() ) {
            abort( 404, "This grapher backend (" . $grapher->name() . ") does not require any configuration to be generated" );
        }

        if( !$grapher->isMonolithicConfigurationSupported() ) {
            abort( 404, "This backend ({$grapher->name()}) does not support single configuration files" );
        }

        $config = $grapher->generateConfiguration( d2r( 'IXP' )->getDefault() )[0];


        return (new Response( $config ) )
              ->header('Content-Type', "text/plain" );
    }

}
