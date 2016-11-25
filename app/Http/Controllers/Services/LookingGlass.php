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
use Illuminate\View\View;

use IXP\Contracts\LookingGlass as LookingGlassContract;

use IXP\Exceptions\Services\LookingGlass\GeneralException as LookingGlassGeneralException;

use IXP\Http\Requests;
use IXP\Http\Controllers\Controller;



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
     * the LookingGlass
     * @var \IXP\Contracts\LookingGlass
     */
    private $lg = null;

    /**
     * The request object
     * @var Illuminate\Http\Request $request
     */
    private $request = null;


    /**
     * Constructor
     */
    public function __construct( Request $request ) {
        // NB: Construtcor happens before middleware...
        $this->request = $request;
    }

    /**
     * Looking glass accessor
     * @return \IXP\Contracts\LookingGlass
     */
    private function lg(): LookingGlassContract {
        if( $this->lg === null ){
            $this->lg = $this->request()->attributes->get('lg');

            // if there's no graph then the middleware went wrong... safety net:
            if( $this->lg === null ){
                throw new LookingGlassGeneralException('Middleware could not load looking glass but did not throw a 404');
            }
        }
        return $this->lg;
    }

    /**
     * Request accessor
     * @return Illuminate\Http\Request
     */
    private function request(): Request {
        return $this->request;
    }

    public function bgpSummary( string $handle ): View {
        // get bgp protocol summary
        return app()->make('view')->make('services/lg/bgp-summary')->with([
            'content' => json_decode( $this->lg()->bgpSummary() ),
            'lg'      => $this->lg(),
        ]);
    }

}
