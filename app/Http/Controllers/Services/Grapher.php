<?php

namespace IXP\Http\Controllers\Services;

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

use Illuminate\Http\{
    Request,
    Response
};

use IXP\Http\Controllers\Controller;

use \IXP\Services\Grapher as GrapherService;
use \IXP\Services\Grapher\Graph;

use IXP\Exceptions\Services\Grapher\GeneralException as GrapherGeneralException;

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
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Grapher extends Controller
{
    /**
     * the grapher service
     *
     * @var GrapherService
     */
    private $grapher;

    /**
     * The graph object
     *
     * @var Graph
     */
    private $graph = null;

    /**
     * The request object
     *
     * @var Request $request
     */
    private $request = null;


    /**
     * Constructor
     *
     * @var Request             $request
     * @var GrapherService      $grapher
     */
    public function __construct( Request $request, GrapherService $grapher )
    {
        $this->grapher = $grapher;
        $this->request = $request;
        // NB: Construtcor happens before middleware...
    }

    /**
     * Grapher accessor
     *
     * @return GrapherService
     */
    private function grapher(): GrapherService
    {
        return $this->grapher;
    }

    /**
     * Graph accessor
     *
     * @return Graph
     *
     * @throws
     */
    private function graph(): Graph
    {
        if( $this->graph === null ) {
            $this->graph = $this->request()->attributes->get('graph');

            // if there's no graph then the middleware went wrong... safety net:
            if( $this->graph === null ) {
                throw new GrapherGeneralException('Middleware could not load graph but did not throw a 404');
            }
        }
        return $this->graph;
    }

    /**
     * Request accessor
     *
     * @return Request
     */
    private function request(): Request
    {
        return $this->request;
    }

    private function simpleResponse( $request ): Response
    {
        return (new Response( call_user_func( [ $this->graph(), $this->graph()->type() ] ) ) )
              ->header('Content-Type', Graph::CONTENT_TYPES[ $this->graph()->type() ] )
              ->header('Content-Disposition', sprintf( 'inline; filename="grapher-%s-%s-%s-%s.%s"',
                    $this->graph()->backend()->name(), $this->graph()->category(),
                    $this->graph()->period(), $this->graph()->protocol(), $this->graph()->type() )
                )
              ->header( 'Expires', Carbon::now()->addMinutes(5)->toRfc1123String() );
    }

    public function ixp( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function infrastructure( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function vlan( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function location( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function switch( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function corebundle( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function trunk( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function physicalInterface( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function virtualInterface( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function customer( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function vlanInterface( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function latency( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }

    public function p2p( Request $request ): Response
    {
        return $this->simpleResponse( $request );
    }
}