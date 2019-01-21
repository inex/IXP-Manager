<?php namespace IXP\Contracts;

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

 /**
  * Helpdesk Contract - any concrete implementation of a Helpdesk provider must
  * implement this interface
  *
  * @see        http://laravel.com/docs/5.0/contracts
  * @author     Barry O'Donovan <barry@opensolutions.ie>
  * @category   Helpdesk
  * @package    IXP\Contracts
  * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
interface Helpdesk {


    /**
     * Return the helpdesk debug information
     *
     * Your implentation should catch API errors, set the $debug member with additional details and throw an ApiException
     */
    public function getDebug();


    /**
     * Find all tickets on the helpdesk
     */
    public function ticketsFindAll();


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
     * Examine customer and Zendesk object and see if Zendesk needs to be updated
     *
     * This belongs here as the different parameters of a customer that one helpdesk
     * may support will vary from another.
     *
     * @param \Entities\Customer $cdb The IXP customer entity as known here in the database
     * @param \Entities\Customer $chd The IXP customer entity as known in the helpdesk
     * @return bool True if these objects are not in sync
     */
    public function organisationNeedsUpdating( \Entities\Customer $cdb, \Entities\Customer $chd );


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
    public function organisationCreate( $cust );

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
    public function organisationUpdate( $helpdeskId, \Entities\Customer $cust );


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
    public function organisationFind( $id );


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
     * Examine contact and Zendesk object and see if Zendesk needs to be updated
     *
     * @param \Entities\Contact $cdb The IXP contact entity as known here in the database
     * @param \Entities\Comtact $chd The IXP contact entity as known in the helpdesk
     * @return bool True if these objects are not in sync
     */
    public function contactNeedsUpdating( \Entities\Contact $cdb, \Entities\Contact $chd );

    /**
     * Create user
     *
     * Create user on the helpdesk.
     *
     * @param \IXP\Entities\Contact contact An IXP Manager contact to create
     * @return \Entities\Contact|bool Decoupled contact object with `helpdesk_id`
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function userCreate( $contact, $org_id );


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
    public function userUpdate( $helpdeskId, \Entities\Contact $contact );

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
    public function userFind( $id );


}
