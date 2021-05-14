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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Illuminate\Routing\Controller as BaseController;

use IXP\Models\{
    Customer,
    User
};

/**
 * Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Checks if reseller mode is enabled.
     *
     * To enable reseller mode set the env variable IXP_RESELLER_ENABLED
     *
     * @see http://docs.ixpmanager.org/features/reseller/
     *
     * @return bool
     */
    protected function resellerMode(): bool
    {
        return (bool)config( 'ixp.reseller.enabled', false );
    }

    /**
     * Checks if as112 is activated in the UI.
     *
     * To disable as112 in the UI set the env variable IXP_AS112_UI_ACTIVE
     *
     * @see http://docs.ixpmanager.org/features/as112/
     *
     * @return bool
     */
    protected function as112UiActive(): bool
    {
        return (bool)config( 'ixp.as112.ui_active', false );
    }

    /**
     * Checks if logo management is enabled
     *
     * To enable logos in the UI set IXP_FE_FRONTEND_DISABLED_LOGO=false in .env
     *
     * @return bool
     */
    protected function logoManagementEnabled(): bool
    {
        return !(bool)config( 'ixp_fe.frontend.disabled.logo' );
    }

    /**
     * Try to get the clients real IP address even when behind a proxy.
     *
     * Source: https://stackoverflow.com/questions/33268683/how-to-get-client-ip-address-in-laravel-5/41769505#41769505
     * @return string|null
     */
    protected function getIp(): ?string
    {
        foreach( [ 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ] as $key ) {
            if( array_key_exists( $key, $_SERVER ) === true ) {
                foreach( explode(',', $_SERVER[$key] ) as $ip ) {
                    $ip = trim($ip);
                    if( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                        return $ip;
                    }
                }
            }
        }
        return request()->getClientIp();
    }

    /**
     * Return the list of privileges for the dropdown depending on the logged in user priv and if the customer is internal or not
     *
     * @return array List of privileges
     *
     * @throws
     */
    protected function getAllowedPrivs(): array
    {
        $privs          = User::$PRIVILEGES_TEXT_NONSUPERUSER;
        $isSuperUser    = Auth::getUser()->isSuperUser();

        // If we add a user via the customer overview users list
        if( request()->custid && request()->is( 'user/create*' ) ) {
            if( ( $c = Customer::find( request()->custid ) ) ) {
                // Internal customer and SuperUser
                if( $c->typeInternal() && $isSuperUser ){
                    $privs = User::$PRIVILEGES_TEXT;
                }
            }
        }elseif( $isSuperUser && ( request()->is( 'user/create*' ) || request()->is( 'customer-to-user/create*' )  ) ) {
            $privs = User::$PRIVILEGES_TEXT;
        }
        return $privs;
    }
}