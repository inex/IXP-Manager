<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
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
            
            // ordering is hard coded as follows in listGetData()
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

                'manufacturer'   => 'Manufacturer'
                
                //'firstseen'      => [
                //    'title'          => 'Last Seen',
                //    'type'           => self::$FE_COL_TYPES[ 'DATETIME' ]
                //]
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
            ->select( 'm' )
            ->from( '\\Entities\\MACAddress', 'm' )
            ->join( 'm.VirtualInterface', 'vi' )
            ->join( 'vi.VlanInterfaces', 'vli' )
            ->join( 'vi.Customer', 'c' )
            ->join( 'vi.PhysicalInterfaces', 'pi' )
            ->join( 'pi.SwitchPort', 'sp' )
            ->join( 'sp.Switcher', 's' );
            
        $qb->orderBy( 'c.name', 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'm.id = ?1' )->setParameter( 1, $id );

        $objects = $qb->getQuery()->getResult();
        
        $ouiRepo = $this->getD2R( '\Entities\OUI' );

        $data = [];
        
        foreach( $objects as $m )
        {
            $data[ $m->getId() ]['id']          = $m->getId();
            $data[ $m->getId() ]['firstseen']   = $m->getFirstseen();
            $data[ $m->getId() ]['lastseen']    = $m->getLastseen();
            $data[ $m->getId() ]['mac']         = $m->getMac();
            $data[ $m->getId() ]['customerid']  = $m->getVirtualInterface()->getCustomer()->getId();
            $data[ $m->getId() ]['customer']    = $m->getVirtualInterface()->getCustomer()->getName();
            $data[ $m->getId() ]['interfaceid'] = $m->getVirtualInterface()->getId();
            
            $data[ $m->getId() ]['interface']   =
                $m->getVirtualInterface()->getPhysicalInterfaces()[0]->getSwitchport()->getSwitcher()->getName() . " - "
                    . $m->getVirtualInterface()->getPhysicalInterfaces()[0]->getSwitchport()->getName();
            
            if( $m->getVirtualInterface()->getVlanInterfaces() )
            {
                foreach( $m->getVirtualInterface()->getVlanInterfaces() as $vli )
                {
                    if( !$vli->getVlan()->getPrivate() )
                    {
                        if( $vli->getIpv4Address() )
                            $data[ $m->getId() ]['ipv4'] = $vli->getIpv4Address()->getAddress();
                        
                        if( $vli->getIpv6Address() )
                            $data[ $m->getId() ]['ipv6'] = $vli->getIpv6Address()->getAddress();
                        
                        break;
                    }
                }
                    
                if( !isset( $data[ $m->getId() ]['ipv4'] ) )
                    $data[ $m->getId() ]['ipv4'] = '';
                
                if( !isset( $data[ $m->getId() ]['ipv6'] ) )
                    $data[ $m->getId() ]['ipv6'] = '';
            }

            if( $m->getMac() )
                $data[ $m->getId() ]['manufacturer'] = $ouiRepo->getOrganisation( strtolower( substr( $m->getMac(), 0, 6 ) ) );
        }
        
        return $data;
    }
    
}

