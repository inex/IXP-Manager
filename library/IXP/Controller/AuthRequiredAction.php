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
class IXP_Controller_AuthRequiredAction extends IXP_Controller_Action
{
    use OSS_Controller_Action_Trait_AuthRequired;
    
    
    /**
     * Load a customer from the database by shortname but redirect to `error/error` if no such customer.
     *
     * Will use 'shortname' parameter is no shortname provided
     *
     * @param string|bool $shortname The customer shortname to load (or, if false, look for `shortname` parameter)
     * @param string $redirect Alternative location to redirect to
     * @return \Entities\Customer The customer object
     */
    protected function loadCustomerByShortname( $shortname = false, $redirect = null )
    {
        if( $shortname === false )
            $shortname = $this->getParam( 'shortname', false );
    
        if( $shortname )
            $c = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->findOneBy( [ 'shortname' => $shortname ] );
    
        if( !$shortname || !$c )
        {
            $this->addMessage( 'Invalid customer', OSS_Message::ERROR );
            $this->redirect( $redirect === null ? 'error/error' : $redirect );
        }
    
        return $c;
    }
    
}

