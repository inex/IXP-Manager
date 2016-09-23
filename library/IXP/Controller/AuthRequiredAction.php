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
class IXP_Controller_AuthRequiredAction extends IXP_Controller_Action
{
    use OSS_Controller_Action_Trait_AuthRequired;
    use IXP_Controller_Trait_Common;

    /**
     * Utility function to load a customer's notes and calculate the amount of unread / updated notes
     * for the logged in user and the given customer
     *
     * Used by:
     * @see CustomerController
     * @see DashboardController
     *
     * @param \Entities\Customer $cust
     */
    protected function _fetchCustomerNotes( $custid, $publicOnly = false )
    {
        $this->view->custNotes = $custNotes = $this->getD2EM()->getRepository( '\\Entities\\CustomerNote' )->ordered( $custid, $publicOnly );
        $unreadNotes = 0;
         
        $rut = $this->getUser()->getPreference( "customer-notes.read_upto" );
        $lr  = $this->getUser()->getPreference( "customer-notes.{$custid}.last_read" );
        
        if( $lr || $rut )
        {
            foreach( $custNotes as $cn )
            {
                $time = $cn->getUpdated()->format( "U" );
                if( ( !$rut || $rut < $time ) && ( !$lr || $lr < $time ) )
                    $unreadNotes++;
            }
        }
        else
            $unreadNotes = count( $custNotes );
    
        $this->view->notesReadUpto = $rut;
        $this->view->notesLastRead = $lr;
        $this->view->unreadNotes   = $unreadNotes;
    }
    
}

