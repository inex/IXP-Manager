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
     * Create a single organisation
     *
     * @param \IXP\Entities\Customer[] custs
     */
    public function organisationsCreate( array $custs );



}
