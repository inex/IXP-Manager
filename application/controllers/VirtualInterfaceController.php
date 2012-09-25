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
            'pagetitle'     => '(Virtual) Interfaces',
        
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
                    
                    'port'       => [
                        'title'         => 'Port',
                        'type'          => self::$FE_COL_TYPES[ 'SCRIPT' ],
                        'script'        => 'virtual-interface/list-column-port.phtml'
                    ],

                    'speed'         => 'Speed'
                ];
                break;
    
            case \Entities\User::AUTH_CUSTADMIN:
            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }
    
    }
    
    public function viewAction()
    {
        $this->forward( 'add' );
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
                    c.name AS customer, c.id AS custid, c.shortname AS shortname,
                    l.name AS location, s.name AS switch,
                    sp.name AS port, SUM( pi.speed ) AS speed, COUNT( pi.id ) AS ports'
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
    
    
    /**
     * @param INEX_Form_Interface_Virtual $form The form object
     * @param \Entities\VirtualInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
        {
            $form->getElement( 'custid' )->setValue( $object->getCustomer()->getId() );

            $this->view->physInts = $object->getPhysicalInterfaces();
            $this->view->vlanInts = $object->getVlanInterfaces();
        }
    }
    
    
    /**
     * @param INEX_Form_Interface_Virtual $form The form object
     * @param \Entities\VirtualInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setCustomer(
            $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $form->getElement( 'custid' )->getValue() )
        );
    
        return true;
    }
    

    /*
    
    public function quickAddAction()
    {
    $f = new INEX_Form_QuickAddInterface( null, false, 'virtual-interface' );
    
    // Process a submitted form if it passes initial validation
    if( $this->inexGetPost( 'commit' ) !== null && $f->isValid( $_POST ) )
    {
    do
    {
    // check customer information
    if( !( $c = Doctrine_Core::getTable( 'Cust' )->find( $f->getValue( 'custid' ) ) ) )
    {
    $f->getElement( 'custid' )->addError( 'Invalid customer' );
    break;
    }
    
    // create the entities
    $conn = Doctrine_Manager::connection();
    $conn->beginTransaction();
    
    try
    {
    // virtual interface
    $vi = new Virtualinterface();
    $f->assignFormToModel( $vi, $this, false );
    $vi->save();
    
    // and now a physical interface
    $pi                       = new Physicalinterface();
    
    $f->assignFormToModel( $pi, $this, false );
    
    $pi['virtualinterfaceid'] = $vi['id'];
    
    $nextMonitorIndex = Doctrine_Query::create()
    ->select( 'MAX( pi.monitorindex )' )
    ->from( 'Physicalinterface pi' )
    ->leftJoin( 'pi.Virtualinterface vi' )
    ->where( 'vi.custid = ?', $c['id'] )
    ->execute()
    ->toArray();
    
    $pi['monitorindex'] = $nextMonitorIndex[0]['MAX'] + 1;
    $pi->save();
    
    
    // and lastly, the VLAN interface
    $vli = new Vlaninterface();
    
    $f->assignFormToModel( $vli, $this, false );
    
    $vli['virtualinterfaceid'] = $vi['id'];
    
    $vli->save();
    
    $conn->commit();
    }
    catch( Exceltion $e )
    {
    $conn->rollback();
    }
    
    $this->getLogger()->notice( 'New virtual, physical and VLAN interface created' );
    $this->session->message = new INEX_Message( "New interface added", "success" );
    $this->_redirect( 'virtual-interface/edit/id/' . $vi['id'] );
    
    }while( false );
    
    $loc = $this->genUrl( 'customer', 'dashboard', array( 'id' => $f->getElement( 'custid' )->getValue() ) );
    }
    else if( $this->_getParam( 'commit', false ) === false && $cid = $this->_getParam( 'custid', false ) )
    {
    $f->getElement( 'custid' )->setValue( $cid );
    $loc = $this->genUrl( 'customer', 'dashboard', array( 'id' => $cid ) );
    }
    else
    {
    $loc = $this->genUrl( 'virtual-interface', 'list' );
    }
    
    if( !$this->_getParam( 'commit', false ) )
    {
    // make BGP MD5 easy
    $f->getElement( 'ipv4bgpmd5secret' )->setValue( INEX_String::random() );
    $f->getElement( 'ipv6bgpmd5secret' )->setValue(  $f->getElement( 'ipv4bgpmd5secret' )->getValue() );
    
    if( $cid = $this->_getParam( 'custid', false ) )
    {
    $cust = Doctrine_Core::getTable( 'Cust' )->find( $cid );
    $f->getElement( 'maxbgpprefix' )->setValue( $cust['maxprefixes'] );
    }
    }
    
    
    $f->getElement( 'cancel' )->setAttrib( 'onClick',
    "parent.location='{$loc}'"
    );
    
    $this->view->form   = $f; //->render( $this->view );
    
    $this->view->display( 'vlan-interface' . DIRECTORY_SEPARATOR . 'quick-add.tpl' );
    
    }
    
    protected function _deleteSetReturnOnSuccess()
    {
    if( $vid = $this->_getParam( 'virtualinterfaceid', false ) )
        return "virtual-interface/edit/id/{$vid}";
    
    return 'vlan-interface/list';
    }
    
    protected function _addEditSetReturnOnSuccess( $form, $object )
    {
    return "virtual-interface/edit/id/{$object['virtualinterfaceid']}";
    }
    
    protected function getForm( $options = null, $isEdit = false )
    {
    $formName = "INEX_Form_{$this->frontend['name']}";
    
    if( $vid = $this->_getParam( 'virtualinterfaceid', false ) )
        $cancelLocation = $this->genUrl( 'virtual-interface', 'edit', array( 'id' => $vid ) );
    else
        $cancelLocation = $this->genUrl( 'vlan-interface', 'list' );
    
    return new $formName( $options, $isEdit, $cancelLocation );
    }
    */
    
    
}

