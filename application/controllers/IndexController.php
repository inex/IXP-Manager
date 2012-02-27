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
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class IndexController extends INEX_Controller_Action
{

    public function indexAction()
    {
        // We lose the message when redirected back here and then onwards from here. Fix:
        if( ( $m = $this->view->message ) !== null )
            $this->session->message = $m;

        $auth = Zend_Auth::getInstance();
        if( !$auth->hasIdentity() )
            $this->_forward( 'login', 'auth' );
        else
        {
            $identity = $auth->getIdentity();

            if( $identity['user']['privs'] == User::AUTH_SUPERUSER )
            {
                $this->_forward( 'index', 'admin' );
            }
            else if( $identity['user']['privs'] == User::AUTH_CUSTADMIN )
            {
                $this->_redirect( 'cust-admin/users' );
            }
            else
            {
                $this->_redirect( 'dashboard' );
            }
        }
    }

    public function controllerDisabledAction()
    {
        $this->view->display( 'index/controller-disabled.tpl' );
    }

    public function aboutAction()
    {
        $this->view->display( 'index/about.tpl' );
    }

    public function helpAction()
    {
        $this->view->display( 'index/help.tpl' );
    }
    
}
