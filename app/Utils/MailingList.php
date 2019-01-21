<?php

namespace IXP\Utils;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM;

use Ds\Set;

use Entities\{
    User as UserEntity
};

use IXP\Exceptions\MailingListException as Exception;

/**
 * Interface for mailing list management
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 */
class MailingList
{
    /**
     * @var array Mailing list config key (from config/mailinglist.php)
     */
    private $key = null;

    /**
     * @var array Mailing list config (from config/mailinglist.php)
     */
    private $ml = null;

    /**
     * Constructor
     *
     * @param string $listname The list name as defined as the array key in config/mailinglist.php
     * @throws Exception
     */
    public function __construct( string $listname ) {
        if( !config( 'mailinglists.lists.' . $listname, false ) ) {
            throw new Exception('Mailing list name not defined in config/mailinglist.php');
        }

        $this->key = $listname;
        $this->ml  = config( 'mailinglists.lists.' . $listname );
    }


    /**
     * Get all users who are (un)subscribed from the mailing list.
     *
     * All emails are validated, normalised to lowercase, duplicates removed and sorted alphabetically.
     *
     * @param bool $subscribed
     * @return array
     */
    public function getSubscriberEmails( bool $subscribed = true ): array {
        $filtered_users = new Set;

        foreach( D2EM::getRepository( UserEntity::class )->getMailingListSubscribers( $this->key, $subscribed, false ) as $u ) {
            $e = strtolower( $u['email'] );
            if( !$filtered_users->contains( $e ) && filter_var( $e, FILTER_VALIDATE_EMAIL ) !== false ) {
                $filtered_users->add( $e );
            }
        }

        $filtered_users->sort();
        return $filtered_users->toArray();
    }


    /**
     * Initialise a new mailing list.
     *
     * See documentation for full details: http://docs.ixpmanager.org/features/mailing-lists/
     *
     * Returns an array with three arrays keyed as:
     *
     * - skipped: users that already had a preference for this list
     * - subscribed: users with a preference added to indicate they are subscribed
     * - unsubscribed: users with a preference added to indicate they are unsubscribed
     * - unknown: no matching user
     *
     * @param Set $addresses Addresses to initialise IXP Manager user preferences with.
     *                       NB: ensure all addresses passed are normalised to lower case!
     * @return array
     */
    public function init( Set $addresses ) {

        // three types of results:
        $skipped      = [];
        $subscribed   = [];
        $unsubscribed = [];

        /** @var UserEntity[] $users */
        $users = D2EM::getRepository( UserEntity::class )->findAll();

        foreach( $users as $u ) {
            $e = strtolower( $u->getEmail() );

            if( $u->hasPreference( "mailinglist.{$this->key}.subscribed" ) ) {
                if( $addresses->contains( $e ) ) {
                    $skipped[] = $e;
                    $addresses->remove( $e );
                }
                continue;
            }

            if( $addresses->contains( $e ) ) {
                $u->setPreference( "mailinglist.{$this->key}.subscribed", 1 );
                $subscribed[] = $e;
                $addresses->remove( $e );
            } else if( filter_var( $e, FILTER_VALIDATE_EMAIL ) !== false ) {
                $u->setPreference( "mailinglist.{$this->key}.subscribed", 0 );
                $unsubscribed[] = $e;
            }
        }

        D2EM::flush();

        return [
            'skipped'      => $skipped,
            'subscribed'   => $subscribed,
            'unsubscribed' => $unsubscribed,
            'unknown'      => $addresses->toArray(),
        ];
    }
}
