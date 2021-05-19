<?php
declare(strict_types=1);

namespace IXP\Http\Controllers\Services;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;
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
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LookingGlass extends Controller
{
    /**
     * the LookingGlass
     * @var LookingGlassContract
     */
    private $lg = null;

    /**
     * The request object
     * @var Request $request
     */
    private $request = null;


    /**
     * Constructor
     * @param Request $request
     */
    public function __construct( Request $request )
    {
        // NB: Construtcor happens before middleware...
        $this->request = $request;
    }

    /**
     * Looking glass accessor
     *
     * @return LookingGlassContract
     *
     * @throws
     */
    private function lg(): LookingGlassContract
    {
        if( $this->lg === null ) {
            $this->lg = $this->request()->attributes->get('lg' );

            // if there's no graph then the middleware went wrong... safety net:
            if( $this->lg === null ) {
                throw new LookingGlassGeneralException('Middleware could not load looking glass but did not throw a 404' );
            }
        }
        return $this->lg;
    }

    /**
     * Request accessor
     *
     * @return Request
     */
    private function request(): Request {
        return $this->request;
    }

    /**
     * Add view parameters common for all requests.
     *
     * @param View $view
     *
     * @return View
     *
     * @throws
     */
    private function addCommonParams( View $view ): View
    {
        $view->with( 'status',  json_decode( $this->lg()->status() ) );
        $view->with( 'lg',      $this->lg() );
        $view->with( 'routers', D2EM::getRepository( RouterEntity::class )->makeRouterDropdown(
            Auth::check() ? Auth::user()->getCustomer() : null, Auth::check() ? Auth::user() : null ) );
        $view->with( 'tabRouters', D2EM::getRepository( RouterEntity::class )->makeRouterTab(
            Auth::check() ? Auth::user()->getCustomer() : null, Auth::check() ? Auth::user() : null ) );
        return $view;
    }


    public function index(): View
    {
        return app()->make('view' )->make('services/lg/index' )->with([
            'lg'            => false,
            'routers'       => D2EM::getRepository( RouterEntity::class )->makeRouterDropdown(
                Auth::check() ? Auth::user()->getCustomer() : null, Auth::check() ? Auth::user() : null ),
            'tabRouters'    => D2EM::getRepository( RouterEntity::class )->makeRouterTab(
                Auth::check() ? Auth::user()->getCustomer() : null, Auth::check() ? Auth::user() : null )

        ]);
    }

    /**
     * Returns the router's status as JSON
     *
     * @param string $handle
     *
     * @return Response JSON of status
     *
     * @throws
     */
    public function status( string $handle ): Response
    {
        // get the router status
        return response()
            ->make( $this->lg()->status() )
            ->header('Content-Type', 'application/json' );
    }

    /**
     * Returns the router's "bgp summary" as JSON
     *
     * @param string $handle
     * @return Response JSON of status
     *
     * @throws
     */
    public function bgpSummaryApi( string $handle ): Response
    {
        // get the router status
        return response()
            ->make( $this->lg()->bgpSummary() )
            ->header('Content-Type', 'application/json');
    }

    /**
     * @param string $handle
     *
     * @return View
     *
     * @throws
     */
    public function bgpSummary(string $handle ): View
    {
        // get bgp protocol summary
        $view = app()->make('view' )->make('services/lg/bgp-summary' )->with([
            'content' => json_decode( $this->lg()->bgpSummary() ),
        ]);

        return $this->addCommonParams( $view );
    }

    /**
     * @param string $handle
     * @param string $table
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     * @throws
     */
    public function routesForTable( string $handle, string $table )
    {
        $tooManyRoutesMsg = "The routing table <code>{$table}</code> has too many routes to display in the web interface. Please use "
            . "<a href=\"" . route( 'lg::route-search', [ 'handle' => $this->lg()->router()->handle() ] )
            . "\">the route search tool</a> to query this table.";

        try{
            $routes = $this->lg()->routesForTable($table);
        } catch( ErrorException $e ) {
            if( strpos( $e->getMessage(), 'HTTP/1.0 403' ) !== false ) {
                return redirect( 'lg/'.$handle )->with( 'msg', $tooManyRoutesMsg );
            }
            return redirect( 'lg/'.$handle )->with('msg', 'An error occurred - please contact our support team if you wish.' );
        }

        if( $routes === "" ) {
            return redirect( 'lg/'.$handle )->with( 'msg', $tooManyRoutesMsg );
        }

        $view = app()->make('view')->make('services/lg/routes')->with([
            'content' => json_decode( $routes ),
            'source' => 'table', 'name' => $table
        ]);
        return $this->addCommonParams($view);
    }

    /**
     * @param string $handle
     * @param string $protocol
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function routesForProtocol( string $handle, string $protocol )
    {
        try{
            // get bgp protocol summary
            $view = app()->make('view' )->make('services/lg/routes' )->with([
                'content' => json_decode( $this->lg()->routesForProtocol($protocol) ),
                'source' => 'protocol', 'name' => $protocol
            ]);
            return $this->addCommonParams( $view );
        } catch( \Exception $e ){
            AlertContainer::push( 'The available resource is not available. Most likely the amount of routes exceed the APIs configured maximum threshold.', Alert::DANGER );
            return redirect( route( "lg::bgp-sum", [ 'handle' => $handle ] ) );
        }
    }

    /**
     * @param string $handle
     * @param string $protocol
     *
     * @return View
     *
     * @throws
     */
    public function routesForExport(string $handle, string $protocol ): View
    {
        // get bgp protocol summary
        $view = app()->make('view' )->make('services/lg/routes' )->with([
            'content' => json_decode( $this->lg()->routesForExport( $protocol ) ),
            'source' => 'export to protocol', 'name' => $protocol
        ]);
        return $this->addCommonParams( $view );
    }


    /**
     * @param string $handle
     * @param string $network
     * @param string $mask
     * @param string $protocol
     *
     * @return View
     *
     * @throws
     */
    public function routeProtocol(string $handle, string $network, string $mask, string $protocol ): View
    {
        return app()->make('view' )->make('services/lg/route' )->with([
            'content' => json_decode( $this->lg()->protocolRoute( $protocol, $network, intval( $mask ) ) ),
            'source'  => 'protocol',
            'name'    => $protocol,
            'lg'      => $this->lg(),
            'net' => urldecode( $network.'/'.$mask ),
        ]);
    }

    /**
     * @param string $handle
     * @param string $network
     * @param string $mask
     * @param string $table
     *
     * @return View
     *
     * @throws
     */
    public function routeTable( string $handle, string $network, string $mask, string $table ): View
    {
        return app()->make('view')->make('services/lg/route')->with([
            'content' => json_decode( $this->lg()->protocolTable($table,$network,intval($mask)) ),
            'source'  => 'table',
            'name'    => $table,
            'lg'      => $this->lg(),
            'net'     => urldecode($network.'/'.$mask),
        ]);
    }

    /**
     * @param string $handle
     * @param string $network
     * @param string $mask
     * @param string $protocol
     *
     * @return View
     *
     * @throws
     */
    public function routeExport(string $handle, string $network, string $mask, string $protocol ): View
    {
        return app()->make('view' )->make('services/lg/route' )->with([
            'content' => json_decode( $this->lg()->exportRoute( $protocol, $network, (int)$mask ) ),
            'source'  => 'export',
            'name'    => $protocol,
            'lg'      => $this->lg(),
            'net' => urldecode( $network.'/'.$mask ),
        ]);
    }


    /**
     * @param string $handle
     *
     * @return View
     *
     * @throws
     */
    public function routeSearch(string $handle ): View
    {
        $view = app()->make('view' )->make('services/lg/route-search' )->with( [
            'content' => json_decode( $this->lg()->symbols() ),
        ]);

        return $this->addCommonParams( $view );
    }

}
