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
 * Controller: API V1 Mailing List controller
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Apiv1_MailingListController extends IXP_Controller_API_V1Action
{
    use IXP_Controller_Trait_MailingList;
    
    public function preDispatch()
    {
        $this->assertUserPriv( \Entities\User::AUTH_SUPERUSER );
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
    }
    
    /**
     * Mailing list subscribed action - list all addresses subscribed to the given list
     */
    public function getSubscribedAction()
    {
        $list = $this->getMailingList();
        $this->getResponse()->setHeader( 'Content-Type', 'text/plain' );
        $this->getSubscribed( $list );
    }
    
    /**
     * Mailing list unsubscribed action - list all addresses not subscribed to the given list
     */
    public function getUnsubscribedAction()
    {
        $list = $this->getMailingList();
        $this->getResponse()->setHeader( 'Content-Type', 'text/plain' );
        $this->getUnsubscribed( $list );
    }
    

    /**
     * Mailing list initialisation script
     *
     * First sets a user preference for ALL users *WITHOUT* a mailing list sub for this list to unsub'd.
     *
     * Then takes a list of *existing* mailing list addresses from stdin and:
     *   - is a user does not exist with same email, skips
     *   - if a user does exist with same email, sets his mailing list preference
     *
     * NB: This function is NON-DESTRUCTIVE. It will *NOT* affect any users with *EXISTING* settings
     * but set those without a setting to on / off as appropriate.
     *
     * E.g.: for a given file test.txt with addresses from Mailman's list_members:
     *
     *     curl -f --data-urlencode addresses@test.txt  \
     *             "http://127.0.0.1/ixp/apiv1/mailing-list/init/key/xxx/list/members"
     *
     */
    public function initAction()
    {
        $list = $this->getMailingList();
        //$this->getResponse()->setHeader( 'Content-Type', 'text/plain' );
        
        $addresses = [];
        $validator = new Zend_Validate_EmailAddress();
        
        foreach( explode( "\n", $this->getParam( 'addresses' ) ) as $a )
        {
            $a = trim( $a );
            if( $validator->isValid( $a ) )
                $addresses[] = $a;
        }
        
        $this->initList( $list, $addresses );
    }
    
    /**
     * Mailing list password sync - create and execute commands to set mailing list p/w of subscribers
     */
    public function passwordSyncAction()
    {
        $list = $this->getMailingList();
    
        // we'll sync by default so only if we're told not to will the following be true:
        if( isset( $this->_options['mailinglists'][$list]['syncpws'] ) && !$this->_options['mailinglists'][$list]['syncpws'] )
        {
            throw new Zend_Controller_Action_Exception(
                "Password sync for the selected mailing list is disabled", 428
            );
        }
    
        $cmds = $this->getPasswordSyncCommands( $list );
        $this->getResponse()->setHeader( 'Content-Type', 'text/plain' );
        echo implode( "\n", $cmds ) . "\n";
    }
    
    
    
    private function getMailingList()
    {
        $list = $this->getMailingListGeneric(  $this->getParam( 'list', false ) );
        
        if( !is_array( $list ) )
            return $list;
        
        throw new Zend_Controller_Action_Exception( $list[0], $list[1] );
    }
    
}
