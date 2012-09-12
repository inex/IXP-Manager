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
 * Controller: Manage switches (and other devices)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchController extends INEX_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Switcher',
            'form'          => 'INEX_Form_Switch',
            'pagetitle'     => 'Switches',
        
            'titleSingular' => 'Switch',
            'nameSingular'  => 'a switch',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC',
        
            'listColumns'    => [
        
                'id'        => [ 'title' => 'UID', 'display' => false ],
                'name'           => 'Name',
                
                'cabinet'  => [
                    'title'      => 'Cabinet',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'cabinet',
                    'action'     => 'view',
                    'idField'    => 'cabinetid'
                ],
            
                'vendor'  => [
                    'title'      => 'Vendor',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'vendor',
                    'action'     => 'view',
                    'idField'    => 'vendorid'
                ],
                
                'model'          => 'Model',
                'ipv4addr'       => 'IPv4 Address',
                'infrastructure' => 'Infrastructure',
                'active'         => 'Active'
            ]
        ];
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'ipv6addr'       => 'IPv6 Address',
                'snmppasswd'     => 'SNMP Community',
                'switchtype'     => 'Type',
                'notes'          => 'Notes'
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
        $qb = $this->getD2EM()->createQueryBuilder()
        ->select( 's.id AS id, s.name AS name,
            s.ipv4addr AS ipv4addr, s.ipv6addr AS ipv6addr, s.snmppasswd AS snmppasswd,
            s.infrastructure AS infrastructure, s.switchtype AS switchtype, s.model AS model,
            s.active AS active, s.notes AS notes,
            v.id AS vendorid, v.name AS vendor, c.id AS cabinetid, c.name AS cabinet'
        )
        ->from( '\\Entities\\Switcher', 's' )
        ->leftJoin( 's.Cabinet', 'c' )
        ->leftJoin( 's.Vendor', 'v' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 's.id = ?1' )->setParameter( 1, $id );
    
        return $qb->getQuery()->getResult();
    }
    
    
    /**
     *
     * @param INEX_Form_Cabinet $form The form object
     * @param \Entities\Cabinet $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
        {
            $form->getElement( 'cabinetid' )->setValue( $object->getCabinet()->getId() );
            $form->getElement( 'vendorid'  )->setValue( $object->getVendor()->getId()  );
        }
    }
    
    
    /**
     *
     * @param INEX_Form_Cabinet $form The form object
     * @param \Entities\Cabinet $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setCabinet(
            $this->getD2EM()->getRepository( '\\Entities\\Cabinet' )->find( $form->getElement( 'cabinetid' )->getValue() )
        );
    
        $object->setVendor(
            $this->getD2EM()->getRepository( '\\Entities\\Vendor' )->find( $form->getElement( 'vendorid' )->getValue() )
        );
    
        return true;
    }
    
    

    function portReportAction()
    {
        $this->view->switch = $switch = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->find( $this->getParam( 'id', 0 ) );

        if( $switch === null )
        {
            $this->addMessage( 'Unknown switch.', OSS_Message::ERROR );
            return $this->redirect( 'switch/list' );
        }
        
        $allports = $this->getD2EM()->createQuery(
                'SELECT sp.id AS spid, sp.name AS portname, sp.type AS porttype
                FROM \\Entities\\SwitchPort sp
                WHERE sp.Switcher = ?1
                ORDER BY spid ASC'
            )
            ->setParameter( 1, $switch )
            ->getResult();
        
        $ports = $this->getD2EM()->createQuery(
                'SELECT sp.id AS spid, sp.name AS portname, sp.type AS porttype,
                        pi.speed AS speed, pi.duplex AS duplex, c.name AS custname
            
                FROM \\Entities\\SwitchPort sp
                    JOIN sp.PhysicalInterface pi
                    JOIN pi.VirtualInterface vi
                    JOIN vi.Customer c
            
                WHERE sp.Switcher = ?1
        
                ORDER BY spid ASC'
            )
            ->setParameter( 1, $switch )
            ->getArrayResult();
            
        foreach( $allports as $id => $port )
        {
            if( isset( $ports[0] ) && $ports[0][ 'portname' ] == $port[ 'portname' ] )
                $allports[ $port[ 'portname' ] ] = array_shift( $ports );
            else
                $allports[ $port[ 'portname' ] ] = $port;
            
            $allports[ $port[ 'portname' ] ]['porttype'] = \Entities\SwitchPort::$TYPES[ $allports[ $port[ 'portname' ] ]['porttype'] ];
            
            unset( $allports[ $id ] );
        }
        
        $this->view->ports = $allports;
    }
    
    
    
    public function addPortsAction()
    {
        $f = new INEX_Form_SwitchPort_AddPorts( null, false, '' );
        
        $f->setAction( Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . $this->getRequest()->getParam( 'controller' ) . "/add-ports" );
 
        if( $this->inexGetPost( 'commit' ) !== null && $f->isValid( $_POST ) )
        {
            do
            {
                try
                {
                    $conn = Doctrine_Manager::connection();
                    $conn->beginTransaction();

                    for( $i = 0; $i < intval( $_POST['numports'] ); $i++ )
                    {
                        $sp = new Switchport();
                        
                        $sp['switchid'] = $f->getValue( 'switchid' );
                        $sp['type']     = intval( $_POST[ 'np_type' . $i ] );
                        $sp['name']     = trim( stripslashes( $_POST[ 'np_name' . $i ] ) );
                        
                        $sp->save();
                    }
                    
                    $conn->commit();
                     
                    $this->getLogger()->notice( intval( $_POST['numports'] ) . ' new switch ports created' );
                    $this->session->message = new INEX_Message( intval( $_POST['numports'] ) . ' new switch ports created', "success" );
                    $this->_redirect( 'switch-port/list/switchid/' . $f->getValue( 'switchid' ) );
                }
                catch( Exception $e )
                {
                    $conn->rollback();
                    
                    Zend_Registry::set( 'exception', $e );
                    return( $this->_forward( 'error', 'error' ) );
                }
            }while( false );
        }

        $this->view->form   = $f->render( $this->view );

        $this->view->display( 'switch-port/add-ports.tpl' );
    }

    
}

