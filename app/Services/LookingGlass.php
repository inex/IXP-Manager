<?php

namespace IXP\Services;

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

use Auth;

use IXP\Contracts\LookingGlass as LookingGlassContract;
use IXP\Exceptions\Services\LookingGlass\ConfigurationException;
use IXP\Services\LookingGlass\BirdsEye as BirdseyeLookingGlass;

use IXP\Models\Router;

/**
 * LookingGlass
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   LookingGlass
 * @package    IXP\Services\LookingGlass
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LookingGlass
{
    /**
     * Get a looking glass implementation for a given router
     *
     * @param Router $router
     *
     * @return LookingGlassContract
     *
     * @throws
     */
    public function forRouter( Router $router )
    {
        switch( $router->apiType() ) {
            case Router::API_TYPE_BIRDSEYE:
                $be = new BirdseyeLookingGlass( $router );
                //Birdseye supports caching but if the user is logged in we want to disable that:
                if( Auth::check() ) {
                    $be->setCacheEnabled(false);
                }
                return $be;
                break;
            default:
                throw new ConfigurationException( 'Invalid, no or unimplemented looking glass backend requested: ' . $r->apiType() );
        }
    }
}