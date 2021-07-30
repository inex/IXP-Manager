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

use Former, Redirect;

use Illuminate\Http\{
    RedirectResponse
};

use Illuminate\Support\Facades\View as FacadeView;
use Illuminate\View\View;

use IXP\Http\Requests\StoreRouter;

use IXP\Models\{
    Router,
    Vlan
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Router Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterController extends Controller
{
    /**
     * Display all the routers
     *
     * @return  View
     */
    public function list(): View
    {
        return view( 'router/index' )->with([
            'routers'       => Router::with( 'vlan' )->get()
        ]);
    }

    /**
     * Status page for routers
     *
     * @return  View
     */
    public function status(): View
    {
        $routers    = $routersWithApi = [];
        $lgEnabled  = !config('ixp_fe.frontend.disabled.lg' );

        if( $lgEnabled ) {
            $routers        = Router::all();
            // Get routers with API
            $routersWithApi = Router::whereNotNull( 'api' )
                ->where( 'api_type', '!=', 0 )->get()
                ->pluck( 'handle' )->toArray();
        }

        return view( 'router/status' )->with([
            'routers'        => $routers,
            'routersWithApi' => $routersWithApi,
            'lgEnabled'      => $lgEnabled,
        ]);
    }

    /**
     * Display the form to create a router
     *
     * @return View
     */
    public function create(): View
    {
        return view( 'router/edit' )->with([
            'rt'                => false,
            'vlans'             => Vlan::publicOnly()->orderBy( 'number' )->get()
        ]);
    }

    /**
     * Create a router
     *
     * @param   StoreRouter $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function store( StoreRouter $r ): RedirectResponse
    {
        if( !FacadeView::exists( $r->template ) ) {
            AlertContainer::push( 'The template you entered cannot be found. Please check the help message for more information.', Alert::DANGER );
            return Redirect::to( route( 'router@create' ) )->withInput( $r->all() );
        }

        Router::create( $r->all() );
        AlertContainer::push( 'Router created.', Alert::SUCCESS );
        return Redirect::to( route( "router@list" ) );
    }

    /**
     * Display the form to edit a router
     *
     * @param Router    $router     router that need to be edited
     *
     * @return View
     */
    public function edit( Router $router ): View
    {
        Former::populate([
            'handle'                    => request()->old( 'handle',      $router->handle       ),
            'vlan_id'                   => request()->old( 'vlan_id',     $router->vlan_id      ),
            'protocol'                  => request()->old( 'protocol',    $router->protocol     ),
            'type'                      => request()->old( 'type',        $router->type         ),
            'name'                      => request()->old( 'name',        $router->name         ),
            'shortname'                 => request()->old( 'shortname',   $router->shortname    ),
            'router_id'                 => request()->old( 'router_id',   $router->router_id    ),
            'peering_ip'                => request()->old( 'peering_ip',  $router->peering_ip   ),
            'asn'                       => request()->old( 'asn',         $router->asn          ),
            'software'                  => request()->old( 'software',                    $router->software                  ),
            'software_version'          => request()->old( 'software_version',            $router->software_version          ),
            'operating_system'          => request()->old( 'operating_system',            $router->operating_system          ),
            'operating_system_version'  => request()->old( 'operating_system_version',    $router->operating_system_version  ),
            'mgmt_host'                 => request()->old( 'mgmt_host',         $router->mgmt_host          ),
            'api_type'                  => request()->old( 'api_type',          $router->api_type           ),
            'api'                       => request()->old( 'api',               $router->api                ),
            'lg_access'                 => request()->old( 'lg_access',         $router->lg_access          ),
            'quarantine'                => request()->old( 'quarantine',        $router->quarantine         ),
            'bgp_lc'                    => request()->old( 'bgp_lc',            $router->bgp_lc             ),
            'rpki'                      => request()->old( 'rpki',              $router->rpki               ),
            'rfc1997_passthru'          => request()->old( 'rfc1997_passthru',  $router->rfc1997_passthru   ),
            'skip_md5'                  => request()->old( 'skip_md5',          $router->skip_md5           ),
            'template'                  => request()->old( 'template',          $router->template           ),
        ]);

        return view( 'router/edit' )->with([
            'rt'                => $router,
            'vlans'             => Vlan::publicOnly()->orderBy( 'number' )->get(),
        ]);
    }

    /**
     * Update a router (set all the data needed)
     *
     * @param StoreRouter   $r      instance of the current HTTP request
     * @param Router        $router
     * 
     * @return  RedirectResponse
     *
     * @throws
     */
    public function update( StoreRouter $r, Router $router ): RedirectResponse
    {
        $router->update( $r->all() );
        AlertContainer::push( 'Router updated.', Alert::SUCCESS );
        return Redirect::to( route( "router@list" ) );
    }

    /**
     * Display the details of a router
     *
     * @param  Router    $router        router that need to be displayed
     *
     * @return View
     */
    public function view( Router $router ): View
    {
        return view( 'router/view' )->with([
            'rt'                => $router->load( 'vlan' )
        ]);
    }

    /**
     * Delete a router
     *
     * @param Router $router
     *
     * @return redirectresponse
     *
     * @throws
     */
    public function delete( Router $router): RedirectResponse
    {
        $router->delete();
        AlertContainer::push( 'Router deleted.', Alert::SUCCESS );
        return Redirect::to( route( "router@list" ) );
    }
}