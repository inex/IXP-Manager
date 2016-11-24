<?php
declare(strict_types=1);

namespace IXP\Http\Controllers\Services;

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

use \IXP\Services\LookingGlass as LookingGlassService;


/**
 * LookingGlass Controller
 *
 * *************************************************
 * ***********      SECURITY NOTICE      ***********
 * *************************************************
 *
 * IF WE GET TO THIS CONTROLLER, WE CAN ASSUME THE
 * REQUEST HAS BEEN VALIDATED AND VERIFIED.
 *
 * THE LookingGlass MIDDLEWARE IS RESPONSIBLE FOR
 * SECURITY AND PARAMETER CHECKS
 *
 * *************************************************
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   LookingGlass
 * @package    IXP\Services\LookingGlass
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LookingGlass extends Controller
{
    /**
     * the LookingGlass service
     * @var \IXP\Services\LookingGlass
     */
    private $lg;

    /**
     * The request object
     * @var Illuminate\Http\Request $request
     */
    private $request = null;


    /**
     * Constructor
     */
    public function __construct( Request $request, LookingGlassService $lg ) {
        // NB: Construtcor happens before middleware...
        $this->lg      = $lg;
        $this->request = $request;
    }

    /**
     * Grapher accessor
     * @return \IXP\Services\Grapher
     */
    private function lg(): LookingGlassService {
        return $this->lg;
    }


    /**
     * Request accessor
     * @return Illuminate\Http\Request
     */
    private function request(): Request {
        return $this->request;
    }


    private function simpleResponse( $request ): Response {
        return (new Response( call_user_func( [ $this->graph(), $this->graph()->type() ] ) ) )
              ->header('Content-Type', Graph::CONTENT_TYPES[ $this->graph()->type() ] )
              ->header('Content-Disposition', sprintf( 'inline; filename="grapher-%s-%s-%s-%s.%s"',
                    $this->graph()->backend()->name(), $this->graph()->category(),
                    $this->graph()->period(), $this->graph()->protocol(), $this->graph()->type() )
                )
              ->header( 'Expires', Carbon::now()->addMinutes(5)->toRfc1123String() );
    }

    public function bgpSummary( Request $request, string $handle ): Response {
        dd($request);
        //return $this->simpleResponse( $request );
        die('ss');
    }

}
