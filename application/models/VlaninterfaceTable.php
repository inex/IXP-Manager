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
class VlaninterfaceTable extends Doctrine_Table
{

    
    /**
     * Utility function to load all customers suitable for inclusion in the peering matrices
     *
     * @param array|int $type The customer type (see Cust::TYPE_*)
     * @param int $hydration The Doctrine hydration method to use
     * @return The resultset in the requested hydration
     */
    public static function getForPeeringMatrix( $vlan, $protocol )
    {
        $q = Doctrine_Query::create()
            ->select( 'vli.rsclient, vi.custid, c.shortname, c.autsys, c.name' )
            ->from( 'Vlaninterface vli' )
            ->leftJoin( 'vli.Virtualinterface vi' )
            ->leftJoin( 'vi.Cust c' )
            ->leftJoin( 'vli.Vlan v' )
            ->andWhere( 'v.number = ?', $vlan )
            ->andWhere( 'c.activepeeringmatrix = 1' );
        
        if( $protocol == 4 )
            $q->andWhere( 'ipv4enabled = 1' );
        else
            $q->andWhere( 'ipv6enabled = 1' );
        
        $q->andWhereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_PROBONO ) )
            ->andWhere( '( c.dateleave IS NULL or c.dateleave = "0000-00-00" )' )
            ->orderBy( 'c.autsys' )
            ->groupBy( 'vi.custid' );
        
        $acusts = $q->execute( null, Doctrine::HYDRATE_ARRAY );
        
        $custs = array();
        
        foreach( $acusts as $c )
        {
            $custs[ $c['Virtualinterface']['Cust']['autsys'] ] = array();
            $custs[ $c['Virtualinterface']['Cust']['autsys'] ]['autsys']    = $c['Virtualinterface']['Cust']['autsys'];
            $custs[ $c['Virtualinterface']['Cust']['autsys'] ]['name']      = $c['Virtualinterface']['Cust']['name'];
            $custs[ $c['Virtualinterface']['Cust']['autsys'] ]['shortname'] = $c['Virtualinterface']['Cust']['shortname'];
            $custs[ $c['Virtualinterface']['Cust']['autsys'] ]['rsclient']  = $c['rsclient'];
            $custs[ $c['Virtualinterface']['Cust']['autsys'] ]['custid']    = $c['Virtualinterface']['Cust']['id'];
        }
        
        return $custs;
    }
    
    
}