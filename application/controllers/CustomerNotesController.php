<?php

use Entities\CustomerNotes;
/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Controller: Customer Notes
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerNotesController extends IXP_Controller_AuthRequiredAction
{

    public function ajaxAddAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER, true );
        
        $f = new IXP_Form_Customer_Notes();
        $r = [ 'error' => true ];
        
        if( $f->isValid( $_POST ) )
        {
            // locate the customer
            $cust = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $f->getValue( 'custid' ) );
            
            if( $cust )
            {
                $n = new \Entities\CustomerNote();
                $n->setTitle( $f->getValue( 'title' ) );
                $n->setNote( $f->getValue( 'note' ) );
                $n->setPrivate( $f->getValue( 'public' ) == 'makePublic' ? false : true );
                $n->setCreated( new DateTime() );
                $n->setCustomer( $cust );
                $this->getD2EM()->persist( $n );
                $this->getD2EM()->flush();
                
                $r = [ 'error' => false ];
            }
            else
            {
                $r['error'] = "Invalid customer specified.";
                $this->getLogger()->alert( "[ID: {$this->getUser()->getId()}] AJAX Customer Note addition - invalid customer specified" );
            }
        }
        
        $this->_helper->json( $r );
    }
}

