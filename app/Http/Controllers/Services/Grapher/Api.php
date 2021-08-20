<?php

namespace IXP\Http\Controllers\Services\Grapher;

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
use Grapher as GrapherService;

use Illuminate\Http\{
    Request,
    Response
};

use IXP\Contracts\Grapher\Backend as GrapherBackendContract;

use IXP\Http\Controllers\Controller;

use IXP\Services\Grapher;

/**
 * Grapher Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Api extends Controller
{
    /**
     * The grapher service
     *
     * @var Grapher
     */
    private $grapher;

    /**
     * Constructor
     * @param Request           $request
     * @param GrapherService    $grapher
     */
    public function __construct( Request $request, GrapherService $grapher )
    {
        $this->grapher = $grapher;
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

    public function generateConfiguration( Request $request ): Response
    {
        // get the appropriate backend
        $grapher = GrapherService::backend( $request->input( 'backend', 'mrtg' ) );

        if( !$grapher->isConfigurationRequired() ) {
            abort( 404, "This grapher backend (" . $grapher->name() . ") does not require any configuration to be generated" );
        }

        if( !$grapher->isMonolithicConfigurationSupported() ) {
            abort( 404, "This backend ({$grapher->name()}) does not support single configuration files" );
        }

        $config = $grapher->generateConfiguration( GrapherBackendContract::GENERATED_CONFIG_TYPE_MONOLITHIC, $request->input() )[0];


        return (new Response( $config ) )
              ->header('Content-Type', "text/plain" );
    }
}