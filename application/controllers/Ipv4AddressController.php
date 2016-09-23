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
 * Controller: Manage IPv4 Addresses
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Ipv4AddressController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\IPv4Address',
            'pagetitle'     => 'IPv4 Addresses',
        
            'titleSingular' => 'IPv4 Address',
            'nameSingular'  => 'an IPv4 address',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
            
            'readonly'      => false,
        
            'listOrderBy'    => 'id',
            'listOrderByDir' => 'ASC',
        
            'listColumns'    => [
        
                'id'        => [ 'title' => 'UID', 'display' => false ],
                'address'   => 'Address',
                'hostname'  => 'Hostname',
                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'view',
                    'idField'    => 'customerid'
                ],
            ]
        ];
            
        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'vlan'  => [
                    'title'      => 'VLAN',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'vlan',
                    'action'     => 'view',
                    'idField'    => 'vlanid'
                ]
            ]
        );
    }
    
    
    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {

        if( $this->getParam( 'ixp', false ) )
            $this->view->ixp = $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $this->getParam( 'ixp' ) );
        else
        {
            $ixp = $this->getD2R( "\\Entities\\IXP" )->findAll();
            if( $ixp )
                $this->view->ixp = $ixp = $ixp[0];
            else
                $ixp = false;
        }

        $this->view->vlans = $vlans = $this->getD2EM()->getRepository( '\\Entities\\Vlan' )->getNames( 1, $ixp );

        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'ip.id as id, ip.address as address,
                v.name AS vlan,
                vli.' . ( $this->_feParams->entity == '\\Entities\\IPv4Address' ? 'ipv4' : 'ipv6' ) . 'hostname AS hostname,
                c.name AS customer, c.id AS customerid'
            )
            ->from( $this->_feParams->entity, 'ip' )
            ->leftJoin( 'ip.Vlan', 'v' )
            ->leftJoin( 'ip.VlanInterface', 'vli' )
            ->leftJoin( 'vli.VirtualInterface', 'vi' )
            ->leftJoin( 'vi.Customer', 'c' );

        if( $ixp )
        {
            $qb->leftJoin( 'v.Infrastructure', 'inf' )
                ->leftJoin( 'inf.IXP', 'ixp' )
                ->andWhere( 'ixp.id = ?3' )
                ->setParameter( 3, $ixp->getId() );
        }
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'ip.id = ?1' )->setParameter( 1, $id );
    
        
        $vid = false;
        if( !( ( $vid = $this->getParam( 'vlan', false ) ) && isset( $vlans[$vid] ) ) )
        {
            if( $ixp && $vlans )
                $vid = array_keys( $vlans )[ 0 ];
            else if( isset( $this->_options['identity']['vlans']['default'] ) && isset( $vlans[ $this->_options['identity']['vlans']['default'] ] ) )
                $vid = $this->_options['identity']['vlans']['default'];
        }
        
        if( $vid )
        {
            $this->view->vid = $vid;
            $qb->andWhere( 'v.id = ?2' )->setParameter( 2, $vid );

            if( !$ixp )
            {
                $vlan = $this->getD2R( "\\Entities\\Vlan" )->find( $vid );
                $this->view->ixp = $ixp = $vlan->getInfrastructure()->getIXP();
            }
        }

        if( $this->multiIXP() )
            $this->view->ixpNames = $this->getD2R( '\\Entities\\IXP' )->getNames( $this->getUser() );
        
        return $qb->getQuery()->getResult();
    }
    
    public function addAction()
    {
        $this->view->form = $form = new IXP_Form_AddAddresses();
        
        if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
        {
            $addrfam = $form->getValue( 'type' );
            $numaddrs = intval( $_POST['numaddrs'] );
            
            if( !( $vlan = $this->getD2EM()->getRepository( '\\Entities\\Vlan' )->find( $form->getValue( 'vlanid' ) ) ) )
                throw new IXP_Exception( 'Unknown VLAN in request' );
            
            for( $i = 0; $i < $numaddrs; $i++ )
            {
                if( $addrfam == 'IPv4' )
                    $ip = new \Entities\IPv4Address();
                else if( $addrfam == 'IPv6' )
                    $ip = new \Entities\IPv6Address();
                else
                    throw new IXP_Exception( 'Invalid address family' );

                $ip->setVlan( $vlan );
                $ip->setAddress( trim( $_POST[ 'np_name' . $i ] ) );
                
                $this->getD2EM()->persist( $ip );
            }
            
            $this->getD2EM()->flush();
                             
            $msg = "{$numaddrs} new {$addrfam} addresses created for VLAN {$vlan->getName()}.";
            $this->getLogger()->info( $msg );
            $this->addMessage( $msg, OSS_Message::SUCCESS );
            
            if( $addrfam == 'IPv4' )
                $redir = 'ipv4';
            else
                $redir = 'ipv6';

            $this->redirect( strtolower( $addrfam ) . '-address/list/vlan/' . $vlan->getId() );
        }
    }
    
    public function editAction()
    {
        $this->addMessage(
            'Editing IP addresses is not currently implemented. '
                . 'You can acheive the same outcome by deleting / adding.',
            OSS_Message::INFO
        );
        $this->forward( 'list' );
    }

    public function ajaxGetForVlanAction()
    {
        if( $this->getRequest()->getControllerName() == 'ipv6-address' )
        {
            $af = 'ipv6'; $entity = 'IPv6Address';
        }
        else
        {
            $af = 'ipv4'; $entity = 'IPv4Address';
        }
        
        $dql = "SELECT {$af}.id AS id, {$af}.address AS address
                    FROM \\Entities\\{$entity} {$af}
                        LEFT JOIN {$af}.Vlan v
                        LEFT JOIN {$af}.VlanInterface vli
                    WHERE
                        v.id = ?1 ";
    
        if( $this->getParam( 'vliid', null ) !== null )
            $dql .= 'AND ( vli.id IS NULL OR vli.id = ?2 )';
        else
            $dql .= 'AND vli.id IS NULL';
    
        $dql .= " ORDER BY {$af}.id ASC";
    
        $query = $this->getD2EM()->createQuery( $dql );
        $query->setParameter( 1, $this->getParam( 'vlanid', 0 ) );
    
        if( $this->getParam( 'vliid', null ) !== null )
            $query->setParameter( 2, $this->getParam( 'vliid' ) );
    
        $ips = $query->getArrayResult();

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody( Zend_Json::encode( $ips ) )
            ->sendResponse();
    
        die(); //FIXME I shouldn't have to die() here...
    }
    
    
    /**
     * Function which can be over-ridden to perform any pre-deletion tasks
     *
     * You can stop the deletion by returning false but you should also add a
     * message to explain why.
     *
     * @param \Entities\IPv4Address $object The Doctrine2 entity to delete
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete( $object )
    {
        if( $object->getVlanInterface() )
        {
            $this->addMessage(
                'This IP address is assigned to a VLAN interface. Please remove <a href="'
                    . OSS_Utils::genUrl( 'vlan-interface', 'edit', false,
                        [ 'id' => $object->getVlanInterface()->getId() ]
                    ) . '">this assignment</a> before deleting the address.',
                OSS_Message::ERROR
            );
            return false;
        }
        
        return true;
    }

}

