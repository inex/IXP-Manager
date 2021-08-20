<?php

namespace IXP\Utils;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Illuminate\Support\Collection;

use IXP\Models\User;

use IXP\Exceptions\MailingListException as Exception;

/**
 * Interface for mailing list management
 *
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Utils
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
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
     * @param string    $listname The list name as defined as the array key in config/mailinglist.php
     *
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
     *
     * @return array
     */
    public function getSubscriberEmails( bool $subscribed = true ): array
    {
        $filtered_users = collect();

        $users = User::when( $subscribed, function ( $query ) {
            return $query->whereJsonContains( 'prefs->mailinglist', [ $this->key => "1" ] );
        }, function ($query) {
            return $query->whereJsonContains( 'prefs->mailinglist', [ $this->key => "0" ] );
        })->orderBy( 'email' )->get();

        foreach( $users as $u ) {
            $e = strtolower( $u->email );
            if( !$filtered_users->contains( $e ) && filter_var( $e, FILTER_VALIDATE_EMAIL ) !== false ) {
                $filtered_users->add( $e );
            }
        }

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
     * @param Collection $addresses Addresses to initialise IXP Manager user preferences with.
     *                       NB: ensure all addresses passed are normalised to lower case!
     * @return array
     */
    public function init( Collection $addresses ): array
    {
        // three types of results:
        $skipped      = [];
        $subscribed   = [];
        $unsubscribed = [];

        foreach( User::all() as $u ) {
            $e = strtolower( $u->email );
            $prefs = $u->prefs;

            if( isset( $prefs[ 'mailinglist' ][ $this->key ] ) && (int)$prefs[ 'mailinglist' ][ $this->key ] === 1 ) {
                if( $addresses->contains( $e ) ) {
                    $skipped[] = $e;
                    $addresses->forget( $addresses->search( $e ) );
                }
                continue;
            }

            if( $addresses->contains( $e ) ) {
                $value = 1;
                $subscribed[] = $e;
                $addresses->forget( $addresses->search( $e ) );
            } else if( filter_var( $e, FILTER_VALIDATE_EMAIL ) !== false ) {
                $value = 0;
                $unsubscribed[] = $e;
            }

            $prefs[ 'mailinglist' ][ $this->key ] = $value;
            $u->prefs = $prefs;
            $u->save();
        }

        return [
            'skipped'      => $skipped,
            'subscribed'   => $subscribed,
            'unsubscribed' => $unsubscribed,
            'unknown'      => $addresses->toArray(),
        ];
    }
}