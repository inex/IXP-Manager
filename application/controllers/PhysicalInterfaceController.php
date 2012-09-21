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
 * Controller: Physical Interfaces
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PhysicalInterfaceController extends INEX_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\PhysicalInterface',
            'form'          => 'INEX_Form_Interface_Physical',
            'pagetitle'     => 'Physical Interfaces',
        
            'titleSingular' => 'Physical Interface',
            'nameSingular'  => 'a physical interface',
        
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
        
                    'location'  => [
                        'title'      => 'Location',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'location',
                        'action'     => 'view',
                        'idField'    => 'locid'
                    ],
        
                    'switch'  => [
                        'title'      => 'Switch',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'switch',
                        'action'     => 'view',
                        'idField'    => 'switchid'
                    ],
                    
                    'port'          => 'Port',
                    
                    'status'        => [
                        'title'          => 'Status',
                        'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                        'xlator'         => \Entities\PhysicalInterface::$STATES
                    ],
                    
        
                    //'location'      => 'Location',
                    //'switch'        => 'Switch',
                    'speed'         => 'Speed',
                    'duplex'        => 'Duplex'
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
                'pi.id AS id, pi.speed AS speed, pi.duplex AS duplex, pi.status AS status,
                c.name AS customer, c.id AS custid,
                s.name AS switch, s.id AS switchid,
                sp.name AS port, l.id AS locid, l.name AS location'
            )
        ->from( '\\Entities\\PhysicalInterface', 'pi' )
        ->leftJoin( 'pi.VirtualInterface', 'vi' )
        ->leftJoin( 'vi.Customer', 'c' )
        ->leftJoin( 'pi.SwitchPort', 'sp' )
        ->leftJoin( 'sp.Switcher', 's' )
        ->leftJoin( 's.Cabinet', 'cab' )
        ->leftJoin( 'cab.Location', 'l' );
    
        return $qb->getQuery()->getArrayResult();
    }
    
    
    
    
    
    
    /**
     * addEditPreDisplay
     *
     * @param INEX_Form_PhysicalInterface The form object
     */
    function addEditPreDisplay( $form, $object )
    {
        // did we get a customer id from the provisioning controller?
        if( $this->_getParam( 'prov_virtualinterface_id', false ) )
        {
            $form->getElement( 'cancel' )->setAttrib( 'onClick',
                "parent.location='" . $this->config['identity']['ixp']['url']
                    . '/provision/interface-overview/id/' . $this->session->provisioning_interface_active_id . "'"
            );
        }

        // if provisioning and we're creating an interface:
        if( $this->_getParam( 'prov_physicalinterface_id' ) !== null )
        {
            $form->getElement( 'status' )->setValue( Physicalinterface::STATUS_XCONNECT );
        }


        if( $this->getRequest()->getParam( 'virtualinterfaceid' ) !== null )
        {
            $form->getElement( 'virtualinterfaceid' )->setValue( $this->getRequest()->getParam( 'virtualinterfaceid' ) );

            if( $form->getElement( 'monitorindex' )->getValue() == '' )
            {
                $virtualInterface = Doctrine::getTable( 'Virtualinterface' )->find( $this->getRequest()->getParam( 'virtualinterfaceid' ) );

                $nextMonitorIndex = Doctrine_Query::create()
	                ->select( 'MAX( pi.monitorindex )' )
	                ->from( 'Physicalinterface pi' )
	                ->leftJoin( 'pi.Virtualinterface vi' )
	                ->where( 'vi.custid = ?', $virtualInterface['custid'] )
	                ->execute()
	                ->toArray();

                $form->getElement( 'monitorindex' )->setValue( $nextMonitorIndex[0]['MAX'] + 1 );
            }
        }
    }
    
    
    protected function formPrevalidate( $form, $isEdit, $object )
    {
        // set the switch and port fields of the form if we're editing
        if( $isEdit )
        {
            $form->getElement( 'switch_id')->setValue( $object->Switchport->SwitchTable['id'] );
            $form->getElement( 'preselectSwitchPort' )->setValue( $object->Switchport['id'] );
            $form->getElement( 'preselectPhysicalInterface' )->setValue( $object['id'] );
        }
    }
    
    public function ajaxGetPortsAction()
    {
        $switch = Doctrine::getTable( 'SwitchTable' )->find( $this->_getParam( 'switchid', null ) );

        $ports = '';
        
        if( $switch )
        {
            $ports = Doctrine_Query::create()
                ->from( 'Switchport sp' )
                ->leftJoin( 'sp.Physicalinterface pi' )
                ->where( 'sp.switchid = ?', $switch['id'] );
                
            if( $this->_getParam( 'id', null ) !== null )
                $ports = $ports->andWhere( '( pi.id IS NULL OR pi.id = ? )', $this->_getParam( 'id' ) );
            else
                $ports = $ports->andWhere( 'pi.id IS NULL' );
                
            $ports = $ports->orderBy( 'sp.id' )
                ->fetchArray();
                
            foreach( $ports as $i => $p )
                $ports[$i]['type'] = Switchport::$TYPE_TEXT[ $p['type'] ];
        }
        
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody( Zend_Json::encode( $ports ) )
            ->sendResponse();
        exit();
    }

}

