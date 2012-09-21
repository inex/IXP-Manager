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
     * Utility function to load all customers suitable for inclusion in the peering manager
     *
     */
    public static function getForPeeringManager()
    {
        $q = Doctrine_Query::create()
                ->select( 'c.shortname, c.autsys, c.name, c.maxprefixes, c.peeringemail, c.peeringpolicy, c.id' )
                ->addSelect( 'vi.id, vli.ipv4enabled, vli.ipv6enabled, vli.rsclient, v.number' )
                ->from( 'Cust c' )
                ->leftJoin( 'c.Virtualinterface vi' )
                ->leftJoin( 'vi.Vlaninterface vli' )
                ->leftJoin( 'vli.Vlan v' )
                //->andWhere( 'c.activepeeringmatrix = 1' );
                ->andWhereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_PROBONO ) )
                ->andWhere( '( c.dateleave IS NULL or c.dateleave = "0000-00-00" )' )
                ->orderBy( 'c.name' );
    
        $acusts = $q->execute( null, Doctrine::HYDRATE_ARRAY );
    
        $custs = array();
    
        foreach( $acusts as $c )
        {
            $custs[ $c['autsys'] ] = array();
            
            $custs[ $c['autsys'] ]['id']            = $c['id'];
            $custs[ $c['autsys'] ]['name']          = $c['name'];
            $custs[ $c['autsys'] ]['shortname']     = $c['shortname'];
            $custs[ $c['autsys'] ]['autsys']        = $c['autsys'];
            $custs[ $c['autsys'] ]['maxprefixes']   = $c['maxprefixes'];
            $custs[ $c['autsys'] ]['peeringemail']  = $c['peeringemail'];
            $custs[ $c['autsys'] ]['peeringpolicy'] = $c['peeringpolicy'];

            $custs[ $c['autsys'] ]['vlaninterfaces'] = array();
            
            foreach( $c['Virtualinterface'] as $vi )
            {
                foreach( $vi['Vlaninterface'] as $vli )
                {
                    if( !isset( $custs[ $c['autsys'] ]['vlaninterfaces'][ $vli['Vlan']['number'] ] ) )
                    {
                        $custs[ $c['autsys'] ]['vlaninterfaces'][ $vli['Vlan']['number'] ] = array();
                        $cnt = 0;
                    }
                    else
                        $cnt = count( $custs[ $c['autsys'] ]['vlaninterfaces'][ $vli['Vlan']['number'] ] );
                    
                    $custs[ $c['autsys'] ]['vlaninterfaces'][ $vli['Vlan']['number'] ][ $cnt ]['ipv4enabled'] = $vli['ipv4enabled'];
                    $custs[ $c['autsys'] ]['vlaninterfaces'][ $vli['Vlan']['number'] ][ $cnt ]['ipv6enabled'] = $vli['ipv6enabled'];
                    $custs[ $c['autsys'] ]['vlaninterfaces'][ $vli['Vlan']['number'] ][ $cnt ]['rsclient']    = $vli['rsclient'];
                }
            }
            
        }
    
        return $custs;
    }
    
    
}