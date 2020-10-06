<?php

namespace IXP\Http\Middleware\Services;

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

use App, Auth, Closure, Route, Validator;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\Http\Request;

use IXP\Models\Router;
use IXP\Models\User;


use IXP\Exceptions\Utils\RouterException;

use IXP\Services\LookingGlass as LookingGlassService;

/**
 * LookingGlass -> MIDDLEWARE
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
     * Check if the symbols is valid
     *
     * @param string $symbol
     */
    private function validateSymbol( string $symbol ): bool
    {
        return $symbol !== '' && preg_match( '/^[a-zA-Z_]?[a-zA-Z0-9_]*$/', $symbol );
    }

    /**
     * Check if the prefix is valid
     *
     * @param Request $request
     *
     * @return bool
     */
    private function validateNetworkRoute( Request $request ): bool
    {
        $validator = Validator::make(
            [
                'net'   => $request->net,
                'mask'  => $request->mask
            ], [
                'net' => 'required|ip',
                'mask' => 'numeric|min:1|max:128',
            ]
        );

        return !$validator->fails();
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle( Request $request, Closure $next )
    {
        if( Route::currentRouteName() === 'lg::index' ) {
            return $next($request);
        }

        // get the router object
        try {
            $router =  Router::whereHandle( $request->handle )->get()->first();

            if( !$router || !$router->api() ) {
                AlertContainer::push( "No router with the provided handle was found", Alert::DANGER );
                return redirect( route( 'lg::index' ) );
            }
        } catch( RouterException $e ) {
            abort( 404, $e->getMessage() );
        }

        if( ( $request->table && !$this->validateSymbol( $request->table ) )
                || ( $request->protocol && !$this->validateSymbol( $request->protocol ) ) ) {
            AlertContainer::push( "Symbol (protocol / table) invalid or not found", Alert::DANGER );
            return redirect( route( 'lg::bgp-sum', [ 'handle' => $request->handle ] ) );
        }

        if( ( $request->net || $request->mask ) && !$this->validateNetworkRoute( $request ) ) {
            abort(404);
        }

        // let's authorise for access (this throws an exception)
        if( !$this->authorise( $router ) ) {
            AlertContainer::push( "Insufficient permissions to access this looking glass", Alert::DANGER );
            return redirect( route( 'lg::index' ) );
        }

        // get the appropriate looking glass service
        // (throws an exception if no appropriate Looking Glass handler)
        $lg = App::make( LookingGlassService::class )->forRouter( $router );

        $request->attributes->add( [ 'lg' => $lg ] );

        return $next( $request );
    }

    /**
     * This function controls access to a router for a looking glass
     *
     * @param Router $router
     *
     * @return bool
     */
    private function authorise( Router $router ): bool
    {
        if( $router->authorise( Auth::check() ? Auth::user()->getPrivs() : User::AUTH_PUBLIC ) ) {
            return true;
        }

        return false;
    }
}