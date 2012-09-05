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
 * @package INEX_Controller
 *
 */
class INEX_Controller_Action extends OSS_Controller_Action
{

    // traits we want to use
    use OSS_Controller_Action_Trait_Namespace;
    use OSS_Controller_Action_Trait_Auth;
    // use OSS_Controller_Action_Trait_AuthRequired;
    use OSS_Controller_Action_Trait_Doctrine2Cache;
    use OSS_Controller_Action_Trait_Doctrine2;
    use OSS_Controller_Action_Trait_Doctrine2User;
    use OSS_Controller_Action_Trait_Mailer;
    // use OSS_Controller_Action_Trait_License;
    use OSS_Controller_Action_Trait_Logger;
    use OSS_Controller_Action_Trait_Smarty;
    // use OSS_Controller_Action_Trait_StatsD;
    // use OSS_Controller_Action_Trait_Freshbooks;
    use OSS_Controller_Action_Trait_Messages;
    // use OSS_Controller_Action_Trait_News;
    
    
    
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

        
            
        if( in_array( 'OSS_Controller_Action_Trait_Auth', get_declared_traits() ) )
        {
            if( $this->getAuth()->hasIdentity() )
                $this->_customer = Doctrine::getTable( 'Cust' )->find( $this->identity[ 'user' ][ 'custid' ] );
            
            $this->view->customer = $this->_customer;
        }
        
        if( $this->getAuth()->hasIdentity() && $this->getUser()['privs'] == 3 )
            $this->superuserSetup();
    }

    /**
     * Get the customer object
     *
     * @return Cust The cust object
     */
    protected function getCustomer()
    {
        return $this->_customer;
    }
    

    
    
    /**
     * Set an array of customer id and names
     *
     * FIXME Move to central cache rather than per-user
     */
    private function superuserSetup()
    {
        // get an array of customer id => names
        if( !isset( $this->session->ahome_customers ) )
            $this->session->ahome_customers = CustTable::getAllNames();
        
        $this->view->customers = $this->_customers = $this->session->ahome_customers;
    }
    
    
    /**
     * Store a variable to the APC cache and ignore if APC is not available
     *
     * Mirrors the scalar version of apc_store
     *
     */
    public function apcStore( $key, $var, $ttl = 0 )
    {
        if( !ini_get( 'apc.enabled' ) )
            return false;
        
        return apc_store( $key, $var, $ttl );
    }
    
    /**
     * Store a variable to the APC cache and ignore if APC is not available
     *
     * Mirrors the scalar version of apc_store
     *
     */
    public function apcFetch( $key )
    {
        if( !ini_get( 'apc.enabled' ) )
            return false;
    
        return apc_fetch( $key );
    }
    
    
}

