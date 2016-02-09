<?php

namespace IXP\Http\Controllers\Services;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Limited.
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

use \IXP\Services\Grapher as GrapherService;
use \IXP\Services\Grapher\Graph;

use Carbon\Carbon;

/**
 * Grapher Controller
 *
 * *************************************************
 * ***********      SECURITY NOTICE      ***********
 * *************************************************
 *
 * IF WE GET TO THIS CONTROLLER, WE CAN ASSUME THE
 * REQUEST HAS BEEN VALIDATED AND VERIFIED.
 *
 * THE GRAPHER MIDDLEWARE IS RESPONSIBLE FOR
 * SECURITY AND PARAMETER CHECKS
 *
 * *************************************************
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Grapher extends Controller
{
    /**
     * the grapher service
     * @var \IXP\Services\Grapher
     */
    private $grapher;

    /**
     * The graph object
     * @var \IXP\Services\Grapher\Graph
     */
    private $graph;


    /**
     * Constructor
     */
    public function __construct( Request $request, GrapherService $grapher ) {
        $this->grapher = $grapher;
        $this->graph  = $request->attributes->get('graph');
    }

    /**
     * Grapher accessor
     * @return \IXP\Services\Grapher
     */
    private function grapher(): GrapherService {
        return $this->grapher;
    }

    /**
     * Graph accessor
     * @return \IXP\Services\Grapher\Graph
     */
    private function graph(): Graph {
        return $this->graph;
    }


    //public function ixp( int $id, string $period, string $category, string $protocol, string $type, Request $request, string $backend = null ): Response {
    public function ixp( Request $request ): Response {

        return (new Response( $this->graph()->png() ) )
              ->header('Content-Type', 'image/png' )
              ->header( 'Expires', Carbon::now()->addMinutes(5)->toRfc1123String() );

    }
}
