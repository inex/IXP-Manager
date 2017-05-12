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

use D2EM, Redirect, Former, Input;

use Illuminate\View\View;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use IXP\Http\Controllers\Controller;

use Entities\{
    PhysicalInterface as PhysicalInterfaceEntity,
    Switcher as SwitcherEntity,
    SwitchPort as SwitchPortEntity,
    VirtualInterface as VirtualInterfaceEntity
};

use IXP\Http\Requests\{
    StorePhysicalInterface
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};



/**
 * PhysicalInterface Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PhysicalInterfaceController extends Controller
{
    /**
     * Display all the physicalInterfaces
     *
     * @return  View
     */
    public function list( int $id = null ): View {
        return view( $id ? 'physical-interface/view' : 'physical-interface/index' )->with([
            'listPi'       => D2EM::getRepository( PhysicalInterfaceEntity::class )->getForList( $id ) // can we change that function by find and create the object function to get all the informations needed ?
        ]);
    }

    /**
     * Display the form to edit a physical interface
     *
     * @return View
     */
    public function edit( int $id, int $viid = null ): View {
        $pi = false;
        /** @var PhysicalInterfaceEntity $pi */
        if( $id and !( $pi = D2EM::getRepository( PhysicalInterfaceEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        $vi = false;

        if( $viid and !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $viid ) ) ) {
            abort(404);
        }

        if( $pi ) {
            // fill the form with physical interface data
            Former::populate([
                'switch'                  => $pi->getSwitchPort()->getSwitcher()->getId(),
                'switch-port'             => $pi->getSwitchPort()->getId(),
                'status'                  => $pi->getStatus(),
                'speed'                   => $pi->getSpeed(),
                'duplex'                  => $pi->getDuplex(),
                'autoneg-label'           => $pi->getAutoneg() ? 1 : 0,
                'monitorindex'            => $pi->getMonitorindex() ? $pi->getMonitorindex() : D2EM::getRepository( PhysicalInterfaceEntity::class )->getNextMonitorIndex( $pi->getVirtualInterface()->getCustomer() ) ,
                'notes'                   => $pi->getNotes()
            ]);
        }


        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'physical-interface/edit' )->with([
            'switches'              => D2EM::getRepository( SwitcherEntity::class )->getNames( ),
            'sp'                    => $pi ? D2EM::getRepository( SwitcherEntity::class )->getAllPorts( $pi->getSwitchPort()->getSwitcher()->getId() ) : '',
            'status'                => PhysicalInterfaceEntity::$STATES,
            'speed'                 => PhysicalInterfaceEntity::$SPEED,
            'duplex'                => PhysicalInterfaceEntity::$DUPLEX,
            'pi'                    => $pi,
            'vi'                    => $vi,
        ]);
    }

    /**
     * Edit a physical interface (set all the data needed)
     *
     * @param   StorePhysicalInterface $request instance of the current HTTP request
     * @return  RedirectResponse
     */
    public function store( StorePhysicalInterface $request ): RedirectResponse {

        if( $request->input( 'id', false ) ) {
            /** @var PhysicalInterfaceEntity $pi */
            if( !( $pi = D2EM::getRepository( PhysicalInterfaceEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort( 404, 'Unknown physical interface' );
            }
        } else {
            $pi = new PhysicalInterfaceEntity;
            D2EM::persist( $pi );
        }

        /** @var VirtualInterfaceEntity $vi */
        if( !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $request->input( 'viid' ) ) ) ){
            abort(404, 'Unknown virtual interface');
        }

        if( !( $sp = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'switch-port' ) ) ) ) {
            abort(404, 'Unknown switch');
        }

        if( !$vi->getCustomer()->isUniqueMonitorIndex( $request->input( 'monitorindex' ) ) ) {
            AlertContainer::push( 'The monitor index must be unique. It has been reset below to a unique value.', Alert::DANGER );

            // doesnt work set all the input and replace the value of the monitor with a new value
            return Redirect::to('physicalInterface/edit/'.$pi->getId() )->withInput( Input::all() , Input::replace(['monitorindex' => D2EM::getRepository( PhysicalInterfaceEntity::class )->getNextMonitorIndex( $pi->getVirtualInterface()->getCustomer()) ] ) );

        }

        $pi->setSwitchPort( $sp );
        $pi->setVirtualInterface( $vi );
        $pi->setStatus( $request->input( 'status' ) );
        $pi->setSpeed( $request->input( 'speed' ) );
        $pi->setDuplex( $request->input( 'duplex' ) );
        $pi->setAutoneg( $request->input( 'autoneg-label' ) ? 1 : 0 );
        $pi->setMonitorindex( $request->input( 'monitorindex' ) );
        $pi->setNotes( $request->input( 'notes' ) );

        D2EM::flush();

        AlertContainer::push( 'Physical Interface updated successfully.', Alert::SUCCESS );

        return Redirect::to( 'virtualInterface/edit/'.$pi->getVirtualInterface()->getId() );

    }

}