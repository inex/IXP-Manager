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
 * Controller: Manage virtual interfaces
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VirtualInterfaceController extends INEX_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\VirtualInterface',
            'form'          => 'INEX_Form_Interface_Virtual',
            'pagetitle'     => 'Interfaces',
        
            'titleSingular' => 'Virtual Interface',
            'nameSingular'  => 'a virtual interface',
        
            'defaultAction' => 'list',
        
            'listOrderBy'    => 'customer',
            'listOrderByDir' => 'ASC',
        ];
    
        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id' => [ 'title' => 'UID', 'display' => false ],
        
                    'customer'  => [
                        'title'      => 'Customer',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'custid'
                    ],
    
                    'shortname'  => [
                        'title'      => 'Shortname',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'custid'
                    ],
    
                    'location'      => 'Location',
                    'switch'        => 'Switch',
                    'port'          => 'Port',
                    'speed'         => 'Speed'
                ];
                break;
    
            case \Entities\User::AUTH_CUSTADMIN:
            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }
    
    }
    
    
    
    /**
     * Provide array of virtual interfaces for the listAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select(
                    'vi.id,
                    c.name AS customer, c.shortname AS shortname,
                    l.name AS location, s.name AS switch,
                    sp.name AS port, SUM( pi.speed ) AS speed'
                 )
            ->from( '\\Entities\\VirtualInterface', 'vi' )
            ->leftJoin( 'vi.Customer', 'c' )
            ->leftJoin( 'vi.PhysicalInterfaces', 'pi' )
            ->leftJoin( 'pi.SwitchPort', 'sp' )
            ->leftJoin( 'sp.Switcher', 's' )
            ->leftJoin( 's.Cabinet', 'cab' )
            ->leftJoin( 'cab.Location', 'l' )
            ->groupBy( 'vi' );
    
        return $qb->getQuery()->getArrayResult();
    }
    
    

    /*
     * If deleting a virtual interface, we should also the delete the physical and vlan interfaces
     * if they exist.
     *
     * @param \Entities\VirtualInterface $vi The virtual interface to delete
     */
    protected function preDelete( $vi )
    {
        foreach( $vi->getPhysicalInterfaces() as $pi )
        {
            $this->getLogger()->info( "Deleting physical interface with id #{$pi->getId()} while deleting virtual interface #{$vi->getId()}" );
            $vi->removePhysicalInterface( $pi );
            $this->getD2EM()->remove( $pi );
        }
        
        foreach( $vi->getVlanInterfaces() as $vli )
        {
            $this->getLogger()->info( "Deleting VLAN interface with id #{$vli->getId()} while deleting virtual interface #{$vi->getId()}" );
            $vi->removeVlanInterface( $vli );
            $this->getD2EM()->remove( $vli );
        }
        
        foreach( $vi->getMACAddresses() as $ma )
        {
            $this->getLogger()->info( "Deleting MAC Address record #{$ma->getMac()} while deleting virtual interface #{$vi->getId()}" );
            $vi->removeMACAddresse( $ma );
            $this->getD2EM()->remove( $ma );
        }
        
        return true;
    }


    /*
    public function _customlist()
    {
        $dataQuery = Doctrine_Query::create()
	        ->from( 'Virtualinterface vi' )
	        ->leftJoin( 'vi.Cust c' )
	        ->leftJoin( 'vi.Physicalinterface pi' )
	        ->leftJoin( 'pi.Switchport sp' )
	        ->leftJoin( 'sp.SwitchTable s' )
	        ->leftJoin( 's.Cabinet cb' )
	        ->leftJoin( 'cb.Location l' )
	        ->orderBy( 'c.shortname ASC' );

        $results = $dataQuery->execute();

        $rows = array();
        foreach( $results as $r )
        {
            $row = array();
            
            $row["member"]      = $r['Cust']['name'];
            $row["memberid"]    = $r['Cust']['id'];
            $row["id"]          = $r['id'];
            $row["description"] = $r['description'];
            $row["shortname"]   = $r['Cust']['shortname'];
            $row["location"]    = $r['Physicalinterface'][0]['Switchport']['SwitchTable']['Cabinet']['Location']['name'];
            $row["locationid"]  = $r['Physicalinterface'][0]['Switchport']['SwitchTable']['Cabinet']['Location']['id'];
            $row["switch"]      = $r['Physicalinterface'][0]['Switchport']['SwitchTable']['name'];
            $row["switchid"]    = $r['Physicalinterface'][0]['Switchport']['SwitchTable']['id'];
            
            if( count( $r['Physicalinterface'] ) > 1 )
            {
                $row["port"]        = '(trunk)';
                $row["speed"]       = $r['Physicalinterface'][0]['speed'] * count( $r['Physicalinterface'] );
            }
            else
            {
                $row["port"]        = $r['Physicalinterface'][0]['Switchport']['name'];
                $row["speed"]       = $r['Physicalinterface'][0]['speed'];
            }
               
            $rows[] = $row;
        }

        return $rows;
    }*/
    
}

