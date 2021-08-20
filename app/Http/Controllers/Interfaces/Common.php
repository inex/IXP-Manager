<?php

namespace IXP\Http\Controllers\Interfaces;

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

use Exception, Redirect;

use IXP\Exceptions\GeneralException;

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\StoreVirtualInterfaceWizard;

use IXP\Models\{
    CoreBundle,
    CoreInterface,
    CoreLink,
    IPv4Address,
    IPv6Address,
    PhysicalInterface,
    SwitchPort,
    VirtualInterface,
    Vlan,
    VlanInterface
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Common Functions for the Inferfaces Controllers
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Interfaces
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
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
     *
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
     * @param   Request|StoreVirtualInterfaceWizard $r instance of the current HTTP request
     * @param   PhysicalInterface                   $pi Peering physical interface to related with fanout physical interface (port).
     * @param   VirtualInterface                    $vi Virtual interface of peering physical interface
     *
     * @return bool
     *
     * @throws
     */
    public function processFanoutPhysicalInterface( $r, PhysicalInterface $pi, VirtualInterface $vi ): bool
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

        // if the physical interface already has a related physical interface and it's not the same as the fanout physical interface
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
                //$vi->save();
                AlertContainer::push( "Missing channel group assigned as this is a LAG port", Alert::INFO );
            }

            // LAGs must have a bundle name
            if( $vi->lag_framing && !$vi->name ) {
                // assumption on no mlags (multi chassis lags) here:
                if( $vendor = $vi->physicalInterfaces()->first()->switchport->switcher->vendor ) {
                    $vi->name = $vendor->bundle_name;
                    //$vi->save();
                    AlertContainer::push( "Missing bundle name assigned as this is a LAG port", Alert::INFO );
                } else {
                    AlertContainer::push( "Missing bundle name not assigned as no bundle name set for this switch vendor (see Vendors)", Alert::WARNING );
                }
            }
        }
        else{
            // we don't allow setting channel group or name until there's >= 1 physical interface / LAG framing:
            $vi->name           = '';
            $vi->channelgroup   = null;
            $vi->lag_framing    = false;
            $vi->fastlacp       = false;
            //$vi->save();
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
     * @param Request       $r
     * @param Vlan          $v
     * @param VlanInterface $vli Vlan interface to assign IP to
     * @param bool          $ipv6 Bool to define if IP address is IPv4 or IPv6
     *
     * @return bool
     *
     * @throws
     */
    public function setIp( Request $r, Vlan $v, VlanInterface $vli, bool $ipv6 = false ): bool
    {
        $iptype         = $ipv6 ? "ipv6" : "ipv4";
        $setterIPv      = "{$iptype}addressid";
        $setterEnabled  = "{$iptype}enabled";
        $setterHostname = "{$iptype}hostname";
        $setterSecret   = "{$iptype}bgpmd5secret";
        $setterPing     = "{$iptype}canping";
        $setterMonitor  = "{$iptype}monitorrcbgp";

        /** @var IPv4Address|IPv6Address $model */
        $model = $ipv6 ? IPv6Address::class : IPv4Address::class;

        $addressValue = $r->input( $iptype . 'address' );

        if( trim( $addressValue ) ) {
            if( !( $ip = $model::where( 'vlanid', $v->id)->where( 'address', $addressValue )->first() ) ) {
                $ip = new $model;
                $ip->vlanid  = $v->id;
                $ip->address = $addressValue;
                $ip->save();
            } else if( $ip->vlanInterface && $ip->vlanInterface->id !== $vli->id ) {
                AlertContainer::push( ucfirst( $iptype ) . " address {$addressValue} is already in use by another VLAN interface on the same VLAN.", Alert::DANGER );
                return false;
            }

            $vli->$setterIPv = $ip->id;

        } else {
            $vli->$setterIPv = null;
        }

        $vli->$setterHostname   = $r->input( $iptype . 'hostname' );
        $vli->$setterEnabled    = $r->input( $iptype . 'enabled'  );
        $vli->$setterSecret     = $r->input( $iptype . 'bgpmd5secret' );
        $vli->$setterPing       = $r->input( $iptype . 'canping' );
        $vli->$setterMonitor    = $r->input( $iptype . 'monitorrcbgp' );
        return true;
    }


    /**
     * @param VlanInterface $vli
     *
     * @return void
     */
    public function warnIfIrrdbFilteringButNoIrrdbSourceSet( VlanInterface $vli ): void
    {
        if( $vli->rsclient && $vli->irrdbfilter && !$vli->virtualInterface->customer->irrdb ) {
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

                // Creating $pia|$pib
                ${ 'pi' . $side } = new PhysicalInterface;
                ${ 'pi' . $side }->switchportid         = ${ 'sp' . $side }->id;
                ${ 'pi' . $side }->virtualinterfaceid   = $vi->id;
                ${ 'pi' . $side }->speed                = $edit ? $cb->speedPi()    : $r->speed;
                ${ 'pi' . $side }->duplex               = $edit ? $cb->duplexPi()   : $r->duplex;
                ${ 'pi' . $side }->autoneg              = $edit ? $cb->autoNegPi()  : $r->input('auto-neg' ) ?? false;
                ${ 'pi' . $side }->status               = PhysicalInterface::STATUS_CONNECTED;
                ${ 'pi' . $side }->virtualinterfaceid = $vis[ $side ]->id;
                ${ 'pi' . $side }->save();

                // Creating $cia|$cib
                ${ 'ci' . $side } = new CoreInterface;
                ${ 'ci' . $side }->physical_interface_id = ${ 'pi' . $side }->id;
                ${ 'ci' . $side }->save();
            }

            $cl->core_interface_sidea_id = $cia->id;/** @var $cia CoreInterface */
            $cl->core_interface_sideb_id = $cib->id;/** @var $cib CoreInterface */

            $cl->save();
        }
        return true;
    }

    /**
     * Delete the physical interface and everything related
     *
     * @param  Request              $r
     * @param  PhysicalInterface    $pi
     * @param  bool                 $setBunleDetails
     *
     * @throws Exception
     */
    protected function deletePi( Request $r, PhysicalInterface $pi, bool $setBunleDetails = false ): void
    {
        $pi2 = clone $pi;
        if( $pi->switchPort->typePeering() && $pi->fanoutPhysicalInterface ) {
            $pi->update( [ 'switchportid' => null ] );
            $pi->fanoutPhysicalInterface->switchPort->update( [ 'type' => SwitchPort::TYPE_PEERING ] );
        } else if( $pi->switchPort->typeFanout() && $pi->peeringPhysicalInterface ) {
            if( (bool)$r->related ){
                $this->removeRelatedInterface( $pi2 );
            }

            $pi->peeringPhysicalInterface->fanout_physical_interface_id = null;
            $pi->peeringPhysicalInterface->save();
        }
        if( (bool)$r->related && $pi2->relatedInterface() ) {
            $this->removeRelatedInterface( $pi2 );
        }

        if( $setBunleDetails ){
            $this->setBundleDetails( $pi->virtualInterface );
        }

        $pi->delete();
    }
}