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

use Auth, ErrorException;

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;
use Illuminate\Http\{
    RedirectResponse,
    Request,
    Response
};

use Illuminate\Routing\Redirector;

use Illuminate\View\View;

use IXP\Contracts\LookingGlass as LookingGlassContract;

use IXP\Exceptions\Services\LookingGlass\GeneralException as LookingGlassGeneralException;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    Aggregators\RouterAggregator,
    Customer,
    User
};

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
 * @author     Barry O'Donovan   <barry@islandbridgenetworks.ie>
 * @author     Yann Robin        <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\LookingGlass
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LookingGlass extends Controller
{
    /**
     * the LookingGlass
     *
     * @var LookingGlassContract
     */
    private $lg = null;

    /**
     * The request object
     *
     * @var Request $request
     */
    private $request = null;

    /**
     * Constructor
     *
     * @param Request $request
     */
    public function __construct( Request $request )
    {
        // NB: Constructor happens before middleware...
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
    private function request(): Request
    {
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
        $cust = Auth::check() ? Customer::find( Auth::getUser()->custid ) : null;
        $user = Auth::check() ? User::find( Auth::id() ) : null;

        $view->with( 'status',      json_decode( $this->lg()->status(), false, 512, JSON_THROW_ON_ERROR));
        $view->with( 'lg',          $this->lg() );
        $view->with( 'routers',     RouterAggregator::forDropdown( $cust, $user ) );
        $view->with( 'tabRouters',  RouterAggregator::forTab( $cust, $user ) );
        return $view;
    }

    /**
     * Index page
     *
     * @return View
     *
     * @throws
     */
    public function index(): View
    {
        $cust = Auth::check() ? Customer::find( Auth::getUser()->custid ) : null;
        $user = Auth::check() ? User::find( Auth::id() ) : null;

        return view('services/lg/index' )->with( [
            'lg'            => false,
            'routers'       => RouterAggregator::forDropdown( $cust, $user ),
            'tabRouters'    => RouterAggregator::forTab( $cust, $user )
        ] );
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
        $view = view('services/lg/bgp-summary' )->with([
            'content' => json_decode( $this->lg()->bgpSummary(), false, 512, JSON_THROW_ON_ERROR),
        ]);

        return $this->addCommonParams( $view );
    }

    /**
     * @param string $handle
     * @param string $table
     *
     * @return RedirectResponse|Redirector|View
     *
     * @throws
     */
    public function routesForTable( string $handle, string $table )
    {
        $tooManyRoutesMsg = "The routing table <code>{$table}</code> has too many routes to display in the web interface. Please use "
            . "<a href=\"" . route( 'lg::route-search', [ 'handle' => $this->lg()->router()->handle ] )
            . "\">the route search tool</a> to query this table.";

        try{
            $routes = $this->lg()->routesForTable( $table );
        } catch( ErrorException $e ) {
            if( strpos( $e->getMessage(), 'HTTP/1.0 403' ) !== false ) {
                return redirect( 'lg/' . $handle )->with( 'msg', $tooManyRoutesMsg );
            }
            return redirect( 'lg/' . $handle )->with('msg', 'An error occurred - please contact our support team if you wish.' );
        }

        if( $routes === "" ) {
            return redirect( 'lg/' . $handle )->with( 'msg', $tooManyRoutesMsg );
        }

        $view = view('services/lg/routes' )->with([
            'content'   => json_decode($routes, false, 512, JSON_THROW_ON_ERROR),
            'source'    => 'table', 'name' => $table
        ]);

        return $this->addCommonParams( $view );
    }

    /**
     * @param string $handle
     * @param string $protocol
     *
     * @return View|RedirectResponse
     *
     * @throws
     */
    public function routesForProtocol( string $handle, string $protocol )
    {
        try{
            // get bgp protocol summary
            $view = view('services/lg/routes' )->with([
                'content' => json_decode( $this->lg()->routesForProtocol( $protocol ), false, 512, JSON_THROW_ON_ERROR),
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
    public function routesForExport( string $handle, string $protocol ): View
    {
        // get bgp protocol summary
        $view = view('services/lg/routes' )->with([
            'content'   => json_decode( $this->lg()->routesForExport( $protocol ), false, 512, JSON_THROW_ON_ERROR),
            'source'    => 'export to protocol',
            'name'      => $protocol
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
    public function routeProtocol( string $handle, string $network, string $mask, string $protocol ): View
    {
        return view('services/lg/route' )->with([
            'content' => json_decode($this->lg()->protocolRoute($protocol, $network, (int) $mask), false, 512,
                JSON_THROW_ON_ERROR),
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
        return view('services/lg/route')->with( [
            'content' => json_decode( $this->lg()->protocolTable( $table, $network, (int)$mask ), false ),
            'source'  => 'table',
            'name'    => $table,
            'lg'      => $this->lg(),
            'net'     => urldecode($network . '/' . $mask),
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
    public function routeExport( string $handle, string $network, string $mask, string $protocol ): View
    {
        return view('services/lg/route' )->with([
            'content'   => json_decode( $this->lg()->exportRoute( $protocol, $network, (int)$mask ), false ),
            'source'    => 'export',
            'name'      => $protocol,
            'lg'        => $this->lg(),
            'net'       => urldecode( $network . '/' . $mask ),
        ]);
    }

    /**
     * @param string $handle
     *
     * @return View
     *
     * @throws
     */
    public function routeSearch( string $handle ): View
    {
        $view = view('services/lg/route-search' )->with( [
            'content' => json_decode( $this->lg()->symbols(), false ),
        ]);
        return $this->addCommonParams( $view );
    }
}