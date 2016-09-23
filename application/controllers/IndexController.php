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
 * Controller: Index controller
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IndexController extends IXP_Controller_Action
{

    public function indexAction()
    {
        if( !$this->getAuth()->hasIdentity() )
            $this->redirectAndEnsureDie( 'auth/login' );
        
        if( $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER )
            $this->_redirect( 'admin/index' );
        else if( $this->getUser()->getPrivs() == \Entities\User::AUTH_CUSTADMIN )
            $this->_redirect( 'contact/list' );
        else
            $this->forward( 'index', 'dashboard' );
    }

    public function controllerDisabledAction()
    {
        $this->view->display( 'index/controller-disabled.tpl' );
    }

    public function aboutAction()
    {
        $this->view->apachever = str_replace("Apache/", "", $_SERVER['SERVER_SOFTWARE']);
        $this->view->phpver    = phpversion();
        $this->view->dbver     = $this->getD2EM()->getConnection()->query( "SELECT VERSION() AS V" )->fetch()['V'];
    }

}
