<?php

namespace IXP\Services;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use GuzzleHttp\{
    Client as GuzzleHttp,
    Exception\RequestException
};


/**
 * PeeringDb
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   LookingGlass
 * @package    IXP\Services\PeeringDb
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringDb {

    /**
     * Get network by ASN
     *
     * @param int $asn
     *
     * @return array
     */
    public function getNetworkByAsn( $asn = null){
        $autsys = trim( $asn );
        $result[ 'error' ] = false;

        try {
            // doing request to get the cookie

            // NOT SURE THAT IS NECESSARY
            $client = new GuzzleHttp( ['cookies' => true] );
            $AsnContent = $client->request( 'GET', "https://www.peeringdb.com/api/net?asn=" . $autsys );

            // check if HTTP request status is 200
            if( $AsnContent->getStatusCode() == '200' ) {
                $id = json_decode( $AsnContent->getBody()->getContents() )->data[ 0 ]->id;

                $infoContent = $client->request( 'GET', "https://" . config( "ixp_api.peeringDB.username" ) . ":" . config( "ixp_api.peeringDB.password" ) ."@peeringdb.com/api/net/" . $id );
                $info = json_decode( $infoContent->getBody()->getContents() );

                $result[ 'result' ] = $info->data[ 0 ];
            }

        } catch (RequestException $e) {
            $result[ 'error' ] = true;
            // If there are network errors, we need to ensure the application doesn't crash.
            // if $e->hasResponse is not null we can attempt to get the message
            // Otherwise, we'll just pass a network unavailable message.
            if( $e->hasResponse() ) {
                $exception = (string)$e->getResponse()->getBody();
                $result[ 'result' ] = json_decode( $exception );
            } else {
                $result[ 'result' ] = $e->getMessage();
            }
        }
        return $result;
    }



}