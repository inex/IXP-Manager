<?php

namespace IXP\Http\Controllers\Interfaces;

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

use D2EM, Input, Redirect;

use Entities\{
    IPv4Address as IPv4AddressEntity,
    IPv6Address as IPv6AddressEntity,
    Vlan as VlanEntity,
    VlanInterface as VlanInterfaceEntity
};

use IXP\Exceptions\GeneralException;

use IXP\Models\{
    CoreBundle,
    CoreInterface,
    CoreLink,
    PhysicalInterface,
    SwitchPort,
    VirtualInterface
};

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\StoreVirtualInterfaceWizard;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Common Functions for the Inferfaces Controllers
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Common extends Controller
{
    /**
     * Removes related interface
     *
     * Removes a related interface and if it only has one physical interface, removes the virtual interface also
     *
     * @param PhysicalInterface $pi Physical interface to remove related physical interface.
     *
     * @return void
     * @throws
     */
    public function removeRelatedInterface( PhysicalInterface $pi ): void
    {
        if( $related = $pi->relatedInterface() ) {
            if( $pi->relatedInterface()->virtualInterface->physicalInterfaces()->count() === 1 ) {
                $pi->relatedInterface()->virtualInterface->macAddresses()->delete();

                foreach( $pi->relatedInterface()->virtualInterface->vlanInterfaces as $vli ) {
                    $vli->layer2addresses()->delete();
                    $vli->delete();
                }

                $vi = $pi->relatedInterface()->virtualInterface();
                $pi->relatedInterface()->update( [ 'virtualinterfaceid' => null ] );
                $vi->delete();
            }

            $pi->relatedInterface()->update( [ 'switchportid' => null ] );
            $pi->fanout_physical_interface_id = null;
            $pi->save();

            $related->delete();
        }
    }

    /**
     * Links peering and fanout physical interfaces.
     *
     * If *link with fanout port* is checked in the form, then this function:
     *
     * * checks if the necessary fields are set;
     * * loads the selected SwitchPort;
     * * creates/loads this SwitchPort's physical interface;
     * * creates a link between the fanout and peering physical interfaces.
     *
     * If *link with fanout port* is not checked then this function checks
     * if the peering port has a related interface and, if so, removes the relation.
     *
     * @param   Request|StoreVirtualInterfaceWizard $r instance of the current HTTP reques
     * @param   PhysicalInterface                   $pi Peering physical interface to related with fanout physical interface (port).
     * @param   VirtualInterface                    $vi Virtual interface of peering physical interface
     *
     * @return bool
     *
     * @throws
     */
    public function processFanoutPhysicalInterface( $r, $pi, $vi ): bool
    {
        if( !$r->fanout ) {
            $this->removeRelatedInterface( $pi );
            return true;
        }

        $fnsp = SwitchPort::findOrFail( $r->input( 'switch-port-fanout' ) );
        $fnsp->update( [ 'type' => SwitchPort::TYPE_FANOUT ] );

        // if switch port does not have a physical interface then create one
        if( !$fnsp->physicalInterface ) {
            $fnpi = new PhysicalInterface;
            $fnpi->switchportid = $fnsp->id;
            $fnpi->save();
        } else {
            $fnpi = $fnsp->physicalInterface;

            // check if the fanout port has a physical interface and if the physical interface is different of the current physical interface
            if( $fnsp->physicalInterface->relatedInterface() && $fnsp->physicalInterface->relatedInterface()->id !== $pi->id ) {
                AlertContainer::push( "Missing bundle name not assigned as no bundle name set for this switch vendor (see Vendors)", Alert::WARNING );
                return false;
            }
        }

        // if the physical interace already has a related physical interface and it's not the same as the fanout physical interface
        if( $pi->relatedInterface() && $pi->relatedInterface()->id !== $fnpi->id ) {
            // if fanout does not have a virtual interface, relate it with old fanout port virtual interface.
            if( !$fnpi->virtualInterface ) {
                $fnpi->virtualinterfaceid = $pi->relatedInterface()->virtualinterfaceid;
            }

            $this->removeRelatedInterface( $pi );

        } else if( !$fnpi->virtualInterface ) {
            // create virtual interface for fanout physical interface if doesn't have one
            $fnvi = new VirtualInterface;
            $fnvi->custid = $vi->customer->reseller;
            $fnvi->save();
            $fnpi->virtualinterfaceid = $fnvi->id;
        }

        $pi->fanout_physical_interface_id = $fnpi->id;
        $pi->save();

        $fnpi->speed  =  $pi->speed;
        $fnpi->status = $pi->status;
        $fnpi->duplex = $pi->duplex;
        $fnpi->save();

        return true;
    }

    /**
     * When we have >1 phys int / LAG framing, we need to set other elements of the virtual interface appropriately:
     *
     * @param VirtualInterface $vi
     *
     * @throws
     */
    public function setBundleDetails( VirtualInterface $vi ): void
    {
        if( $vi->physicalInterfaces()->count() ) {
            // LAGs must have a channel group and bundle name. But only if they have a phys int:
            if( $vi->lag_framing && !$vi->channelgroup ) {
                $vi->channelgroup = $this->assignChannelGroup( $vi );
                $vi->save();
                AlertContainer::push( "Missing channel group assigned as this is a LAG port", Alert::INFO );
            }


            // LAGs must have a bundle name
            if( $vi->lag_framing && !$vi->name ) {
                // assumption on no mlags (multi chassis lags) here:
                if( $vendor = $vi->physicalInterfaces()->first()->switchport->switcher->vendor ) {
                    $vi->name = $vendor->bundle_name;
                    $vi->save();
                    AlertContainer::push( "Missing bundle name assigned as this is a LAG port", Alert::INFO );
                } else {
                    AlertContainer::push( "Missing bundle name not assigned as no bundle name set for this switch vendor (see Vendors)", Alert::WARNING );
                }
            }
        }
        else{
            // we don't allow setting channel group or name until there's >= 1 physical interface / LAG framing:
            $vi->name = '';
            $vi->channelgroup = null;
            $vi->lag_framing = false;
            $vi->fastlacp = false;
            $vi->save();
        }
    }

    /**
     * For the given $vi, assign a unique channel group
     *
     * @param VirtualInterface $vi
     *
     * @return int
     *
     * @throws
     */
    public function assignChannelGroup( VirtualInterface $vi ): int
    {
        if( $vi->physicalInterfaces()->count()  === 0 ) {
            throw new GeneralException("Channel group number is only relevant when there is at least one physical interface");
        }

        $usedChannelGroups = VirtualInterface::select( [ 'vi.channelgroup' ] )
            ->from( 'virtualinterface AS vi' )
            ->leftJoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->leftJoin( 'switchport as sp', 'sp.id', 'pi.switchportid' )
            ->whereNotNull( 'vi.channelgroup' )
            ->whereIn( 'sp.switchid', function( $query ) use( $vi ) {
                $query->select( [ 's.id' ] )
                    ->from( 'switch AS s' )
                    ->leftJoin( 'switchport AS sp', 'sp.switchid', 's.id')
                    ->leftJoin( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
                    ->where( 'pi.virtualinterfaceid', $vi->id );
            })->distinct()->get()->pluck('channelgroup')->toArray();


        $orig = $vi->channelgroup;
        for( $i = 1; $i < 1000; $i++ ) {
            if( !in_array( $i, $usedChannelGroups, true ) ) {
                $vi->channelgroup = $i;
                return $i;
            }
        }

        $vi->channelgroup = $orig;
        throw new GeneralException("Could not assign a free channel group number");
    }

    /**
     * Sets IPv4 or IPv6 from form to given VlanInterface from request data.
     *
     * Function checks if IPvX address is provided if IPvX is enabled. Then
     * it checks if given IPvX address exists for current Vlan:
     *
     * NB: form sanity checking must be performed BEFORE calling this function
     *
     * * if it exists, it ensures is is not assigned to another interface;
     * * if !exists, creates a new one.
     *
     * @param Request $request
     * @param VlanEntity $v
     * @param VlanInterfaceEntity $vli Vlan interface to assign IP to
     * @param bool $ipv6 Bool to define if IP address is IPv4 or IPv6
     * @return bool
     * @throws
     */
    public function setIp( Request $request, VlanEntity $v, VlanInterfaceEntity $vli, bool $ipv6 = false )
    {
        $iptype         = $ipv6 ? "ipv6" : "ipv4";
        $ipVer          = $ipv6 ? "IPv6" : "IPv4";
        $setterIPv      = "set{$ipVer}Address";
        $setterEnabled  = "set{$ipVer}enabled";
        $setterHostname = "set{$ipVer}hostname";
        $setterSecret   = "set{$ipVer}bgpmd5secret";
        $setterPing     = "set{$ipVer}canping";
        $setterMonitor  = "set{$ipVer}monitorrcbgp";

        $entity = $ipv6 ? IPv6AddressEntity::class : IPv4AddressEntity::class;

        $addressValue = $request->input( $iptype . '-address' );

        if( trim( $addressValue ) ) {
            if( !( $ip = D2EM::getRepository( $entity )->findOneBy( [ "Vlan" => $v->getId(), 'address' => $addressValue ] ) ) ) {
                /** @var IPv4AddressEntity|IPv6AddressEntity $ip */
                $ip = new $entity();
                $ip->setVlan( $v );
                $ip->setAddress( $addressValue );
                D2EM::persist( $ip );
            } else if( $ip->getVlanInterface() && $ip->getVlanInterface() != $vli ) {
                AlertContainer::push( "{$ipVer} address {$addressValue} is already in use by another VLAN interface on the same VLAN.", Alert::DANGER );
                return false;
            }

            $vli->$setterIPv( $ip );

        } else {
            $vli->$setterIPv( null );
        }

        $vli->$setterHostname( $request->input( $iptype . '-hostname' ) );
        $vli->$setterEnabled(  $request->input( $iptype . '-enabled', false ) );
        $vli->$setterSecret(   $request->input( $iptype . '-bgp-md5-secret' ) );
        $vli->$setterPing(     $request->input( $iptype . '-can-ping', false ) );
        $vli->$setterMonitor(  $request->input( $iptype . '-monitor-rcbgp', false ) );

        return true;
    }


    public function warnIfIrrdbFilteringButNoIrrdbSourceSet( VlanInterfaceEntity $vli )
    {
        if( $vli->getRsclient() && $vli->getIrrdbfilter() && !$vli->getVirtualInterface()->getCustomer()->getIRRDB() ) {
            AlertContainer::push( "You have enabled IRRDB filtering for this VLAN interface's route server sessions. "
                . "However, the customer does not have an IRRDB source set. As such, the route servers will block all prefix "
                . "advertisements. To rectify this, edit the customer and set an IRRDB source.", Alert::WARNING );
        }
    }


    /**
     * Build everything that a Core Bundle need (core link, core Interface etc)
     *
     * @param   CoreBundle  $cb Core bundle object
     * @param   Request     $r instance of the current HTTP request
     * @param   array       $vis array of the Virtual interfaces ( side A and B ) linked to the core bundle
     * @param   bool        $edit Are we editing the core bundle ?
     *
     * @return RedirectResponse|bool
     *
     * @throws
     */
    public function buildCorelink( CoreBundle $cb, Request $r, array $vis, bool $edit )
    {
        foreach( $r->input( "cl-details" ) as $clDetail ) {
            $cl = new CoreLink;

            $cl->core_bundle_id = $cb->id;
            $cl->enabled = $clDetail[ 'enabled-cl' ] ?? false;

            $bfd    = $clDetail[ 'bfd' ] ?? false;
            $type   = $edit ? $cb->type : $r->type;

            $cl->bfd =  (int)$type === CoreBundle::TYPE_ECMP ? $bfd : false;
            $cl->ipv4_subnet = $clDetail[ 'subnet' ] ?? null;

            foreach( $vis as $side => $vi ) {
                if( !( ${ 'sp' . $side } = SwitchPort::find( $clDetail[ "hidden-sp-$side" ] ) ) ) {
                    return Redirect::back()->withInput( $r->all() );
                }

                ${ 'sp' . $side }->type = SwitchPort::TYPE_CORE;
                ${ 'sp' . $side }->save();

                ${ 'pi' . $side } = new PhysicalInterface;
                ${ 'pi' . $side }->switchportid         = ${ 'sp' . $side }->id;
                ${ 'pi' . $side }->virtualinterfaceid   = $vi->id;
                ${ 'pi' . $side }->speed                = $edit ? $cb->speedPi()    : $r->speed;
                ${ 'pi' . $side }->duplex               = $edit ? $cb->duplexPi()   : $r->duplex;
                ${ 'pi' . $side }->autoneg              = $edit ? $cb->autoNegPi()  : $r->input('auto-neg' ) ?? false;
                ${ 'pi' . $side }->status               = PhysicalInterface::STATUS_CONNECTED;
                ${ 'pi' . $side }->save();

                ${ 'ci' . $side } = new CoreInterface;
                ${ 'ci' . $side }->physical_interface_id = ${ 'pi' . $side }->id;
                ${ 'ci' . $side }->save();
            }

            $cl->core_interface_sidea_id = $cia->id;
            $cl->core_interface_sideb_id = $cib->id;

            $cl->save();

            $pia->virtualinterfaceid = $vis[ 'a' ]->id;
            $pia->save();
            $pib->virtualinterfaceid = $vis[ 'b' ]->id;
            $pib->save();
        }
        return true;
    }
}