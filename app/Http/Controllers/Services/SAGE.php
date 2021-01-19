<?php
declare(strict_types=1);

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

use Auth, D2EM;

use Laravel\Socialite\Facades\Socialite;
use Entities\{
    Router as RouterEntity
};

use ErrorException;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

use IXP\Contracts\LookingGlass as LookingGlassContract;

use IXP\Exceptions\Services\LookingGlass\GeneralException as LookingGlassGeneralException;

use IXP\Http\Controllers\Controller;


/**
 * SAGE Accounting Controller
 *
 * *** INEX INTERNAL TESTING ***
 * *** INEX INTERNAL TESTING ***
 * *** INEX INTERNAL TESTING ***
 * *** INEX INTERNAL TESTING ***
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   LookingGlass
 * @package    IXP\Services\SAGE
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SAGE extends Controller
{

    public function login()
    {
        return Socialite::driver('sage')->redirect();
    }

    public function callback()
    {
        return view( 'services/sage/index', [ 'user' => Socialite::driver('sage')->user() ] );
    }


}
