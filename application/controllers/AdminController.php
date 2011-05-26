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
 * CustAdminController
 *
 * @author
 * @version
 */

class AdminController extends INEX_Controller_Action
{


    public function preDispatch()
    {
        // let's get the user's details sorted before everything else
        if( !$this->identity )
            $this->_redirect( 'auth/login' );
        else if( $this->user->privs != User::AUTH_SUPERUSER )
	    {
	        $this->view->message = new INEX_Message(
	            "You must be an administrator to access this page. This attempt to access private and "
	            . "secure sections of the site has been recorded and our administrators alerted.",
	            INEX_Message::MESSAGE_TYPE_ERROR
	        );

	        $this->logger->alert( $this->user->username . " tried to access the admin controller without sufficient permissions" );

            Zend_Session::destroy( true, true );

	        $this->_forward( 'login', 'auth' );
	        return false;
	    }

    }


    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        $this->_forward( 'index' );
    }


    public function staticAction()
    {
        $page = $this->_request->getParam( 'page', null );

        if( $page == null )
            return( $this->_redirect( 'index' ) );

        // does the requested static page exist? And if so, display it
        if( preg_match( '/^[a-zA-Z0-9\-]+$/', $page ) > 0
                && file_exists( APPLICATION_PATH . "/views/admin/static/{$page}.tpl" ) )
        {
            $this->view->display( "admin/static/{$page}.tpl" );
        }
        else
        {
            $this->session->message = new INEX_Message(
                "The requested page was not found.",
                INEX_Message::MESSAGE_TYPE_ERROR
            );
            $this->_redirect( 'index' );
        }
    }
}

