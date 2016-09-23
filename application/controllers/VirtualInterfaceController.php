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
 * Controller: Manage virtual interfaces
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VirtualInterfaceController extends IXP_Controller_FrontEnd
{

    use IXP_Controller_Trait_Interfaces;


    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\VirtualInterface',
            'form'          => 'IXP_Form_Interface_Virtual',
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
                    COUNT( ppi.id ) as ppid, COUNT( pfi.id ) as fpid,
                    l.name AS location, s.name AS switch, sp.type as type,
                    sp.name AS port, SUM( pi.speed ) AS speed, COUNT( pi.id ) AS ports'
                 )
            ->from( '\\Entities\\VirtualInterface', 'vi' )
            ->leftJoin( 'vi.Customer', 'c' )
            ->leftJoin( 'vi.PhysicalInterfaces', 'pi' )
            ->leftJoin( 'pi.SwitchPort', 'sp' )
            ->leftJoin( 'pi.PeeringPhysicalInterface', 'ppi' )
            ->leftJoin( 'pi.FanoutPhysicalInterface', 'pfi' )
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

            if( $pi->getSwitchPort()->getType() == \Entities\SwitchPort::TYPE_PEERING && $pi->getFanoutPhysicalInterface() )
            {
                $pi->getSwitchPort()->setPhysicalInterface( null );
                $pi->getFanoutPhysicalInterface()->getSwitchPort()->setType( \Entities\SwitchPort::TYPE_PEERING );
            }
            else if( $pi->getSwitchPort()->getType() == \Entities\SwitchPort::TYPE_FANOUT && $pi->getPeeringPhysicalInterface() )
            {
                if( $this->getParam( 'related', false ) )
                    $this->removeRelatedInterface( $pi );

                $pi->getPeeringPhysicalInterface()->setFanoutPhysicalInterface( null );
            }
            $this->getD2EM()->remove( $pi );

            if( $this->getParam( 'related', false ) && $pi->getRelatedInterface() )
                $this->removeRelatedInterface( $pi );
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
            $vi->removeMACAddresses( $ma );
            $this->getD2EM()->remove( $ma );
        }

        return true;
    }

    /**
     * @param IXP_Form_Interface_Virtual $form The form object
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

            $this->view->ptypes   = \Entities\SwitchPort::$TYPES;
            $this->view->cust     = $object->getCustomer();
            $this->view->physInts = $object->getPhysicalInterfaces();
            $this->view->vlanInts = $object->getVlanInterfaces();
            $this->view->type     = $object->getType();
        }
    }

    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful add / edit operation.
     *
     * By default it returns `false`.
     *
     * On `false`, the default action (`index`) is called and a standard success message is displayed.
     *
     *
     * @param OSS_Form $form The form object
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function addDestinationOnSuccess( $form, $object, $isEdit  )
    {
        $this->addMessage( 'Virtual interface successfully ' . ( $isEdit ? ' edited.' : ' added.' ), OSS_Message::SUCCESS );
        $this->redirect( 'virtual-interface/edit/id/' . $object->getId() );
    }


    /**
     * @param IXP_Form_Interface_Virtual $form The form object
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


    public function addWizardAction()
    {
        $this->view->form = $form = new IXP_Form_Interface_AddWizard();
        if( $this->resellerMode() )
            $this->view->resoldCusts = json_encode( $this->getD2R( "\\Entities\\Customer" )->getResoldCustomerNames() );

        $form->enableFanoutPort( $this->resellerMode() );

        // Process a submitted form if it passes initial validation
        if( $this->getRequest()->isPost() )
        {
            // make sure we have a custid
            if( !isset( $_POST['custid'] ) && $this->getParam( 'custid', false ) )
                $_POST['custid'] = $this->getParam( 'custid' );

            if( $form->isValid( $_POST ) )
            {
                // check customer information
                if( !( $cust = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $form->getValue( 'custid' ) ) ) )
                {
                    $form->getElement( 'custid' )->addError( 'Invalid customer' );
                }
                else
                {
                    $vi = new \Entities\VirtualInterface();
                    $form->assignFormToEntity( $vi, $this, false );
                    $vi->setCustomer( $cust );
                    $this->getD2EM()->persist( $vi );

                    $pi = new \Entities\PhysicalInterface();
                    $form->assignFormToEntity( $pi, $this, false );
                    $pi->setVirtualInterface( $vi );

                    $sp = $this->getD2R( '\\Entities\\SwitchPort' )->find( $form->getValue( 'switchportid' ) );
                    $sp->setType( \Entities\SwitchPort::TYPE_PEERING );
                    $pi->setSwitchPort( $sp );

                    $pi->setMonitorindex(
                        $this->getD2EM()->getRepository( '\\Entities\\PhysicalInterface' )->getNextMonitorIndex( $cust )
                    );
                    $this->getD2EM()->persist( $pi );

                    if( $form->getElement( 'fanout' ) )
                    {
                        if( !$this->processFanoutPhysicalInterface( $form, $pi, $vi ) )
                            return false;

                        if( $pi->getRelatedInterface() )
                        {
                            $pi->getRelatedInterface()->setSpeed( $form->getValue( "speed" ) );
                            $pi->getRelatedInterface()->setStatus( $form->getValue( "status" ) );
                            $pi->getRelatedInterface()->setDuplex( $form->getValue( "duplex" ) );
                        }
                    }

                    $vli = new \Entities\VlanInterface();
                    $form->assignFormToEntity( $vli, $this, false );

                    $vli->setVlan(
                        $this->getD2EM()->getRepository( '\\Entities\\Vlan' )->find( $form->getElement( 'vlanid' )->getValue() )
                    );

                    if( !$this->setIp( $form, $vi, $vli, false ) || !$this->setIp( $form, $vi, $vli, true ) )
                        return false;

                    $vli->setVirtualInterface( $vi );
                    $this->getD2EM()->persist( $vli );

                    $this->getD2EM()->flush();

                    $this->getLogger()->info( 'New virtual, physical and VLAN interface created for ' . $cust->getName() );
                    $this->addMessage( "New interface created!", OSS_Message::SUCCESS );

                    $this->redirect( 'customer/overview/tab/ports/id/' . $cust->getId() );
                }
            }
        }

        if( !isset( $cust ) && ( $cid = $this->getParam( 'custid', false ) ) )
            $cust = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $cid );

        if( !$this->getRequest()->isPost() )
        {
            if( isset( $cust ) )
            {
                $form->getElement( 'custid' )->setValue( $cust->getId() );
                $form->getElement( 'maxbgpprefix' )->setValue( $cust->getMaxprefixes() );
            }
        }

        if( $this->getParam( 'rtn', false ) == 'ov' )
        {
            $form->getElement( 'custid' )->setAttrib( 'disabled', 'disabled' );
            $form->getElement( 'cancel' )->setAttrib( 'href',
                OSS_Utils::genUrl( 'customer', 'view', false, [ 'id' =>  $this->getParam( 'custid' ) ] )
            );
        }
        else
        {
            $form->getElement( 'cancel' )->setAttrib( 'href', OSS_Utils::genUrl( 'virtual-interface', 'list' ) );
        }
    }
}
