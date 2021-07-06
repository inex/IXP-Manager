<?php

namespace IXP\Contracts;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\{
    Contact,
    Customer
};

use IXP\Services\Helpdesk\ApiException;

/**
  * Helpdesk Contract - any concrete implementation of a Helpdesk provider must
  * implement this interface
  *
  * @see        http://laravel.com/docs/5.0/contracts
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin <yann@islandbridgenetworks.ie>
  * @category   Helpdesk
  * @package    IXP\Contracts
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
interface Helpdesk
{
    /**
     * Return the helpdesk debug information
     *
     * Your implementation should catch API errors, set the $debug member with additional details and throw an ApiException
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
     * @param Customer $cdb The IXP customer entity as known here in the database
     * @param Customer $chd The IXP customer entity as known in the helpdesk
     *
     * @return bool True if these objects are not in sync
     */
    public function organisationNeedsUpdating( Customer $cdb, Customer $chd ):bool;

    /**
     * Create organisation
     *
     * Create an organisation on the helpdesk. Tickets are usually aligned to
     * users and they in turn to organisations.
     *
     * @param Customer $cust An IXP Manager customer to create as organisation
     *
     * @return Customer|bool A decoupled customer entity (including `helpdesk_id`)
     *
     * @throws ApiException
     */
    public function organisationCreate( Customer $cust );

    /**
     * Update an organisation **where the helpdesk ID is known!**
     *
     * Updates an organisation already found via `organisationFind()` as some implementations
     * (such as Zendesk's PHP client as of Apr 2015) require knowledge of the helpdesk's ID for
     * an organisatoin.
     *
     * @param int               $helpdeskId The ID of the helpdesk's organisation object
     * @param Customer          $cust       An IXP Manager customer as returned by `organisationFind()`
     *
     * @return Customer|bool A decoupled customer entity (including `helpdesk_id`)
     * @throws ApiException
     */
    public function organisationUpdate( int $helpdeskId, Customer $cust );

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
     *
     * @return Customer|bool A shallow disassociated customer object or false
     *
     * @throws ApiException
     */
    public function organisationFind( int $id );

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
     * @param Contact $cdb The IXP contact entity as known here in the database
     * @param Contact $chd The IXP contact entity as known in the helpdesk
     *
     * @return bool True if these objects are not in sync
     */
    public function contactNeedsUpdating( Contact $cdb, Contact $chd ):bool;

    /**
     * Create user
     *
     * Create user on the helpdesk.
     *
     * @param Contact   $contact An IXP Manager contact to create
     * @param int       $org_id
     *
     * @return Contact|bool Decoupled contact object with `helpdesk_id`
     *
     * @throws ApiException
     */
    public function userCreate( Contact $contact, int $org_id );

    /**
     * Update an user **where the helpdesk ID is known!**
     *
     * Updates an user already found via `userFind()` as some implementations
     * (such as Zendesk's PHP client as of Apr 2015) require knowledge of the helpdesk's ID for
     * an user.
     *
     * @param int       $helpdeskId The ID of the helpdesk's user object
     * @param Contact   $contact    An IXP Manager contact as returned by `userFind()`
     *
     * @return Contact Decoupled contact object with `helpdesk_id`
     *
     * @throws ApiException
     */
    public function userUpdate( int $helpdeskId, Contact $contact ): Contact;

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
     *
     * @return Contact|bool A shallow disassociated contact object or false
     *
     * @throws ApiException
     */
    public function userFind( int $id );
}