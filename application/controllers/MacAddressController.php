<?php

/*
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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
 * Controller: Manage known MAC addresses
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MacAddressController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\MacAddress',
            'pagetitle'     => 'Known MAC Addresses',
    
            'titleSingular' => 'MAC Address',
            'nameSingular'  => 'a MAC address',
    
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'readonly'      => true,
            
            'listOrderBy'    => 'customer',
            'listOrderByDir' => 'ASC',
        
            'listColumns'    => [
                'id'        => [ 'title' => 'UID', 'display' => false ],
                
                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'view',
                    'idField'    => 'customerid'
                ],
                                
                'interface'  => [
                    'title'      => 'Interface',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'virtual-interface',
                    'action'     => 'edit',
                    'idField'    => 'interfaceid'
                ],
                
                'ipv4'           => 'IPv4',
                'ipv6'           => 'IPv6',
                'mac'            => 'MAC Address',
                
                'firstseen'      => [
                    'title'          => 'First Seen',
                    'type'           => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                
                'lastseen'      => [
                    'title'          => 'Last Seen',
                    'type'           => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        ];
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = $this->_feParams->listColumns;
    }

    /**
     * Provide array of MAC addresses for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select(
                'm.id AS id, m.firstseen AS firstseen, m.lastseen AS lastseen, m.mac AS mac,
                c.id AS customerid, c.name AS customer,
                vi.id AS interfaceid,
                CONCAT( CONCAT( s.name, \' - \' ),  sp.name ) AS interface,
                ip4.address AS ipv4, ip6.address AS ipv6'
            )
            ->from( '\\Entities\\MACAddress', 'm' )
            ->join( 'm.VirtualInterface', 'vi' )
            ->join( 'vi.VlanInterfaces', 'vli' )
            ->join( 'vli.IPv4Address', 'ip4' )
            ->join( 'vli.IPv6Address', 'ip6' )
            ->join( 'vi.Customer', 'c' )
            ->join( 'vi.PhysicalInterfaces', 'pi' )
            ->join( 'pi.SwitchPort', 'sp' )
            ->join( 'sp.Switcher', 's' );
            
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'm.id = ?1' )->setParameter( 1, $id );
    
        return $qb->getQuery()->getResult();
    }
    
}

