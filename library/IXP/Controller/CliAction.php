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
 * INEX's version of Zend's Zend_Controller_Action implemented custom
 * functionality.
 *
 * All application controlers subclass this rather than Zend's version directly.
 *
 * @package IXP_Controller
 *
 */
class IXP_Controller_CliAction extends OSS_Controller_CliAction
{
    
    use IXP_Controller_Trait_Common;
    
    /**
     * Override the Zend_Controller_Action's constructor (which is called
     * at the very beginning of this function anyway).
     *
     * @param object $request See Parent class constructer
     * @param object $response See Parent class constructer
     * @param object $invokeArgs See Parent class constructer
     */
    public function __construct(
                Zend_Controller_Request_Abstract $request,
                Zend_Controller_Response_Abstract $response,
                array $invokeArgs = null )
    {
        // call the parent's version where all the Zend magic happens
        parent::__construct( $request, $response, $invokeArgs );
        
        // we need this for access to class constants in the template
        $this->view->registerClass( 'USER',       '\\Entities\\User' );
        $this->view->registerClass( 'CUSTOMER',   '\\Entities\\Customer' );
        $this->view->registerClass( 'SWITCHER',   '\\Entities\\Switcher' );
        $this->view->registerClass( 'SWITCHPORT', '\\Entities\\SwitchPort' );
         
        $this->view->resellerMode = $this->resellerMode();
        $this->view->multiIXP     = $this->multiIXP();
    }

}

