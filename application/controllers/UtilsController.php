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
 * Controller: Misc utils
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UtilsController extends INEX_Controller_Action
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

            $this->getLogger()->alert( $this->user->username . " tried to access utils without sufficient permissions" );

            Zend_Session::destroy( true, true );

            $this->_forward( 'login', 'auth' );
            return false;
        }

    }


    /**
     * Display apcinfo()
     */
    public function apcinfoAction()
    {
        $BU = Zend_Controller_Front::getInstance()->getBaseUrl() . '/utils/apcinfo';

        require( APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'apcinfo.php' );
    }

    /**
     * Display phpinfo()
     */
    public function phpinfoAction()
    {
        phpinfo();
    }
}

