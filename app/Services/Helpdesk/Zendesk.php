<?php namespace IXP\Services\Helpdesk;

/*
 * Copyright (C) 2009-2015 Internet Neutral Exchange Association Limited.
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

use IXP\Contracts\Helpdesk as HelpdeskContract;

use Zendesk\API\Client as ZendeskAPI;


/**
 * Helpdesk Backend -> Zendesk
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   Helpdesk
 * @package    IXP\Services
 * @copyright  Copyright (c) 2009 - 2015, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Zendesk implements HelpdeskContract {

    /**
     * The Zendesk Client
     * @var Zendesk\API\Client
     */
    private $client;


    public function __construct( $config ) {
        if( !isset( $config['subdomain'] ) || !isset( $config['token'] ) || !isset( $config['email'] ) )
            throw new ConfigurationException( "Zendesk requires that 'subdomain', 'token', 'email' be configured" );

        $this->client = new ZendeskAPI($config['subdomain'], $config['email']);
        $this->client->setAuth('token', $config['token']);
    }

    /**
     * Find all tickets on the helpdesk
     */
    public function ticketsFindAll() {
        try {
            return $this->client->tickets()->findAll();
        } catch( \Zendesk\API\ResponseException $re ) {
            var_dump( $this->client->getDebug() );
        }
    }

    /**
     * Convert a IXP Customer entitiy into an associated array as rerquired by Zendesk's API
     *
     * @param \Entity\Customer $cust     The IXP Manager customer entity
     * @param bool             $id       If updating, set to Zendesk organisation ID
     * @return array Data in associate array format as required by Zendesk PHP API
     */
    private function customerEntityToZendeskObject( $cust, $id = false )
    {
        $data = [];

        $data['external_id']  = $cust->getId();
        $data['name']         = $cust->getName();

        if( $id ) {
            // updating so set the Zendesk ID:
            $data['id'] = $id;
        } else {
            // creating - set the initial domains:
            if( preg_match( '/^(http[s]*\:\/\/)?www\.([a-zA-Z0-9\.\-]+).*$/', $cust->getCorpwww(), $matches ) )
                $data['domain_names'] = $matches[2];
        }

        $data['organization_fields'] = [
            'asn'           => $cust->getAutsys(),
            'as_set'        => $cust->getPeeringmacro(),
            'peering_email' => $cust->getPeeringemail(),
            'noc_email'     => $cust->getNocemail(),
            'shortname'     => $cust->getShortname(),
            'type'          => $cust->getTypeText(),
            'addresses'     => '',
            'status'        => $cust->getStatusText(),
            'has_left'      => $cust->hasLeft()
        ];

        foreach( $cust->getVirtualInterfaces() as $vi ) {
            foreach( $vi->getVlanInterfaces() as $vli ) {
                if( $vli->getIpv4enabled() && $vli->getIPv4Address() )
                    $data['organization_fields']['addresses'] .= $vli->getIPv4Address()->getAddress() . "\n";
                if( $vli->getIpv6enabled() && $vli->getIPv6Address() )
                    $data['organization_fields']['addresses'] .= $vli->getIPv6Address()->getAddress() . "\n";
            }
        }

        $data['organization_fields']['addresses'] = trim( $data['organization_fields']['addresses'] );

        return $data;
    }

    /**
     * Convert a Zendesk organisation object to a IXP Customer entity
     *
     * @param object $org The Zendesk organisation object
     * @return \Entity\Customer $cust  The IXP Manager customer entity
     */
    private function zendeskObjectToCustomerEntity( $org )
    {
        $cust = new \Entities\Customer;

        $cust->setName(         $org->name          );
        $cust->setAutsys(       isset( $org->organization_fields->asn           ) ? $org->organization_fields->asn           : null );
        $cust->setPeeringmacro( isset( $org->organization_fields->as_set        ) ? $org->organization_fields->as_set        : null );
        $cust->setPeeringemail( isset( $org->organization_fields->peering_email ) ? $org->organization_fields->peering_email : null );
        $cust->setNocemail(     isset( $org->organization_fields->noc_email     ) ? $org->organization_fields->noc_email     : null );
        $cust->setShortname(    isset( $org->organization_fields->shortname     ) ? $org->organization_fields->shortname     : null );

        // these throw an exception if we send an unknow value but the source of the data is external here so we ignore:
        try { $cust->setTypeText(   isset( $org->organization_fields->type   ) ? $org->organization_fields->type   : null ); } catch( \IXP\Exceptions\GeneralException $e ) {}
        try { $cust->setStatusText( isset( $org->organization_fields->status ) ? $org->organization_fields->status : null ); } catch( \IXP\Exceptions\GeneralException $e ) {}

        // fake has left:
        if( isset( $org->organization_fields->has_left ) && $org->organization_fields->has_left )
            $cust->setDateLeave( new \DateTime );

        // store Zendesk's own ID for this organisation
        $cust->helpdesk_id = $org->id;

        return $cust;
    }

    /**
     * Examine customer and Zendesk object and see if Zendesk needs to be updated
     *
     * @param \Entities\Customer $cdb The IXP customer entity as known here in the database
     * @param \Entities\Customer $chd The IXP customer entity as known in the helpdesk
     * @return bool True if these objects are not in sync
     */
    public function organisationNeedsUpdating( \Entities\Customer $cdb, \Entities\Customer $chd )
    {
        try {
            return $cdb->getName()         != $chd->getName()
                || $cdb->getAutsys()       != $chd->getAutsys()
                || $cdb->getPeeringmacro() != $chd->getPeeringmacro()
                || $cdb->getPeeringemail() != $chd->getPeeringemail()
                || $cdb->getNocemail()     != $chd->getNocemail()
                || $cdb->getShortname()    != $chd->getShortname()
                || $cdb->getTypeText()     != $chd->getTypeText()
                || $cdb->getStatusText()   != $chd->getStatusText()
                || $cdb->hasLeft()         != $chd->hasLeft();

                // FIXME IP addresses
        } catch( \IXP\Exceptions\GeneralException $e ) {
            // some issue with type / status - means the customer needs updating
            return true;
        }
    }

    /**
     * Create organisation(s)
     *
     * Create organisations on the helpdesk. Tickets are usually aligned to
     * users and they in turn to organisations.
     *
     * @param \IXP\Entities\Customer[] custs An array of IXP Manager customers to create as organisations
     * @return bool
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function organisationsCreate( array $custs )
    {
        $params = [];

        // Zendesk has a limit of 100 objects per request
        $count = 0;
        $total = count( $custs );

        foreach( $custs as $cust ) {
            $params[] = $this->customerEntityToZendeskObject( $cust );

            if( ++$count == $total || $count % 100 == 0 ) {
                try {
                    usleep( 60/200 );
                    $response = $this->client->organizations()->createMany( $params );
                    $params = [];
                } catch( \Zendesk\API\ResponseException $re ) {
                    var_dump( $this->client->getDebug() );
                    return false;
                }
            }
        }

        return true;
    }




    /**
     * Update an organisation **where the helpdesk ID is known!**
     *
     * Updates an organisation already found via `organisationFind()` as some implementations
     * (such as Zendesk's PHP client as of Apr 2015) require knowledge of the helpdesk's ID for
     * an organisatoin.
     *
     * @param int                $helpdeskId The ID of the helpdesk's organisation object
     * @param \Entities\Customer $cust       An IXP Manager customer as returned by `organisationFind()`
     * @return bool
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function organisationUpdate( $helpdeskId, \Entities\Customer $cust )
    {
        try {
            usleep( 60/200 );
            $this->client->organizations()->update( $this->customerEntityToZendeskObject( $cust, $helpdeskId ) );
            return true;
        } catch( \Zendesk\API\ResponseException $re ) {
            var_dump( $this->client->getDebug() );
        }
    }


    /**
     * Find an organisation by our own customer ID
     *
     * **NB:** the returned entity shouldn't have an ID parameter set - you should already know it!
     *
     * The reason for this is that the returned customer object is incomplete and is only intended
     * to be used to compare local with Zendesk and/or identify if a customer exists.
     *
     * The returned customer object MUST have a member `helpdesk_id` containing the helpdesk provider's
     * ID for this organisation.
     *
     * @param int $id Our own customer ID to find the organisation from
     * @return \IXP\Entities\Customer|bool A shallow disassociated customer object or false
     */
    public function organisationFind( $id )
    {
        try {
            usleep( 60/200 );
            $response = $this->client->organizations()->search( [ 'external_id' => $id ] );

            if( !isset( $response->organizations[0] ) )
                return false;

            return $this->zendeskObjectToCustomerEntity( $response->organizations[0] );

        } catch( \Zendesk\API\ResponseException $re ) {
            var_dump( $this->client->getDebug() );
        }
    }


}
