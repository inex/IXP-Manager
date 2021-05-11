<?php

namespace IXP\Http\Controllers\Api\V4;

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
use IXP\Exceptions\MailingListException;

use IXP\Utils\MailingList as ML;

use Illuminate\Http\{
    JsonResponse, Request, Response
};

/**
 * MailingListController API Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MailingListController extends Controller
{
    /**
     * @var ML
     */
    private $ml = null;

    /**
     * @var string
     */
    private $mlkey = null;

    public function __construct()
    {
        if( PHP_SAPI !== 'cli' && !config( 'mailinglists.enabled' ) ) {
            abort( 503, "Mailing list functionality is disabled. See: http://docs.ixpmanager.org/features/mailing-lists/" );
        }
    }

    /**
     * For the given listname, return the appropriate mailing list object (or throw a 404 if not found)
     *
     * @param string $listname Name of the mailing list (array index from config/mailinglist.php)
     *
     * @return ML
     */
    private function getMailingList( string $listname ): ML
    {
        if( $this->ml === null || $this->mlkey !== $listname ) {
            try {
                $this->ml    = new ML( $listname );
                $this->mlkey = $listname;
            } catch( MailingListException $e ) {
                abort( 404, 'Mailing list not defined in config/mailinglists.php' );
            }
        }

        return $this->ml;
    }

    /**
     * Mailing list subscribers action - list all addresses subscribed to the given list
     *
     * All emails are validated, normalised to lowercase, duplicates removed and sorted alphabetically.
     *
     * @param string @listname Name of the mailing list (array index from config/mailinglist.php)
     *
     * @return JsonResponse|Response
     */
    public function subscribers( string $listname ): JsonResponse|Response
    {
        if( request()->is('api/v4/mailing-list/subscribers/json/*' ) ) {
            return response()->json( $this->getMailingList( $listname )->getSubscriberEmails() );
        }

        return response( implode( "\n", $this->getMailingList( $listname )->getSubscriberEmails() ) . "\n",
            200, [ 'Content-Type' => 'text/plain; charset=utf-8' ] );
    }

    /**
     * Mailing list unsubscribed action - list all addresses not subscribed to the given list
     *
     * All emails are validated, normalised to lowercase, duplicates removed and sorted alphabetically.
     *
     * @param string @listname Name of the mailing list (array index from config/mailinglist.php)
     *
     * @return JsonResponse|Response
     */
    public function unsubscribed( string $listname ): JsonResponse|Response
    {
        if( request()->is('api/v4/mailing-list/unsubscribed/json/*' ) ) {
            return response()->json( $this->getMailingList( $listname )->getSubscriberEmails(false ) );
        }

        return response( implode( "\n", $this->getMailingList( $listname )->getSubscriberEmails(false ) ) . "\n",
            200, [ 'Content-Type' => 'text/plain; charset=utf-8' ] );
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
     * curl --data "addresses=noc@blacknight.ie\nbarryo@inex.ie\nbarry@opensolutions.ie\nbarry@example.com\n" -X POST -H "X-IXP-Manager-API-Key: NIJm5aYpwrl1MQzgtUWXOx8i7DlVqinOfwfDbhorPRbmztH7" http://ixp-ibn.dev/api/v4/mailing-list/init/members
     *
     * @param Request $request
     * @param string $listname  Name of the mailing list (array index from config/mailinglist.php)
     *
     * @return JsonResponse|Response
     */
    public function init( Request $request, string $listname ): JsonResponse|Response
    {
        $addresses = collect();

        foreach( explode( "\n", $request->addresses ) as $a ) {
            $addresses->add( strtolower( trim( $a ) ) );
        }

        $result = $this->getMailingList( $listname )->init( $addresses );

        if( request()->is('api/v4/mailing-list/init/json/*' ) ) {
            return response()->json( $result );
        }

        $output = "";
        foreach( $result as $k => $v ) {
            foreach( $v as $e ) {
                $output .= "{$k}: {$e}\n";
            }
        }

        return response( $output, 200, [ 'Content-Type' => 'text/plain; charset=utf-8' ] );
    }
}