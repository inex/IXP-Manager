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
 * INEX's version of Zend's Zend_Controller_Action implemented custom
 * functionality.
 *
 * All application controlers subclass this rather than Zend's version directly.
 *
 * @package IXP_Controller
 *
 */
class IXP_Controller_Action extends OSS_Controller_Action
{

    // traits we want to use
    use OSS_Controller_Action_Trait_Namespace;
    use OSS_Controller_Action_Trait_Doctrine2User;
    use OSS_Controller_Action_Trait_Auth;
    // use OSS_Controller_Action_Trait_AuthRequired;
    use OSS_Controller_Action_Trait_Doctrine2Cache;
    use OSS_Controller_Action_Trait_Doctrine2;
    use OSS_Controller_Action_Trait_Mailer;
    // use OSS_Controller_Action_Trait_License;
    use OSS_Controller_Action_Trait_Logger;
    use OSS_Controller_Action_Trait_Smarty;
    // use OSS_Controller_Action_Trait_StatsD;
    // use OSS_Controller_Action_Trait_Freshbooks;
    use OSS_Controller_Action_Trait_Messages;
    // use OSS_Controller_Action_Trait_News;
    
    use IXP_Controller_Trait_Common;
    
    
    /**
     * A variable to hold the customer record
     *
     * @var object An instance of the customer record
     */
    protected $_customer = false;

    /**
     * An array of id => cust.name for super users
     * @var array
     */
    protected $_customers = null;
    
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
        $this->view->registerClass( 'SWITCHPORT', '\\Entities\\SwitchPort' );
        $this->view->registerClass( 'VLAN',       '\\Entities\\Vlan' );
        
        $this->view->resellerMode  = $this->resellerMode();
        $this->view->multiIXP      = $this->multiIXP();
        $this->view->as112UiActive = $this->as112UiActive();
        
        if( $this->getAuth()->hasIdentity() && $this->getUser()->getPrivs() == Entities\User::AUTH_SUPERUSER )
            $this->superUserSetup();

        
    }

    /**
     * Get the customer object
     *
     * @return Entities\Customer The customer object for the current user
     */
    protected function getCustomer()
    {
        return $this->getUser()->getCustomer();
    }
    

    /**
     * Assertion function for ensuring user permissions.
     *
     * This redirects to an insufficient perms page and issues a log if the
     * assertion fails.
     *
     * @param $priv int The \Entities\User::AUTH_XXX permission to ensure the user has
     * @param $exact bool If true, match the permission exactly rather than 'at least'
     * @return bool True if okay, redirects on insufficient permissions
     */
    protected function assertPrivilege( $priv, $exact = true )
    {
        if( !$this->getAuth()->hasIdentity() )
            $this->redirectAndEnsureDie( 'auth/login' );
        
        if( ( $exact && $this->getUser()->getPrivs() != $priv ) || ( !$exact && $this->getUser()->getPrivs() < $priv ) )
        {
            $this->getLogger()->notice( "{$this->getUser()->getUsername()} illegally tried to access {$this->getRequest()->getRequestUri()}" );
            $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }
        
        return true;
    }
    
    /**
     * Perform some setup functions for super users
     *
     */
    private function superUserSetup()
    {
        // get an array of customer id => names
        if( !( $this->_customers = $this->getD2Cache()->fetch( 'admin_home_customers' ) ) )
        {
            $this->_customers = $this->getD2EM()->getRepository( 'Entities\\Customer' )->getNames( true );
            $this->getD2Cache()->save( 'admin_home_customers', $this->_customers, 3600 );
        }
        
        $this->view->customers = $this->_customers;
    }

}

