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

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;
use IXP\Http\Controllers\Controller;

use Entities\{
    VirtualInterface as VirtualInterfaceEntity,
    Customer as CustomerEntity,
    Vlan as VlanEntity,
    Switcher as SwitcherEntity,
    PhysicalInterface as PhysicalInterfaceEntity,
    SwitchPort as SwitchPortEntity,
    VlanInterface as VlanInterfaceEntity,
    IPv4Address as IPv4AddressEntity,
    IPv6Address as IPv6AddressEntity
};


use IXP\Http\Requests\{
    StoreVirtualInterface,
    StoreInterfaceWizard
};

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;


/**
 * VirtualInterface Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VirtualInterfaceController extends Controller
{
    /**
     * Display all the virtualInterfaces
     *
     * @return  View
     */
    public function list( ): View {
        return view( 'virtual-interface/index' )->with([
            'listVi'       => D2EM::getRepository( VirtualInterfaceEntity::class )->getForList()
        ]);
    }

    /**
     * Display the form to add a virtual interface
     *
     * @return View
     */
    public function edit( int $id = null ): View {
        $vi = false;
        /** @var VirtualInterfaceEntity $vi */
        if( $id and !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        if( $vi ) {
            // fill the form with Virtual interface data
            Former::populate([
                'cust'                  => $vi->getCustomer(),
                'name'                  => $vi->getName(),
                'description'           => $vi->getDescription(),
                'channel-group'         => $vi->getChannelgroup(),
                'mtu'                   => $vi->getMtu(),
            ]);
        }


        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'virtual-interface/edit' )->with([
            'cust'      => D2EM::getRepository( CustomerEntity::class)->getNames(),
            'vi'        => $vi ? $vi : false
        ]);
    }

    /**
     * Display the Virtual Interface informations
     *
     * @params  int $id ID of the Virtual Interface
     * @return  view
     */
    public function view( int $id = null ): View {
        $vi = false;

        if( !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $id ) ) ){
            abort(404);
        }

        return view( 'virtual-interface/view' )->with([
            'vi'                        => $vi
        ]);
    }

    /**
     * Display the form to add a virtual interface
     *
     * @return View
     */
    public function editWizard( int $id = null ): View {
        $vi = false;
        /** @var VirtualInterfaceEntity $vi */
        if( $id and !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        if( $vi ) {
            // fill the form with Virtual interface data
            Former::populate([
                'cust'                  => $vi->getCustomer(),
                'name'                  => $vi->getName(),
                'description'           => $vi->getDescription(),
                'channel-group'         => $vi->getChannelgroup(),
                'mtu'                   => $vi->getMtu(),
            ]);
        }


        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'virtual-interface/edit-wizard' )->with([
            'cust'                  => D2EM::getRepository( CustomerEntity::class )->getNames(),
            'vlan'                  => D2EM::getRepository( VlanEntity::class )->getNames( false ),
            'switches'              => D2EM::getRepository( SwitcherEntity::class )->getNames( ),
            'status'                => PhysicalInterfaceEntity::$STATES,
            'speed'                 => PhysicalInterfaceEntity::$SPEED,
            'duplex'                => PhysicalInterfaceEntity::$DUPLEX,
            'vi'                    => $vi ? $vi : false
        ]);
    }

    /**
     * Add or edit a virtual interface (set all the data needed)
     *
     * @param   StoreVirtualInterface $request instance of the current HTTP request
     * @return  RedirectResponse
     */
    public function store( StoreVirtualInterface $request ): RedirectResponse {
        /** @var VirtualInterfaceEntity $vi */
        if( $request->input( 'id' ) && $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $request->input( 'id' ) ) ) {
            if( !$vi ) {
                abort(404, 'Unknown router');
            }
        } else {
            $vi = new VirtualInterfaceEntity;
            D2EM::persist($vi);
        }

        if( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'cust' ) ) ) ) {
            abort(404, 'Unknown customer');
        }

        $vi->setCustomer( $cust );
        $vi->setTrunk( $request->input( 'trunk' ) ? $request->input( 'trunk' ) : false );
        $vi->setLagFraming( $request->input( 'lag_framing' ) ? $request->input( 'lag_framing' ) : false );
        $vi->setFastLACP( $request->input( 'fastlacp' ) ? $request->input( 'fastlacp' ) : false);
        $vi->setName( $request->input( 'name' ) );
        $vi->setDescription( $request->input( 'description' ) );
        $vi->setChannelgroup( $request->input( 'channel-group' ) );
        $vi->setMtu( $request->input( 'mtu' ) );

        D2EM::flush();

        AlertContainer::push( 'Virtual Interface added/updated successfully.', Alert::SUCCESS );

        return Redirect::to( 'virtualInterface/edit/'.$vi->getId());

    }

    /**
     * Add or edit a interface wizard (set all the data needed)
     *
     * @param   StoreInterfaceWizard $request instance of the current HTTP request
     * @return  RedirectResponse
     */
    public function storeInterfaceWizard( StoreInterfaceWizard $request ): RedirectResponse {
        /** @var CustomerEntity $cust */
        if( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'cust' ) ) ) ) {
            abort(404, 'Unknown customer');
        }
        /** @var VlanEntity $vlan */
        if( !( $vlan = D2EM::getRepository( VlanEntity::class )->find( $request->input( 'vlan' ) ) ) ) {
            abort(404, 'Unknown vlan');
        }
        /** @var SwitcherEntity $switch */
        if( !( $switch = D2EM::getRepository( SwitcherEntity::class )->find( $request->input( 'switch' ) ) ) ) {
            abort(404, 'Unknown switch');
        }
        /** @var SwitchPortEntity $sp */
        if( !( $sp = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'switch-port' ) ) ) ) {
            abort(404, 'Unknown customer');
        }

        /** @var VirtualInterfaceEntity $vi */
        $vi = new VirtualInterfaceEntity;
        D2EM::persist($vi);

        $vi->setCustomer( $cust );

        // these options is not available in the wizard ????????
        $vi->setName('');
        $vi->setChannelgroup(null);
        $vi->setLagFraming(false);
        $vi->setFastLACP(false);

        /** @var PhysicalInterfaceEntity $pi */
        $pi = new PhysicalInterfaceEntity();
        D2EM::persist($pi);

        $pi->setVirtualInterface( $vi );
        $vi->addPhysicalInterface($pi);

        $sp->setType( \Entities\SwitchPort::TYPE_PEERING );
        $pi->setSwitchPort( $sp );

        $pi->setMonitorindex( D2EM::getRepository( PhysicalInterfaceEntity::class )->getNextMonitorIndex( $cust ) );


        if( count( $vi->getPhysicalInterfaces() ) ) {

            // LAGs must have a channel group and bundle name. But only if they have a phys int:
            if( $vi->getLagFraming() && !$vi->getChannelgroup() ) {
                $vi->setChannelgroup( D2EM::getRepository( VirtualInterfaceEntity::class )->assignChannelGroup( $vi ) );
                AlertContainer::push( "Missing channel group assigned as this is a LAG port", Alert::INFO );
            }

            // LAGs must have a bundle name
            if( $vi->getLagFraming() && !$vi->getName() ) {
                // assumption on no mlags (multi chassis lags) here:
                $vi->setName( $vi->getPhysicalInterfaces()[ 0 ]->getSwitchport()->getSwitcher()->getVendor()->getBundleName() );

                if( $vi->getName() ) {
                    AlertContainer::push( "Missing bundle name assigned as this is a LAG port", Alert::INFO );
                } else {
                    AlertContainer::push( "Missing bundle name not assigned as no bundle name set for this switch vendor (see Vendors)", Alert::WARNING );
                }
            }
        }


        /* ?????? if( $form->getElement( 'fanout' ) )
        {
            if( !$this->processFanoutPhysicalInterface( $form, $pi, $vi ) )
                return false;

            if( $pi->getRelatedInterface() )
            {
                $pi->getRelatedInterface()->setSpeed( $form->getValue( "speed" ) );
                $pi->getRelatedInterface()->setStatus( $form->getValue( "status" ) );
                $pi->getRelatedInterface()->setDuplex( $form->getValue( "duplex" ) );
            }
        }*  ASK WHAT IS THAT  /

        /** @var VlanInterfaceEntity $vli */
        $vli = new VlanInterfaceEntity();
        D2EM::persist($vli);

        $vli->setVlan( $vlan );

        $ipv4Set = null;
        $ipv6Set = null;

        if( $request->input('ipv4-enabled' ) ){
            $ipv4Set = $this->setIp($request, $vlan, $vli, false );
        }

        if( $request->input('ipv6-enabled' ) ){
            $ipv6Set = $this->setIp($request, $vlan, $vli, true );
        }

        if( ( $request->input('ipv4-enabled' ) && $ipv4Set == false ) || ( $request->input('ipv6-enabled' ) && $ipv6Set == false ) ) {
            return Redirect::to('virtualInterface/add-wizard' )->withInput( Input::all() );
        }

        $vli->setVirtualInterface( $vi );

        D2EM::flush();

        AlertContainer::push( "New interface created!", Alert::SUCCESS );

        return Redirect::to( 'customer/overview/tab/ports/id/' . $cust->getId() );

    }


    /**
     *
     * DUPLICATED !!!!
     *
     * Sets IPv4 or IPv6 from form to given VlanInterface.
     *
     * Function checks if IPvX address is provided if IPvX is enabled. Then
     * it checks if given IPvX address exists for current Vlan:
     *
     * * if it exists, it ensures is is not assigned to another interface;
     * * if !exists, creates a new one.
     *
     * @param \Entities\VlanInterface      $vli  Vlan interface to assign IP to
     * @param bool                         $ipv6 Bool to define if IP address is IPv4 or IPv6
     * @return bool
     */
    private function setIp($request, $vl, $vli, $ipv6 = false )
    {
        $iptype = $ipv6 ? "ipv6" : "ipv4";
        $ipVer  = $ipv6 ? "IPv6" : "IPv4";
        $setterIPv = "set{$ipVer}Address";
        $setterEnabled = "set{$ipVer}enabled";
        $setterHostname = "set{$ipVer}hostname";
        $setterSecret = "set{$ipVer}bgpmd5secret";
        $setterPing = "set{$ipVer}canping";
        $setterMonitor = "set{$ipVer}monitorrcbgp";

        $entity = $ipv6 ? IPv6AddressEntity::class : IPv4AddressEntity::class;

        $addressValue = $request->input( $iptype . '-address' );

        if( !$addressValue ) {
            AlertContainer::push( "Please select or enter an ".$ipVer." address.", Alert::DANGER );
            return false;
        }

        if( !($ip = D2EM::getRepository( $entity )->findOneBy( [ "Vlan" => $vl->getId(), 'address' => $addressValue ] )  ) ){

            $ip = new $entity();
            D2EM::persist( $ip );
            $ip->setVlan( $vl );
            $ip->setAddress( $addressValue );
        }
        else if( $ip->getVlanInterface() && $ip->getVlanInterface() != $vli )
        {
            AlertContainer::push( $ipVer."address ".$addressValue." is already in use.", Alert::DANGER );
            return false;
        }

        $vli->$setterIPv( $ip );
        $vli->$setterHostname( $request->input( $iptype . '-hostname' ) );
        $vli->$setterEnabled( true );
        $vli->$setterSecret( $request->input( $iptype . '-bgp-md5-secret' ) );
        $vli->$setterPing( $request->input( $iptype . '-can-ping' ) );
        $vli->$setterMonitor( $request->input( $iptype . '-monitor-rcbgp' ) );

        return true;
    }


}
