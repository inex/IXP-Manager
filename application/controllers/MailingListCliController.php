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
 * Controller: Mailing List CLI actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MailingListCliController extends IXP_Controller_CliAction
{
    use IXP_Controller_Trait_MailingList;
    
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
     */
    public function listInitAction()
    {
        $list = $this->getMailingList();

        $stdin = fopen( "php://stdin","r" );
        $addresses = array();
        
        while( $address = strtolower( trim( fgets( $stdin ) ) ) )
            $addresses[] = $address;

        fclose( $stdin );
        
        $this->verbose( "Setting mailing list subscription for all users without a subscription setting...\n" );
        
        $this->initList( $list, $addresses );
    }

    /**
     * Mailing list subscribed action - list all addresses subscribed to the given list
     */
    public function getSubscribedAction()
    {
        $list = $this->getMailingList();
        $this->getSubscribed( $list );
    }
    
    /**
     * Mailing list unsubscribed action - list all addresses not subscribed to the given list
     */
    public function getUnsubscribedAction()
    {
        $list = $this->getMailingList();
        $this->getUnsubscribed( $list );
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
            $this->verbose( "{$list}: Password sync for the given mailing list is disabled" );
            die();
        }

        foreach( $this->getPasswordSyncCommands( $list ) as $cmd )
        {
            $this->verbose( "$cmd" );
            if( !$this->getParam( 'noexec', false ) )
                exec( $cmd );
        }
    }
    
    /**
     * Mailing list syncronisation - generates a shell script for all mailing lists
     */
    public function syncScriptAction()
    {
        // do we have mailing lists defined?
        if( !isset( $this->_options['mailinglists'] ) || !count( $this->_options['mailinglists'] ) )
            die( "ERR: No valid mailing lists defined in your application.ini\n" );
        
        $this->view->apppath = APPLICATION_PATH;
        $this->view->date = date( 'Y-m-d H:i:s' );

        if( $this->getFrontController()->getParam( 'param1', false ) == 'apiv1' )
            echo $this->view->render( 'mailing-list-cli/mailing-list-sync-script-apiv1.sh' );
        else
            echo $this->view->render( 'mailing-list-cli/mailing-list-sync-script.sh' );
    }
    
    private function getMailingList()
    {
        $list = $this->getMailingListGeneric( $this->getFrontController()->getParam( 'param1', false ) );
    
        if( !is_array( $list ) )
            return $list;
    
        die( "ERR: {$list[0]}\n" );
    }
}


