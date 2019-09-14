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

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\View as FacadeView;
use Illuminate\View\View;

use IXP\Http\Requests\StoreRouter;

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

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
    public function list( ): View {
        return view( 'router/index' )->with([
            'routers'       => D2EM::getRepository( RouterEntity::class )->findAll()
        ]);
    }

    /**
     * Status page for routers
     *
     * @return  View
     */
    public function status(): View {

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
     * @param  int    $id        router that need to be edited
     *
     * @return View
     */
    public function edit( int $id = null ): View {

        $rt = false; /** @var RouterEntity $rt */
        if( $id && !( $rt = D2EM::getRepository( RouterEntity::class )->find( $id ) ) ) {
            abort(404, "Unknown Router");
        }

        $old = request()->old();

        if( $rt ) {
            // fill the form with router data
            Former::populate([
                'handle'                => array_key_exists( 'handle',      $old ) ? $old['handle']         : $rt->getHandle(),
                'vlan'                  => array_key_exists( 'vlan',        $old ) ? $old['vlan']           : $rt->getVlan()->getId(),
                'protocol'              => array_key_exists( 'protocol',    $old ) ? $old['protocol']       : $rt->getProtocol(),
                'type'                  => array_key_exists( 'type',        $old ) ? $old['type']           : $rt->getType(),
                'name'                  => array_key_exists( 'name',        $old ) ? $old['name']           : $rt->getName(),
                'shortname'             => array_key_exists( 'shortname',   $old ) ? $old['shortname']      : $rt->getShortName(),
                'router_id'             => array_key_exists( 'router_id',   $old ) ? $old['router_id']      : $rt->getRouterId(),
                'peering_ip'            => array_key_exists( 'peering_ip',  $old ) ? $old['peering_ip']     : $rt->getPeeringIp(),
                'asn'                   => array_key_exists( 'asn',         $old ) ? $old['asn']            : $rt->getAsn(),

                'software'                 => array_key_exists( 'software',                    $old ) ? $old['software']                 : $rt->getSoftware(),
                'software_version'         => array_key_exists( 'software_version',            $old ) ? $old['software_version']         : $rt->getSoftwareVersion(),
                'operating_system'         => array_key_exists( 'operating_system',            $old ) ? $old['operating_system']         : $rt->getOperatingSystem(),
                'operating_system_version' => array_key_exists( 'operating_system_version',    $old ) ? $old['operating_system_version'] : $rt->getOperatingSystemVersion(),

                'mgmt_host'             => array_key_exists( 'mgmt_host',   $old ) ? $old['mgmt_host']      : $rt->getMgmtHost(),
                'api_type'              => array_key_exists( 'api_type',    $old ) ? $old['api_type']       : $rt->getApiType(),
                'api'                   => array_key_exists( 'api',         $old ) ? $old['api']            : $rt->getApi(),
                'lg_access'             => array_key_exists( 'lg_access',   $old ) ? $old['lg_access']      : $rt->getLgAccess(),
                'quarantine'            => array_key_exists( 'quarantine',  $old ) ? $old['quarantine']     : ( $rt->getQuarantine()    ? 1 : 0 ),
                'bgp_lc'                => array_key_exists( 'bgp_lc',      $old ) ? $old['bgp_lc']         : ( $rt->getBgpLc()         ? 1 : 0 ),
                'rpki'                  => array_key_exists( 'rpki',        $old ) ? $old['rpki']           : ( $rt->getRPKI()          ? 1 : 0 ),
                'skip_md5'              => array_key_exists( 'skip_md5',    $old ) ? $old['skip_md5']       : ( $rt->getSkipMd5()       ? 1 : 0 ),
                'template'              => array_key_exists( 'template',    $old ) ? $old['template']       : $rt->getTemplate(),
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
    public function store( StoreRouter $request ): RedirectResponse {

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
    public function view( int $id ): View {
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
     * @param  int    $id        router that need to be deleted
     *
     * @return redirectresponse
     *
     * @throws
     */
    public function delete( int $id ): RedirectResponse {
        /** @var RouterEntity $rt */
        if( !( $rt = D2EM::getRepository( RouterEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        D2EM::remove($rt);
        D2EM::flush();

        AlertContainer::push( 'The router has been successfully deleted.', Alert::SUCCESS );
        return Redirect::to( route( "router@list" ) );
    }

}
