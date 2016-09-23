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
 * A trait of common mailing list functions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller_Trait
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
trait IXP_Controller_Trait_MailingList
{
    
    /**
     * Mailing list subscribed action - list all addresses subscribed to the given list
     */
    protected function getSubscribed( $list )
    {
        $users = $this->getD2EM()->getRepository( '\\Entities\\User' )->getMailingListSubscribers( $list, 1 );
        
        foreach( $users as $user )
            if( strlen( $user['email'] ) )
                echo "{$user['email']}\n";
    }
    
    
    /**
     * Mailing list unsubscribed action - list all addresses not subscribed to the given list
     */
    protected function getUnsubscribed( $list )
    {
        $users = $this->getD2EM()->getRepository( '\\Entities\\User' )->getMailingListSubscribers( $list, 0 );
        
        foreach( $users as $user )
            if( strlen( $user['email'] ) )
                echo "{$user['email']}\n";
    }
    
    protected function getPasswordSyncCommands( $list )
    {
        $users = $this->getD2EM()->getRepository( '\\Entities\\User' )->getMailingListSubscribers( $list, 1 );
        
        $cmds = [];
        foreach( $users as $user )
            $cmds[] = sprintf( "{$this->_options['mailinglist']['cmd']['changepw']} %s %s %s",
                escapeshellarg( $list ), escapeshellarg( $user['email'] ), escapeshellarg( $user['password'] )
            );
        
        return $cmds;
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
     */
    protected function initList( $list, $addresses )
    {
        $users = $this->getD2EM()->getRepository( '\\Entities\\User' )->findAll();
        
        foreach( $users as $u )
        {
            if( $u->hasPreference( "mailinglist.{$list}.subscribed" ) )
                continue;
        
            if( in_array( $u->getEmail(), $addresses ) )
                $u->setPreference( "mailinglist.{$list}.subscribed", 1 );
            else
                $u->setPreference( "mailinglist.{$list}.subscribed", 0 );
        }
        
        $this->getD2EM()->flush();
    }
    
    protected function getMailingListGeneric( $list )
    {
        // do we have mailing lists defined?
        if( !isset( $this->_options['mailinglist']['enabled'] ) || !$this->_options['mailinglist']['enabled'] )
            return [ "Mailing lists disabled in configuration (use: mailinglist.enabled = 1 to enabled)", 428 ];
    
        if( !$list )
            return [ "You must specify a list name (e.g. /list/name or --p1 name)", 404 ];
    
        // do we have mailing lists defined?
        if( !isset( $this->_options['mailinglists'] ) || !count( $this->_options['mailinglists'] ) )
            return [ "No mailing lists defined in application.ini", 404 ];
    
        // is it a valid list?
        if( !isset( $this->_options['mailinglists'][$list] ) )
            return [ "Mailing list not found", 404 ];
    
        return $list;
    }
    
}

