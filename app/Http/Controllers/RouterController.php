<?php
/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace IXP\Http\Controllers;

use D2EM, Former, Input, Redirect;

use Entities\{
    Router as RouterEntity,
    Vlan as VlanEntity,
    User as UserEntity
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
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
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
        $routers = D2EM::getRepository( RouterEntity::class )->findAll();

        $routersWithApi = [];
        foreach( $routers as $r ) {
            if( $r->hasApi() ) {
                $routersWithApi[] = $r->getHandle();
            }
        }

        return view( 'router/status' )->with([
            'routers'        => $routers,
            'routersWithApi' => $routersWithApi,
        ]);
    }


    /**
     * Display the form to edit a router
     *
     * @param  int    $id        router that need to be edited
     * @return View
     */
    public function edit( int $id = null ): View {
        /** @var RouterEntity $rt */
        if( $id ) {
            if( !( $rt = D2EM::getRepository( RouterEntity::class )->find( $id ) ) ) {
                abort( 404 );
            }
        } else {
            $rt = new RouterEntity;
        }

        // fill the form with router data
        Former::populate([
            'handle'                => $rt->getHandle(),
            'vlan'                  => $rt->getVlan(),
            'protocol'              => $rt->getProtocol(),
            'type'                  => $rt->getType(),
            'name'                  => $rt->getName(),
            'shortname'             => $rt->getShortName(),
            'router_id'             => $rt->getRouterId(),
            'peering_ip'            => $rt->getPeeringIp(),
            'asn'                   => $rt->getAsn(),
            'software'              => $rt->getSoftware(),
            'mgmt_host'             => $rt->getMgmtHost(),
            'api_type'              => $rt->getApiType(),
            'api'                   => $rt->getApi(),
            'lg_access'             => $rt->getLgAccess(),
            'quarantine'            => $rt->getQuarantine() ? 1 : 0,
            'bgp_lc'                => $rt->getBgpLc() ? 1 : 0,
            'skip_md5'              => $rt->getSkipMd5() ? 1 : 0,
            'template'              => $rt->getTemplate()
        ]);

        Former::open()->rules([
            'handle'                => 'required|string|max:255',
            'name'                  => 'required|string|max:255',
            'shortname'             => 'required|string|max:20',
            'router_id'             => 'required|ipv4',
            'asn'                   => 'required|integer',
            'mgmt_host'             => 'required|string|max:255'
        ]);

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'router/edit' )->with([
            'rt'                => $rt,
            'vlans'             => D2EM::getRepository( VlanEntity::class )->getNames( VlanRepository::TYPE_NORMAL ),
            'protocols'         => RouterEntity::$PROTOCOLS,
            'types'             => RouterEntity::$TYPES,
            'softwares'         => RouterEntity::$SOFTWARES,
            'apiTypes'          => RouterEntity::$API_TYPES,
            'lgAccess'          => UserEntity::$PRIVILEGES_ALL
        ]);
    }



    /**
     * Add or edit a router (set all the data needed)
     *
     * @param   StoreRouter $request instance of the current HTTP request
     * @return  RedirectResponse
     */
    public function store( StoreRouter $request ): RedirectResponse {

        /** @var RouterEntity $rt */
        if( $request->input( 'id' ) && $rt = D2EM::getRepository( RouterEntity::class )->find( $request->input( 'id' ) ) ) {
            if( !$rt ) {
                abort(404, 'Unknown router');
            }
        } else {
            $rt = new RouterEntity;
            D2EM::persist($rt);
        }

        /** @var VlanEntity $vlan */
        if( !( $vlan = D2EM::getRepository( VlanEntity::class )->find( $request->input( 'vlan' ) ) ) ) {
            abort(404, 'Unknown vlan');
        }

        if( !FacadeView::exists( $request->input( 'template' ) ) ) {
            AlertContainer::push( 'The template you entered cannot be found. Please check the help message for more information.', Alert::DANGER );

            return Redirect::to( $request->input( 'id' ) ? 'router/edit/'.$request->input( 'id' ) : 'router/add/' )
                ->withInput( Input::all() );
        }

        $rt->setHandle( $request->input( 'handle' ) );
        $rt->setVlan( $vlan );
        $rt->setProtocol( $request->input( 'protocol' ) );
        $rt->setType( $request->input( 'type' ) );
        $rt->setName( $request->input( 'name' ) );
        $rt->setShortName( $request->input( 'shortname' ) );
        $rt->setRouterId( $request->input( 'router_id' ) );
        $rt->setPeeringIp( $request->input( 'peering_ip' ) );
        $rt->setAsn( $request->input( 'asn' ) );
        $rt->setSoftware( $request->input( 'software' ) );
        $rt->setMgmtHost( $request->input( 'mgmt_host' ) );
        $rt->setApiType( $request->input( 'api_type' ) );
        $rt->setApi( $request->input( 'api' ) );
        $rt->setLgAccess( $request->input( 'lg_access' ) );
        $rt->setQuarantine( ( $request->input( 'quarantine' ) ) ? $request->input( 'quarantine' ) : false );
        $rt->setBgpLc( ( $request->input( 'bgp_lc' ) ) ? $request->input( 'bgp_lc' ) : false );
        $rt->setSkipMd5( ( $request->input( 'skip_md5' ) ) ? $request->input( 'skip_md5' ) : false );
        $rt->setTemplate(  $request->input( 'template' ) ) ;

        D2EM::flush();

        AlertContainer::push( 'Router added/updated successfully.', Alert::SUCCESS );
        return Redirect::to( 'router/list');
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
            abort(404);
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
     * @return redirectresponse
     */
    public function delete( int $id ): RedirectResponse {
        /** @var RouterEntity $rt */
        if( !( $rt = D2EM::getRepository( RouterEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        D2EM::remove($rt);
        D2EM::flush();

        AlertContainer::push( 'The router has been successfully deleted.', Alert::SUCCESS );
        return Redirect::to( 'router/list');
    }

}
