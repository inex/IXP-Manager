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
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PhysicalInterfaceController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\PhysicalInterface',
            'form'          => 'IXP_Form_Interface_Physical',
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
                
                $this->_feParams->viewColumns = array_merge(
                    $this->_feParams->listColumns,
                    [ 'monitorindex' => 'Monitor Index', 'notes' => 'Notes' ]
                );
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
                    pi.monitorindex AS monitorindex, pi.notes AS notes,
                    c.name AS customer, c.id AS custid,
                    s.name AS switch, s.id AS switchid,
                    vi.id AS vintid,
                    sp.name AS port, l.id AS locid, l.name AS location'
                )
            ->from( '\\Entities\\PhysicalInterface', 'pi' )
            ->leftJoin( 'pi.VirtualInterface', 'vi' )
            ->leftJoin( 'vi.Customer', 'c' )
            ->leftJoin( 'pi.SwitchPort', 'sp' )
            ->leftJoin( 'sp.Switcher', 's' )
            ->leftJoin( 's.Cabinet', 'cab' )
            ->leftJoin( 'cab.Location', 'l' );

        
        if( $id !== null )
            $qb->where( 'pi.id = ' . intval( $id ) );
        
        return $qb->getQuery()->getArrayResult();
    }
    
    
    /**
     * @param IXP_Form_Interface_Physical $form The form object
     * @param \Entities\PhysicalInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
        {
            $form->getElement( 'switchid' )->setValue( $object->getSwitchPort()->getSwitcher()->getId() );
            $form->getElement( 'switchportid' )->setValue( $object->getSwitchPort()->getId() );
            $form->getElement( 'preselectSwitchPort' )->setValue( $object->getSwitchPort()->getId() );
            $form->getElement( 'preselectPhysicalInterface' )->setValue( $object->getId() );
            $form->getElement( 'virtualinterfaceid' )->setValue( $object->getVirtualInterface()->getId() );
            
            if( $this->getParam( 'rtn', false ) == 'pi' )
                $form->setAction( OSS_Utils::genUrl( 'physical-interface', 'edit', false, [ 'id' => $object->getId(), 'rtn' => 'pi' ] ) );
            else
                $form->getElement( 'cancel' )->setAttrib( 'href', OSS_Utils::genUrl( 'virtual-interface', 'edit', false, [ 'id' => $object->getVirtualInterface()->getId() ] ) );
        }
        else // not editing
        {
            if( $this->getRequest()->isPost() && ( $vintid = ( isset( $_POST['virtualinterfaceid'] ) && $_POST['virtualinterfaceid'] ) ) )
                $vint = $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->find( $_POST['virtualinterfaceid'] );
            else if( ( $vintid = $this->getRequest()->getParam( 'vintid' ) ) !== null )
                $vint = $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->find( $vintid );
            
            if( !isset( $vint ) || !$vint )
            {
                $this->addMessage( 'You need a containing virtual interface before you add a physical interface', OSS_Message::ERROR );
                $this->redirect( 'virtual-interface/add' );
            }
            
            $form->getElement( 'virtualinterfaceid' )->setValue( $vint->getId() );
            $form->getElement( 'cancel' )->setAttrib( 'href', OSS_Utils::genUrl( 'virtual-interface', 'edit', false, [ 'id' => $vint->getId() ] ) );
            
            if( !$object->getMonitorindex() )
            {
                $form->getElement( 'monitorindex' )->setValue(
                    $this->getD2EM()->getRepository( '\\Entities\\PhysicalInterface' )->getNextMonitorIndex( $vint->getCustomer() )
                );
            }
        }
    }
    
    
    /**
     * @param IXP_Form_Interface_Physical $form The form object
     * @param \Entities\PhysicalInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setSwitchPort(
            $this->getD2EM()->getRepository( '\\Entities\\SwitchPort' )->find( $form->getElement( 'switchportid' )->getValue() )
        );
        
        $object->setVirtualInterface(
            $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->find( $form->getElement( 'virtualinterfaceid' )->getValue() )
        );

        return true;
    }
    
    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful add / edit operation.
     *
     * @param IXP_Form_Interface_Physical $form The form object
     * @param \Entities\PhysicalInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function addDestinationOnSuccess( $form, $object, $isEdit  )
    {
        if( $this->getParam( 'rtn', false ) == 'pi' )
            return false;
        
        $this->addMessage(
            'Physical interface successfuly ' . ( $isEdit ? 'edited.' : 'added.' ), OSS_Message::SUCCESS
        );
        
        $this->redirectAndEnsureDie( 'virtual-interface/edit/id/' . $object->getVirtualInterface()->getId() );
    }
    
    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful deletion operation.
     *
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function deleteDestinationOnSuccess()
    {
        if( $this->getParam( 'rtn', false ) == 'pi' )
            return false;
        
        $this->addMessage(
            'Physical interface deleted successfuly.', OSS_Message::SUCCESS
        );
        
        $this->redirectAndEnsureDie( 'virtual-interface/edit/id/' . $this->getParam( 'vintid' ) );
    }
    
}

