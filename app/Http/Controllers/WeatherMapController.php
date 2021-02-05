<?php

namespace IXP\Http\Controllers;

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

use Illuminate\View\View;

/**
 * WeatherMap Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class WeatherMapController extends Controller
{
    /**
     * Display the weather map
     *
     * @param  int $id ID of the weather map
     *
     * @return  view
     */
    public function index( int $id ): View
    {
        if( !is_numeric( $id ) || !isset( config( 'ixp_tools.weathermap' )[ $id ]  )  ) {
            abort( 404,'Unknown weathermap requested');
        }
        return view( 'weather-map/index' )->with([
            'wm'        => config( 'ixp_tools.weathermap' )[ $id ],
            'wms'       => config( 'ixp_tools.weathermap' ),
        ]);
    }
}