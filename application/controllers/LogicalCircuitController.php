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

class LogicalCircuitController extends INEX_Controller_FrontEnd
{

    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'LogicalCircuit';
        $this->frontend['name']            = 'LogicalCircuit';
        $this->frontend['pageTitle']       = 'Logical Circuits';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'our_ref', 'installed', 'removed', 'custid' ),

            'viewPanelRows'  => array( 'id', 'our_ref', 'installed', 'removed', 'custid', 'notes' ),

            'viewPanelTitle' => 'our_ref',

            'sortDefaults' => array(
                'column' => 'our_ref',
                'order'  => 'asc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),

            'our_ref' => array(
                'label' => 'Our Reference',
                'sortable' => 'true',
            ),

            'installed' => array(
                'label' => 'Installed',
                'sortable' => true
            ),

            'removed' => array(
                'label' => 'Removed',
                'sortable' => true
            ),

            'custid' => array(
                'type' => 'hasOne',
                'model' => 'Cust',
                'controller' => 'customer',
                'field' => 'name',
                'label' => 'Customer',
                'sortable' => true
            )

        );

        parent::feInit();
    }

    public function addAction()
    {
        $this->view->physicalConnectionTypes = PhysicalCircuit::$TYPES_TEXT;

        // is this an attempt to edit?
        if( $this->getRequest()->getParam( 'id' ) !== NULL && is_numeric( $this->getRequest()->getParam( 'id' ) ) )
        {
            $isEdit = true;

            // is the ID valid?
            if( !( $object = Doctrine::getTable( $this->frontend['model'] )->find( $this->getRequest()->getParam( 'id' ) ) ) )
            {
                $this->view->message = new INEX_Message( 'No entry with ID: ' . $this->getRequest()->getParam( 'id' ) . " exists.", "failure" );
                return( $this->_forward( 'list' ) );
            }

            $this->view->object = $object;

            $form = $this->getForm( null, $isEdit );
            $form->assignModelToForm( $object, $this );
            $form->setAction(  Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . $this->getRequest()->getParam( 'controller' ) . '/edit/id/' . $this->getRequest()->getParam( 'id' ) );
            $form->getElement( 'commit' )->setLabel( 'Save Changes' );
        }
        else
        {
            $isEdit = false;

            $form = $this->getForm( null, $isEdit );
            $form->setAction( Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . $this->getRequest()->getParam( 'controller' ) . "/add" );
        }

        $this->view->isEdit = $isEdit;

        if( $this->getRequest()->getParam( 'return' ) !== null )
            $form->addElement( $form->createElement( 'hidden', 'return' )->setValue( $this->getRequest()->getParam( 'return' ) ) );

        if( $this->inexGetPost( 'commit' ) !== null && $form->isValid( $_POST ) )
        {
            do
            {
                try
                {
                    // non-standard validation checks
                    if( !$isEdit )
                    {
                        if( method_exists( $this, 'formValidateForAdd' ) )
                        if( $this->formValidateForAdd( $form ) === false )
                        break;

                        $object = new $this->frontend['model'];
                    }

                    $form->assignFormToModel( $object, $this, $isEdit );
                    $object->save();

                    if( $isEdit )
                    {
                        $this->logger->notice( $this->getName() . ' edited' );
                        $this->view->message = new INEX_Message( $this->getName() . ' edited', "success" );
                    }
                    else
                    {
                        $this->logger->notice( 'New ' . $this->getName() . ' created' );
                        $this->view->message = new INEX_Message( $this->getName() . ' added', "success" );
                    }

                    if( $this->getRequest()->getParam( 'return' ) !== null )
                        $this->_redirect( 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . '://' . $_SERVER['SERVER_NAME'] . $this->getRequest()->getParam( 'return' ) );
                    else
                        return( $this->_forward( 'list' ) );
                }
                catch( Exception $e )
                {
                    Zend_Registry::set( 'exception', $e );
                    return( $this->_forward( 'error', 'error' ) );
                }
            }while( false );
        }

        if( method_exists( $this, 'addEditPreDisplay' ) )
            $this->addEditPreDisplay( $form );

        $this->view->form   = $form->render( $this->view );
        $this->view->object = $object;

        if( $this->view->templateExists( $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'addEdit.tpl' ) )
            $this->view->display( $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'addEdit.tpl' );
        else
            $this->view->display( 'frontend' . DIRECTORY_SEPARATOR . 'addEdit.tpl' );
    }

}

?>