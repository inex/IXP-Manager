<?php

namespace IXP\Http\Controllers;

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

use D2EM, Former, Redirect;


use Entities\{
    Router as RouterEntity,
    Vlan as VlanEntity
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\Support\Facades\View as FacadeView;
use Illuminate\View\View;

use IXP\Http\Requests\StoreRouter;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Repositories\Vlan as VlanRepository;

/**
 * Router Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterController extends Controller
{

    /**
     * Display all the routers
     *
     * @return  View
     */
    public function list( ): View
    {
        return view( 'router/index' )->with([
            'routers'       => D2EM::getRepository( RouterEntity::class )->findAll()
        ]);
    }

    /**
     * Status page for routers
     *
     * @return  View
     */
    public function status(): View
    {
        /** @var RouterEntity[] $routers */
        $routers        = [];
        $routersWithApi = [];
        $lgEnabled      = !config('ixp_fe.frontend.disabled.lg' );

        if( $lgEnabled ) {
            $routers = D2EM::getRepository( RouterEntity::class )->findAll();
            foreach( $routers as $r ) {
                if( $r->hasApi() ) {
                    $routersWithApi[] = $r->getHandle();
                }
            }
        }

        return view( 'router/status' )->with([
            'routers'        => $routers,
            'routersWithApi' => $routersWithApi,
            'lgEnabled'      => $lgEnabled,
        ]);
    }


    /**
     * Display the form to edit a router
     *
     * @param Request $request
     * @param int $id router that need to be edited
     *
     * @return View
     */
    public function edit( Request $request, int $id = null ): View
    {
        $rt = false; /** @var RouterEntity $rt */
        if( $id && !( $rt = D2EM::getRepository( RouterEntity::class )->find( $id ) ) ) {
            abort(404, "Unknown Router");
        }

        if( $rt ) {
            // fill the form with router data
            Former::populate([
                'handle'                => request()->old( 'handle',      $rt->getHandle() ),
                'vlan'                  => request()->old( 'vlan',        $rt->getVlan()->getId() ),
                'protocol'              => request()->old( 'protocol',    $rt->getProtocol() ),
                'type'                  => request()->old( 'type',        $rt->getType() ),
                'name'                  => request()->old( 'name',        $rt->getName() ),
                'shortname'             => request()->old( 'shortname',   $rt->getShortName() ),
                'router_id'             => request()->old( 'router_id',   $rt->getRouterId() ),
                'peering_ip'            => request()->old( 'peering_ip',  $rt->getPeeringIp() ),
                'asn'                   => request()->old( 'asn',         $rt->getAsn() ),

                'software'                 => request()->old( 'software',                    $rt->getSoftware() ),
                'software_version'         => request()->old( 'software_version',            $rt->getSoftwareVersion() ),
                'operating_system'         => request()->old( 'operating_system',            $rt->getOperatingSystem() ),
                'operating_system_version' => request()->old( 'operating_system_version',    $rt->getOperatingSystemVersion() ),

                'mgmt_host'             => request()->old( 'mgmt_host',   $rt->getMgmtHost() ),
                'api_type'              => request()->old( 'api_type',    $rt->getApiType() ),
                'api'                   => request()->old( 'api',         $rt->getApi() ),
                'lg_access'             => request()->old( 'lg_access',   $rt->getLgAccess() ),
                'quarantine'            => request()->old( 'quarantine',  ( $rt->getQuarantine()    ? 1 : 0 ) ),
                'bgp_lc'                => request()->old( 'bgp_lc',      ( $rt->getBgpLc()         ? 1 : 0 ) ),
                'rpki'                  => request()->old( 'rpki',        ( $rt->getRPKI()          ? 1 : 0 ) ),
                'rfc1997_passthru'      => request()->old( 'rfc1997_passthru', ( $rt->getRFC1997Passthru() ? 1 : 0 ) ),
                'skip_md5'              => request()->old( 'skip_md5',    ( $rt->getSkipMd5()       ? 1 : 0 ) ),
                'template'              => request()->old( 'template',    $rt->getTemplate() ),
            ]);
        }

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'router/edit' )->with([
            'rt'                => $rt,
            'vlans'             => D2EM::getRepository( VlanEntity::class )->getNames( VlanRepository::TYPE_NORMAL ),
        ]);
    }



    /**
     * Add or edit a router (set all the data needed)
     *
     * @param   StoreRouter $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function store( StoreRouter $request ): RedirectResponse
    {
        $isEdit = $request->input( 'id' ) ? true : false;

        /** @var RouterEntity $rt */
        if( $request->input( 'id' ) && $rt = D2EM::getRepository( RouterEntity::class )->find( $request->input( 'id' ) ) ) {
            if( !$rt ) {
                abort(404, 'Unknown router');
            }
        } else {
            $rt = new RouterEntity;
            D2EM::persist($rt);
        }

        if( !FacadeView::exists( $request->input( 'template' ) ) ) {
            AlertContainer::push( 'The template you entered cannot be found. Please check the help message for more information.', Alert::DANGER );

            return Redirect::to( $isEdit ? route( "router@edit", [ "id" => $request->input( 'id' ) ] ) : route( "router@add" ) )
                ->withInput( $request->all() );
        }

        $rt->setHandle(     $request->input( 'handle'       ) );
        $rt->setProtocol(   $request->input( 'protocol'     ) );
        $rt->setType(       $request->input( 'type'         ) );
        $rt->setName(       $request->input( 'name'         ) );
        $rt->setShortName(  $request->input( 'shortname'    ) );
        $rt->setRouterId(   $request->input( 'router_id'    ) );
        $rt->setPeeringIp(  $request->input( 'peering_ip'   ) );
        $rt->setAsn(        $request->input( 'asn'          ) );

        $rt->setSoftware(               $request->input( 'software'                 ) );
        $rt->setSoftwareVersion(        $request->input( 'software_version'         ) );
        $rt->setOperatingSystem(        $request->input( 'operating_system'         ) );
        $rt->setOperatingSystemVersion( $request->input( 'operating_system_version' ) );


        $rt->setMgmtHost(   $request->input( 'mgmt_host'    ) );
        $rt->setApiType(    $request->input( 'api_type'     ) );
        $rt->setApi(        $request->input( 'api'          ) );
        $rt->setLgAccess(   $request->input( 'lg_access'    ) );
        $rt->setTemplate(   $request->input( 'template'     ) );

        $rt->setVlan(       D2EM::getRepository( VlanEntity::class )->find( $request->input( 'vlan' ) ) );

        $rt->setQuarantine(( $request->input( 'quarantine'  ) ) ? $request->input( 'quarantine'     ) : 0 );
        $rt->setBgpLc(       ( $request->input( 'bgp_lc'       ) ) ? $request->input( 'bgp_lc'         ) : 0 );
        $rt->setRPKI(        ( $request->input( 'rpki'         ) ) ? $request->input( 'rpki'           ) : 0 );
        $rt->setRFC1997Passthru( ( $request->input( 'rfc1997_passthru' ) ) ? $request->input( 'rfc1997_passthru' ) : 0 );
        $rt->setSkipMd5(   ( $request->input( 'skip_md5'     ) ) ? $request->input( 'skip_md5'       ) : 0 );

        D2EM::flush();

        AlertContainer::push( 'Router added/updated successfully.', Alert::SUCCESS );

        return Redirect::to( route( "router@list" ) );
    }


    /**
     * Display the details of a router
     *
     * @param  int    $id        router that need to be displayed
     * @return View
     */
    public function view( int $id ): View
    {
        /** @var RouterEntity $rt */
        if( !( $rt = D2EM::getRepository( RouterEntity::class )->find( $id ) ) ) {
            abort(404 , 'Unknown router' );
        }

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'router/view' )->with([
            'rt'                => $rt
        ]);
    }

    /**
     * Delete a router
     *
     * @param Request $request
     *
     * @return redirectresponse
     *
     */
    public function delete( Request $request ): RedirectResponse
    {
        /** @var RouterEntity $rt */
        if( !( $rt = D2EM::getRepository( RouterEntity::class )->find( $request->input( "id" ) ) ) ) {
            abort(404);
        }

        D2EM::remove( $rt );
        D2EM::flush();

        AlertContainer::push( 'The router has been successfully deleted.', Alert::SUCCESS );

        return Redirect::to( route( "router@list" ) );
    }

}
