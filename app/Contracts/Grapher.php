<?php namespace IXP\Contracts;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Limited.
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

use Entities\IXP;

 /**
  * Helpdesk Contract - any concrete implementation of a Helpdesk provider must
  * implement this interface
  *
  * @see        http://laravel.com/docs/5.0/contracts
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   Graphing
  * @package    IXP\Contracts
  * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
interface Grapher {

    /**
     * Period of one day for graphs
     */
    const PERIOD_DAY   = 'day';

    /**
     * Period of one week for graphs
     */
    const PERIOD_WEEK  = 'week';

    /**
     * Period of one month for graphs
     */
    const PERIOD_MONTH = 'month';

    /**
     * Period of one year for graphs
     */
    const PERIOD_YEAR  = 'year';

    /**
     * Array of valid periods for drill down graphs
     */
    const PERIOD_DESCS = [
        self::PERIOD_DAY   => 'Day',
        self::PERIOD_WEEK  => 'Week',
        self::PERIOD_MONTH => 'Month',
        self::PERIOD_YEAR  => 'Year'
    ];

    /**
     * 'Bits' category for graphs
     */
    const CATEGORY_BITS     = 'bits';

    /**
     * 'Packets' category for graphs
     */
    const CATEGORY_PACKETS  = 'pkts';

    /**
     * 'Errors' category for graphs
     */
    const CATEGORY_ERRORS   = 'errs';

    /**
     * 'Discards' category for graphs
     */
    const CATEGORY_DISCARDS = 'discs';

    /**
     * Array of valid categories for graphs
     */
    const CATEGORY_DESC = [
        self::CATEGORY_BITS     => 'Bits',
        self::CATEGORY_PACKETS  => 'Packets',
        self::CATEGORY_ERRORS   => 'Errors',
        self::CATEGORY_DISCARDS => 'Discards',
    ];

    /**
     * Protocols for graphs
     */
    const PROTOCOL_IPV4 = 4;

    /**
     * Protocols for graphs
     */
    const PROTOCOL_IPV6 = 6;

    /**
     * Array of valid protocols
     */
    const PROTOCOLS = array(
        self::PROTOCOL_IPV4 => 'IPv4',
        self::PROTOCOL_IPV6 => 'IPv6'
    );




}
