<?php namespace IXP\Contracts;

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

 /**
  * Helpdesk Contract - any concrete implementation of a Helpdesk provider must
  * implement this interface
  *
  * @see        http://laravel.com/docs/5.0/contracts
  * @author     Barry O'Donovan <barry@opensolutions.ie>
  * @category   Helpdesk
  * @package    IXP\Contracts
  * @copyright  Copyright (c) 2009 - 2015, Internet Neutral Exchange Association Ltd
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
interface Helpdesk {

    /**
     * Find all tickets on the helpdesk
     */
    public function ticketsFindAll();




    /**
     * Create organisation(s)
     *
     * Create organisations on the helpdesk. Tickets are usually aligned to
     * users and they in turn to organisations.
     *
     * @param \IXP\Entities\Customer[] custs An array of IXP Manager customers to create as organisations
     * @return int The number of organisations successfully created
     * @throws \IXP\Services\Helpdesk\ApiException
     */
    public function organisationsCreate( array $custs );

    /**
     * Find an organisation by our own customer ID
     *
     * @param int $id Our own customer ID to find the organisation from
     * @return \IXP\Entities\Customer|bool A shallow disassociated customer object or false
     */
    public function organisationFind( $id );



}
