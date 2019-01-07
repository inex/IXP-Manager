<?php namespace IXP\Services\Helpdesk;

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

use IXP\Contracts\Helpdesk as HelpdeskContract;

use Zendesk\API\HttpClient as ZendeskAPI;


/**
 * Helpdesk Backend -> Zendesk
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   Helpdesk
 * @package    IXP\Services
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Zendesk implements HelpdeskContract {

    /**
     * The Zendesk Client
     * @var Zendesk\API\Client
     */
    private $client;

    /**
     * Debug object
     *
     * No specific rules around this yet. Usually means something bad happened...
     */
    private $debug;

    public function __construct( $config ) {
        if( !isset( $config['subdomain'] ) || !isset( $config['token'] ) || !isset( $config['email'] ) )
            throw new ConfigurationException( "Zendesk requires that 'subdomain', 'token', 'email' be configured" );

        $this->client = new ZendeskAPI($config['subdomain'], $config['email']);
        $this->client->setAuth('basic', [ 'username' => $config['email'], 'token' => $config['token'] ] );
    }

    /**
     * Centralised function to perform the Zendesk API call, throw exceptions, etc
     *
     * @param function $fn Anonymous function containing API call
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    protected function callApi( $fn )
    {
        try {
            usleep( 60/200 );
            return call_user_func( $fn );
        } catch( \Exception $e ) {
            $this->debug = $this->client->getDebug();

            $apie = new ApiException( "Zendesk API error - further details available from \$helpdeskInstance->getDebug() / \$this->getErrorDetails()" );

            if( $e instanceof \Zendesk\API\Exceptions\ApiResponseException )
                $apie->setErrorDetails( json_decode( $e->getErrorDetails() ) );

            throw $apie;
        }
    }

    /**
     * Return the Zendesk debug information
     */
    public function getDebug() {
        return $this->debug;
    }


    /**
     * Find all tickets on the helpdesk
     *
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function ticketsFindAll() {
        $this->callApi( function() { $this->client->tickets()->findAll(); } );
    }



    // ********************************************************************************************
    // ********************************************************************************************
    // ********************************************************************************************
    //
    // COMPANIES / ORGANISATIONS
    //
    // ********************************************************************************************
    // ********************************************************************************************
    // ********************************************************************************************


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
     * Create organisation
     *
     * Create an organisation on the helpdesk. Tickets are usually aligned to
     * users and they in turn to organisations.
     *
     * @param \IXP\Entities\Customer cust An IXP Manager customer to create as organisation
     * @return \Entities\Customer|false A decoupled customer entity (including `helpdesk_id`)
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function organisationCreate( $cust )
    {
        $response = $this->callApi( function() use ( $cust ) {
            return $this->client->organizations()->create( $this->customerEntityToZendeskObject( $cust ) );
        });

        if( isset( $response->organization ) )
            return $this->zendeskObjectToCustomerEntity( $response->organization );

        return false;
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
     * @return \Entities\Customer|bool A decoupled customer entity (including `helpdesk_id`)
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function organisationUpdate( $helpdeskId, \Entities\Customer $cust )
    {
            $response = $this->callApi( function() use ( $cust, $helpdeskId ) {
                return $this->client->organizations()->update( $this->customerEntityToZendeskObject( $cust, $helpdeskId ) );
            });

            if( isset( $response->organization ) )
                return $this->zendeskObjectToCustomerEntity( $response->organization );

            return false;
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
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function organisationFind( $id )
    {
            $response = $this->callApi( function() use ( $id ) {
                return $this->client->organizations()->search( $id );
            });

            if( !isset( $response->organizations[0] ) )
                return false;

            return $this->zendeskObjectToCustomerEntity( $response->organizations[0] );
    }



    // ********************************************************************************************
    // ********************************************************************************************
    // ********************************************************************************************
    //
    // CONTACTS / USERS
    //
    // ********************************************************************************************
    // ********************************************************************************************
    // ********************************************************************************************

    /**
     * Convert a IXP Contact entitiy into an associated array as rerquired by Zendesk's API
     *
     * @param \Entity\Contact  $contact  The IXP Manager customer entity
     * @param int              $org_id   The Zendesk ID of the organisation
     * @param int              $id       If updating, set to Zendesk contact ID
     * @return array Data in associate array format as required by Zendesk PHP API
     */
    private function contactEntityToZendeskObject( $contact, $org_id = null, $id = false )
    {
        $data = [];

        if( $contact->getId() )
            $data['external_id']     = $contact->getId();
        $data['name']            = $contact->getName();

        $data['email']           = $contact->getEmail();
        $data['phone']           = $contact->getMobile();
        $data['organization_id'] = $org_id;

        if( $contact->getUser() && $contact->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER )
            $data['role'] = 'admin';
        else {
            $data['role'] = 'end-user';

            if( $org_id )
                $data['ticket_restriction'] = 'organization';
            else
                $data['ticket_restriction'] = 'requested';
        }


        if( $id ) {
            // updating so set the Zendesk ID:
            $data['id'] = $id;

        } else {
            $data['verified']  = true;
            $data['locale_id'] = 1176;              // British English
            $data['time_zone'] = 'Europe/Dublin';
        }

        return $data;
    }

    /**
     * Convert a Zendesk user object to a IXP contact entity
     *
     * @param object $user The Zendesk user object
     * @return \Entity\Contact $contact  The IXP Manager contact entity
     */
    private function zendeskObjectToContactEntity( $user )
    {
        $contact = new \Entities\Contact;

        $contact->setName(   $user->name   );
        $contact->setEmail(  $user->email  );
        $contact->setMobile( $user->phone  );

        // store Zendesk's own ID for this organisation
        $contact->helpdesk_id = $user->id;

        return $contact;
    }

    /**
     * Examine contact and Zendesk object and see if Zendesk needs to be updated
     *
     * @param \Entities\Contact $cdb The IXP contact entity as known here in the database
     * @param \Entities\Comtact $chd The IXP contact entity as known in the helpdesk
     * @return bool True if these objects are not in sync
     */
    public function contactNeedsUpdating( \Entities\Contact $cdb, \Entities\Contact $chd )
    {
        try {
            return $cdb->getName()         != $chd->getName()
                || $cdb->getMobile()       != $chd->getMobile()
                || $cdb->getEmail()        != $chd->getEmail();
        } catch( \IXP\Exceptions\GeneralException $e ) {
            // some issue with type / status - means the customer needs updating
            return true;
        }
    }

    /**
     * Create user
     *
     * Create user on the helpdesk.
     *
     * @param \IXP\Entities\Contact contact An IXP Manager contact to create
     * @return \Entities\Contact|bool Decoupled contact object with `helpdesk_id`
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function userCreate( $contact, $org_id )
    {
        $response = $this->callApi( function() use ( $contact, $org_id ) {
            return $this->client->users()->create( $this->contactEntityToZendeskObject( $contact, $org_id ) );
        });

        if( isset( $response->user ) )
            return $this->zendeskObjectToContactEntity( $response->user );

        return false;
    }




    /**
     * Update an user **where the helpdesk ID is known!**
     *
     * Updates an user already found via `userFind()` as some implementations
     * (such as Zendesk's PHP client as of Apr 2015) require knowledge of the helpdesk's ID for
     * an user.
     *
     * @param int                $helpdeskId The ID of the helpdesk's user object
     * @param \Entities\Contact  $contact    An IXP Manager contact as returned by `userFind()`
     * @return \Entities\Contact Decoupled contact object with `helpdesk_id`
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function userUpdate( $helpdeskId, \Entities\Contact $contact )
    {
        $response = $this->callApi( function() use ( $contact, $helpdeskId ) {
            return $this->client->users()->update( $helpdeskId, $this->contactEntityToZendeskObject( $contact, null, $helpdeskId ) );
        });

        if( isset( $response->user ) )
            return $this->zendeskObjectToContactEntity( $response->user );

        return false;
    }


    /**
     * Find an user by our own contact ID
     *
     * **NB:** the returned entity shouldn't have an ID parameter set - you should already know it!
     *
     * The reason for this is that the returned contact object is incomplete and is only intended
     * to be used to compare local with Zendesk and/or identify if a contact exists.
     *
     * The returned contact object MUST have a member `helpdesk_id` containing the helpdesk provider's
     * ID for this organisation.
     *
     * @param int $id Our own contact ID to find the contact from
     * @return \IXP\Entities\Contact|bool A shallow disassociated contact object or false
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function userFind( $id )
    {
        $response = $this->callApi( function() use ( $id ) {
            return $this->client->users()->search( [ 'external_id' => $id ] );
        });

        if( !isset( $response->users[0] ) )
            return false;

        return $this->zendeskObjectToContactEntity( $response->users[0] );
    }


}
