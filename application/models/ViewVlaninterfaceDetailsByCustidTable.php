<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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

/**
 *
 * Auto-generated Doctrine ORM File
 *
 * @category ORM
 * @package IXP_ORM_Models
 * @copyright Copyright 2008 - 2010 Internet Neutral Exchange Association Limited <info (at) inex.ie>
 * @author Barry O'Donovan <barryo (at) inex.ie>
 */
class ViewVlaninterfaceDetailsByCustidTable extends Doctrine_Table
{

    /**
     * Get an array of route server enabled states by customer ID
     *
     * Returns an array of the form:
     *   custid -> rsclient
     *
     * @param $vlan int The VLAN to query
     */
    public function getRSClientEnabledPerVLAN( $vlan = null )
    {
        // get the member IPv6 enabled states for this VLAN
        $_rsclient = Doctrine_Query::create()
             ->select( 'custid, rsclient' )
             ->from( 'ViewVlaninterfaceDetailsByCustid' )
             ->where( 'vlan = ?' )
             ->execute( $vlan, Doctrine_Core::HYDRATE_ARRAY );

        $rsclient = array();
        foreach( $_rsclient as $i )
            $rsclient[$i['custid']] = $i['rsclient'];

        return $rsclient;
    }

    /**
     * Get an array of IPv6 enabled states by customer ID
     *
     * Returns an array of the form:
     *   custid -> ipv6enabled
     *
     * @param $vlan int The VLAN to query
     */
    public function getIPv6EnabledPerVLAN( $vlan = null )
    {
        // get the member IPv6 enabled states for this VLAN
        $_ipv6 = Doctrine_Query::create()
             ->select( 'custid, ipv6enabled' )
             ->from( 'ViewVlaninterfaceDetailsByCustid' )
             ->where( 'vlan = ?' )
             ->execute( $vlan, Doctrine_Core::HYDRATE_ARRAY );

        $ipv6 = array();
        foreach( $_ipv6 as $i )
            $ipv6[$i['custid']] = $i['ipv6enabled'];

        return $ipv6;
    }
}