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
 * INEX's version of Zend's Zend_Controller_Action implemented custom
 * functionality.
 *
 * All application controlers subclass this rather than Zend's version directly.
 *
 * @package INEX_Controller
 */
class INEX_Controller_FrontEnd extends INEX_Controller_Action
{

    /**
     * Do the user need to be authenticated to access the component?
     *
     * @var array Is an authenticated user required to access this?
     */
    protected $feAuthLevelRequired = User::AUTH_SUPERUSER;

    /**
     * Contains the configuration for the front end
     *
     * @var array Configuration for the front end
     */
    protected $frontend = null;

    /**
     * A dedicated namespace for this front end manager for storing information
     * between user actions.
     *
     * @var object Dedicated namespace for this controller
     */
    protected $feSession = null;


    /**
     * Override the INEX_Controller_Action's constructor (which is called
     * at the very beginning of this function anyway).
     *
     * @param object $request See Parent class constructer
     * @param object $response See Parent class constructer
     * @param object $invokeArgs See Parent class constructer
     */
    public function __construct(
            Zend_Controller_Request_Abstract  $request,
            Zend_Controller_Response_Abstract $response,
            array $invokeArgs = null )
    {
        // call the parent's version where all the Zend majic happens
        parent::__construct( $request, $response, $invokeArgs );
    }

    /**
     * Preflight checks - authenticated users, etc
     */
    public function preDispatch()
    {
        // is this controller enabled?
        if( isset( $this->config['controller'][$this->getRequest()->getParam( 'controller' )]['enabled'] )
                && !$this->config['controller'][$this->getRequest()->getParam( 'controller' )]['enabled'] )
        {
            return( $this->_forward( 'controller-disabled', 'index' ) );
        } 
        
        if( $this->feAuthLevelRequired != User::AUTH_PUBLIC )
        {
            if( isset( $this->frontend['authLevels'][$this->getRequest()->getActionName()] )
                && $this->frontend['authLevels'][$this->getRequest()->getActionName()] == User::AUTH_PUBLIC
            )
            {
                // do nothing -> action has public access
            }
            else if( !$this->auth->hasIdentity() )
            {
                // record the page we wanted
                $this->session->postAuthRedirect = $this->_request->getPathInfo();
                $this->_redirect( 'auth/login' );
            }
            else
            {
                if( isset( $this->frontend['authLevels'][$this->getRequest()->getActionName()] ) )
                    $authRequired = $this->frontend['authLevels'][$this->getRequest()->getActionName()];
                else
                    $authRequired = $this->feAuthLevelRequired;

                if( $this->identity['user']['privs'] < $authRequired )
                {
                    $this->logger->alert( "User {$this->identity['username']} was denied access to {$this->frontend['name']}" );
                    $this->view->message = new INEX_Message( "You are not authorised to view the requested page!", "alert" );
                    return( $this->_forward( 'index', 'index' ) );
                }
            }
        }
    }

    /**
     * Validation function that is called as part of the subclasses set up.
     *
     * This should check that all necessary variables are set.
     */
    final protected function feInit()
    {
        if( $this->frontend['name'] == null )
            throw new Exception( "You must set a name for the controller in the init() method." );


        if( $this->frontend['model'] == null )
            throw new Exception( "You must set the model in the init() method for the {$this->feName} controller." );

        if( $this->frontend['columns'] == null )
            throw new Exception( "You must set the view's column configuration in the init() method for the {$this->feName} controller." );

        $this->frontend['controller'] = $this->getRequest()->getParam( 'controller' );
        $this->feSession = $this->_bootstrap->getResource( 'namespace' );
        $this->view->frontend = $this->frontend;
    }


    public function indexAction()
    {
        $this->_forward( 'list' );
    }




    public function addAction()
    {
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
            $object = new $this->frontend['model'];
        }

        $this->view->isEdit = $isEdit;

        if( $this->getRequest()->getParam( 'return' ) !== null )
            $form->addElement( $form->createElement( 'hidden', 'return' )->setValue( $this->getRequest()->getParam( 'return' ) ) );

        // optional extra pre-validation code
        if( method_exists( $this, 'formPrevalidate' ) )
            $this->formPrevalidate( $form, $isEdit, $object );

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
                    }

                    $form->assignFormToModel( $object, $this, $isEdit );

                    if( method_exists( $this, 'addEditPreSave' ) )
                        $this->addEditPreSave( $object, $isEdit, $form );

                    $object->save();

                    if( method_exists( $this, 'addEditPostSave' ) )
                        $this->addEditPostSave( $object, $isEdit, $form );

                    if( $isEdit )
                    {
                        $this->logger->notice( $this->getName() . ' edited' );
                        $this->session->message = new INEX_Message( $this->getName() . ' edited', "success" );
                    }
                    else
                    {
                        $this->logger->notice( 'New ' . $this->getName() . ' created' );
                        $this->session->message = new INEX_Message( $this->getName() . ' added', "success" );
                    }

                    if( $this->getRequest()->getParam( 'return' ) !== null )
                        $this->_redirect( $this->getRequest()->getParam( 'return' ) . '/objectid/' . $object['id'] );
                    else if( method_exists( $this, '_addEditSetReturnOnSuccess' ) )
                        $this->_redirect( $this->_addEditSetReturnOnSuccess( $form, $object ) );
                    else
                        $this->_redirect( $this->getRequest()->getParam( 'controller' ) );
                }
                catch( Exception $e )
                {
                    Zend_Registry::set( 'exception', $e );
                    return( $this->_forward( 'error', 'error' ) );
                }
            }while( false );
        }

        if( method_exists( $this, 'addEditPreDisplay' ) )
            $this->addEditPreDisplay( $form, $object );

        $this->view->form   = $form->render( $this->view );
        $this->view->object = $object;

        if( $this->view->templateExists( $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'addEdit.tpl' ) )
            $this->view->display( $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'addEdit.tpl' );
        else
            $this->view->display( 'frontend' . DIRECTORY_SEPARATOR . 'addEdit.tpl' );
    }

    public function editAction()
    {
        $this->_forward( 'add' );
    }

    public function viewAction()
    {
        $this->view->perspective = $this->getRequest()->getParam( 'perspective' );

        if( $this->getRequest()->getParam( 'id' ) !== NULL && is_numeric( $this->getRequest()->getParam( 'id' ) ) )
        {
            // is the ID valid?
            if( !( $object = Doctrine::getTable( $this->frontend['model'] )->find( $this->getRequest()->getParam( 'id' ) ) ) )
            {
                $view = '<h1>Error</h1><p>No entry with ID: ' . $this->getRequest()->getParam( 'id' ) . ' exists.</p>';
            }
            else
            {
                $this->view->object = $object;
            }
        }

        $this->view->display( 'frontend' . DIRECTORY_SEPARATOR . 'view.tpl' );
    }



    /**
     * A generic action to delete an element of a database (as represented
     * by a Doctrine model) via Smarty templates.
     *
     * This method calls preDelete before the deletion.
     *
     * It then calls postDelete() after the deletion (assuming it succeeds).
     *
     * To capture errors, pre and postDelete() should throw an exception.
     *
     */
    public function deleteAction()
    {
        // is the ID valid?
        if( !( $object = Doctrine::getTable( $this->getModelName() )->find( $this->getRequest()->getParam( 'id' ) ) ) )
        {
            $this->view->message = new INEX_Message( 'No such object with ID: ' . $this->getRequest()->getParam( 'id' ), "error" );
            return( $this->_forward( 'list' ) );
        }

        $this->preDelete( $object );

        if( $object->delete() )
        {
            $this->logger->notice( 'Object with ID: ' . $this->getRequest()->getParam( 'id' ) . " deleted from {$this->frontend['model']}" );
            $this->view->message = new INEX_Message( "Object with ID " . $this->getRequest()->getParam( 'id' ) . " deleted", "success" );

            $this->postDelete();
        }
        else
        {
            $this->logger->error( "Object could not be deleted" );
            $this->view->message = new INEX_Message( "Error deleting object", "error" );
        }

        if( $this->getRequest()->getParam( 'return' ) !== null )
            $this->_redirect( 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . '://' . $_SERVER['SERVER_NAME'] . $this->getRequest()->getParam( 'return' ) );
        else
            return( $this->_forward( 'list' ) );
    }

    /**
     * A generic action to list the elements of a database (as represented
     * by a Doctrine model) via Smarty templates.
     */
    public function listAction()
    {
        if( !( $this->view->rows = $this->_customList() ) )
        {
            $dataQuery = Doctrine_Query::create()
                ->from( $this->frontend['model'] . ' x' );
    
            if( isset( $this->frontend['columns']['sortDefaults'] ) )
            {
                $order = '';
                if( isset( $this->frontend['columns']['sortDefaults']['order'] ) )
                    $order = strtoupper( $this->frontend['columns']['sortDefaults']['order'] );
    
                $dataQuery->orderBy( "{$this->frontend['columns']['sortDefaults']['column']} $order" );
            }
    
            $dataQuery = $this->_preList( $dataQuery );
    
            $this->view->rows = $dataQuery->execute();
        }        

        $this->view->feSession  = $this->feSession;

        $this->view->frontend = $this->frontend;


        if( $this->view->templateExists( $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'customContextMenu.js.tpl' ) )
            $this->view->hasCustomContextMenu = $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'customContextMenu';

        if( $this->view->templateExists( $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'postContent.tpl' ) )
            $this->view->hasPostContent = $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'postContent.tpl';

        if( $this->view->templateExists( $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'list.tpl' ) )
            $this->view->display( $this->getRequest()->getParam( 'controller' ) . DIRECTORY_SEPARATOR . 'list.tpl' );
        else
            $this->view->display( 'frontend' . DIRECTORY_SEPARATOR . 'list.tpl' );
    }


    protected function _customList()
    {
        return false;
    }
    
    /**
     * A function executed before the list action queries the database.
     *
     * You can add clauses to the query or perform other queries and assign data to the
     * view.
     *
     * @param Doctrine_Query $dataQuery The query being formed to which you can add clauses. Just be sure to return it irregardless!
     * @return You *must* return the $dataQuery object.
     */
    protected function _preList( $dataQuery )
    {
        return $dataQuery;
    }

    protected function getForm( $options = null, $isEdit = false )
    {
        $formName = "INEX_Form_{$this->frontend['name']}";

        if( $this->getRequest()->getParam( 'return' ) !== null )
            $cancelLocation = 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . '://' . $_SERVER['SERVER_NAME'] . $this->getRequest()->getParam( 'return' );
        else
            $cancelLocation = 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . '://' . $_SERVER['SERVER_NAME'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . $this->getRequest()->getParam( 'controller' ) . '/list';

        return new $formName( $options, $isEdit, $cancelLocation );
    }


    /**
     * Returns the model name.
     *
     * Each controller extending the FrontEnd class represents a Doctrine model. This returns the name of that model.
     *
     * @return string the model name
     */
    public function getModelName()
    {
        return $this->frontend['model'];
    }

    /**
     * Returns the controller name.
     *
     * Each controller extending the FrontEnd class represents a Doctrine model. This returns the name that we use for the frontend to describe the model.
     *
     * @return string the name
     */
    public function getName()
    {
        return $this->frontend['name'];
    }


    /**
     * Base method to allow for pre-deletion hooks.
     */
    protected function preDelete()
    {}


    /**
     * Base method to allow for post-deletion hooks.
     */
    protected function postDelete()
    {}



    /**
     * Base method to allow for pre-add/edit save hooks.
     *
     * @param Doctrine_Record $object The object being built for adding or edited
     * @param bool $isEdit True if this is an edit, false if it's an add
     * @param Zend_Form $form The submitted add / edit form
     */
    protected function addEditPreSave( $object, $isEdit, $form )
    {}

    
}

