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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class PatchPanelController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'PatchPanel';
        $this->frontend['name']            = 'PatchPanel';
        $this->frontend['pageTitle']       = 'Patch Panels';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'name', 'cabinetid', 'colo_ref', 'cable_type', 'interface_type', 'allow_duplex' ),

            'viewPanelRows'  => array( 'id', 'name', 'cabinetid', 'colo_ref', 'cable_type', 'interface_type', 'allow_duplex' ),

            'viewPanelTitle' => 'name',

            'sortDefaults' => array(
                'column' => 'name',
                'order'  => 'asc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),

            'name' => array(
                'label' => 'Name',
                'sortable' => 'true',
            ),

            'cabinetid' => array(
                'type' => 'hasOne',
                'model' => 'Cabinet',
                'controller' => 'cabinet',
                'field' => 'name',
                'label' => 'Cabinet',
                'sortable' => true
            ),

            'colo_ref' => array(
                'label' => 'Co-lo Ref',
                'sortable' => true
            ),

            'cable_type' => array(
                'label' => 'Cable Type',
                'sortable' => true,
                'type' => 'xlate',
                'xlator' => PatchPanelPort::$CABLES_TYPES
            ),

            'interface_type' => array(
                'label' => 'Interface',
                'sortable' => true,
                'type' => 'xlate',
                'xlator' => PatchPanelPort::$INTERFACE_TYPES
            ),

            'allow_duplex' => array(
                'label' => 'Duplex Allowed?',
                'sortable' => true
            )

        );

        parent::feInit();
    }




    public function addAction()
    {
        $pp = null;
        $form = new INEX_Form_PatchPanel( null, false );

        $form->setAction(
            Zend_Controller_Front::getInstance()->getBaseUrl()
                . '/' . $this->getRequest()->getParam( 'controller' ) . "/add"
        );

        if( $this->_request->getParam( 'commit', null ) !== null )
        {

            if( $this->_request->getParam( 'cb_autogen' ) == '1' )
            {
                $form->getSubForm( 'AutoGenForm' )
                     ->getElement( 'num_ports' )
                     ->setRequired( true )
                     ->addValidator( 'Between', true, array( 'min' => 1, 'max' => 48 ) );
            }

	        if( $form->isValid( $_POST ) )
	        {
	            do
	            {
	                try
	                {
	                    $ppform = $form->getSubForm( 'PatchPanelForm' );

	                    if( $form->getSubForm( 'AutoGenForm' )->getElement( 'edit' )->isChecked() )
	                    {
	                        // user wants to edit each port first
                            $this->session->pp_form = $form;
                            return( $this->_forward( 'edit-ports' ) );
                        }

	                    $pp = new PatchPanel();
	                    $ppform->assignFormToModel( $pp, $this, false );
	                    $pp->save();

                        $this->getLogger()->notice( 'New patch panel created with ID: ' . $pp->id );

                        if( $form->getSubForm( 'AutoGenForm' )->getElement( 'cb_autogen' )->isChecked() )
                        {
					        for( $i = 1; $i <= $form->getSubForm( 'AutoGenForm' )->getElement( 'num_ports' )->getValue(); $i++ )
					        {
					            foreach( PatchPanelPort::$SIDES as $side_i => $side_v )
					            {
                                    $mport = new PatchPanelPort();
                                    $mport->port       = $i;
                                    $mport->side       = $side_i;
                                    $mport->type       = $ppform->interface_type->getValue();
                                    $mport->colo_ref   = $ppform->colo_ref->getValue() . ".$i";
                                    $mport->cable_type = $ppform->cable_type->getValue();
                                    $mport->PatchPanel = $pp;
                                    $mport->save();
                                    $this->getLogger()->notice( "New patch panel port created with ID {$mport->id} for patch panel #{$pp->id}" );
					            }
					        }

					        $this->view->message = new INEX_Message( "The new patch panel and its ports have been added", "success" );
                        }
                        else
                        {
                            $this->view->message = new INEX_Message( "The new patch panel has been added without ports", "success" );
                        }

	                    return( $this->_forward( 'list' ) );
	                }
	                catch( Exception $e )
	                {
	                    Zend_Registry::set( 'exception', $e );
	                    return( $this->_forward( 'error', 'error' ) );
	                }
	            }while( false );
	        }

        }

        $this->view->isEdit = false;
        $this->view->form   = $form->render( $this->view );
        $this->view->pp     = $pp;

        $this->view->display( $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'addEdit.tpl' );
    }



    public function editAction()
    {
        // does the requested patch panel exist?
        if( !( $pp = Doctrine_Core::getTable( 'PatchPanel' )->find( $this->getRequest()->getParam( 'id', null ) ) ) )
        {
            $this->view->message = new INEX_Message( "No patch panel with the requested ID exists", "error" );
            return( $this->_forward( 'list' ) );
        }

        $orig_colo_ref = $pp->colo_ref;

        $form = new INEX_Form_PatchPanel( null, true );

        $form->removeSubForm( 'AutoGenForm' );

        $form->setAction(
            Zend_Controller_Front::getInstance()->getBaseUrl()
                . '/' . $this->getRequest()->getParam( 'controller' ) . "/edit/id/" . $pp->id
        );

        $form->getSubForm( 'PatchPanelForm' )->assignModelToForm( $pp, $this );

        if( $this->_request->getParam( 'commit', null ) !== null && $form->isValid( $_POST ) )
        {
            $ppform = $form->getSubForm( 'PatchPanelForm' );

            $ppform->assignFormToModel( $pp, $this, false );
            $pp->save();

            $this->getLogger()->notice( "Patch panel #{$pp->id} edited" );

            if( $orig_colo_ref != $pp->colo_ref )
            {
                // need to update the ports colo references
                foreach( Doctrine_Core::getTable( 'PatchPanelPort' )->findByPatchPanelId( $pp->id ) as $port )
                {
                    $port->colo_ref = "{$pp->colo_ref}.{$port->port}";
                    $port->save();
                }
                $this->getLogger()->notice( "Patch panel #{$pp->id} has had it's colo-ref edited - ports have been updated also" );
                $this->view->message = new INEX_Message( "Patch panel {$pp->name} edited and its ports colocation references have been updated", "success" );
            }
            else
            {
                $this->view->message = new INEX_Message( "Patch panel {$pp->name} edited", "success" );
            }

            return( $this->_forward( 'list' ) );
        }

        $this->view->form   = $form->render( $this->view );
        $this->view->pp     = $pp;
        $this->view->isEdit = true;

        $this->view->display( $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'addEdit.tpl' );
    }


    public function editPortsAction()
    {
        $cancelLocation = 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . '://' . $_SERVER['SERVER_NAME'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . $this->getRequest()->getParam( 'controller' ) . '/list';

        if( $this->_request->getParam( 'commit2', null ) !== null )
            $submitted = true;
        else
            $submitted = false;

        // allow the user to edit the ports on a per port basis
        $pp = $this->session->pp_form;

        $ppps = new INEX_Form_PatchPanelPorts( null, false, $cancelLocation );

        $ppps->setAction(
            Zend_Controller_Front::getInstance()->getBaseUrl()
                . '/' . $this->getRequest()->getParam( 'controller' ) . "/edit-ports"
        );

        for( $i = 1; $i <= $pp->getSubForm( 'AutoGenForm' )->getElement( 'num_ports' )->getValue(); $i++ )
        {
            $index = ($i - 1) * 2;

            foreach( PatchPanelPort::$SIDES as $side_i => $side_v )
            {
                $pppf = new INEX_Form_SubForm_PatchPanelPort( null, false, null );

                $pppf->setName( 'PPP_' . $index );

                $pppf->port->setValue( $i );
                $pppf->side->setValue( $side_i );

	            $pppf->type->setValue( $pp->getSubForm( 'PatchPanelForm' )->interface_type->getValue() );
	            $pppf->cable_type->setValue( $pp->getSubForm( 'PatchPanelForm' )->cable_type->getValue() );
                $pppf->colo_ref->setValue( $pp->getSubForm( 'PatchPanelForm' )->colo_ref->getValue() . ".$i" );

                $pppf->setElementsBelongTo( 'PPP_' . $index );

                $ppps->addSubForm( $pppf, 'PPP_' . $index, $index );

                ++$index;
            }
        }

        if( $this->_request->getParam( 'commit2', null ) !== null && $ppps->isValid( $_POST ) )
        {
            // all validation checks out - add patch panel and ports.
            $pp_model = new PatchPanel();
            $pp->getSubForm( 'PatchPanelForm' )->assignFormToModel( $pp_model, $this, false );
            $pp_model->save();

            $this->getLogger()->notice( 'New patch panel created with ID: ' . $pp_model->id );

            foreach( $ppps->getSubForms() as $fport )
            {
                $mport = new PatchPanelPort();
                $mport->port       = $fport->port->getValue();
                $mport->side       = $fport->side->getValue();
                $mport->type       = $fport->type->getValue();
                $mport->colo_ref   = $fport->colo_ref->getValue();
                $mport->cable_type = $fport->cable_type->getValue();
                $mport->PatchPanel = $pp_model;
                $mport->save();
            }

            $this->getLogger()->notice( "New patch panel port created with ID {$mport->id} for patch panel #{$pp_model->id}" );
            $this->view->message = new INEX_Message( "The new patch panel and its ports have been added", "success" );

            return( $this->_forward( 'list' ) );

        }

        $this->view->form   = $ppps->render( $this->view );
        $this->view->sides  = PatchPanelPort::$SIDES;

        $this->view->display( 'patch-panel' . DIRECTORY_SEPARATOR . 'edit-ports.tpl' );
    }



    /**
     * Pre-deletion method to delete all patch panel ports before deleting the patch panel itself
     *
     */
    public function preDelete( $object = null )
    {
        Doctrine_Core::getTable( 'PatchPanelPort' )->findByPatchPanelId(
                $this->getRequest()->getParam( 'id' )
            )->delete();
    }
}

?>