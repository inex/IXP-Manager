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


    /**
     * Not all graphing backends will require a configuration. This function indicates whether the
     * backend being implemented requires a configuration or not.
     *
     * Used, for example, by the Artisan grapher:generate-configuration console command.
     * @return bool
     */
    public function isConfigurationRequired(): bool;

    /**
     * Not all graphing backends are created equal and some will support / require different output formats.
     *
     * The types we are looking at are:
     *
     * * single monolithic text - standard output or to a specified file
     * * multiple files (and optionally directories) to a specified directory
     * * gzip'd bundle of one or more files
     *
     * This function indicates whether this graphing engine supports single monolithic text
     *
     * @return bool
     */
    public function isMonolithicConfigurationSupported(): bool;

    /**
     * @see IXP\Contracts\Grapher::isMonolithicConfigurationSupported() for an explanation
     *
     * This function indicates whether this graphing engine supports multiple files to a directory
     *
     * @return bool
     */
    public function isMultiFileConfigurationSupported(): bool;


    /**
     * Constant for configuration type to generate: one big file
     * @var int
     */
    const GENERATED_CONFIG_TYPE_MONOLITHIC = 1;

    /**
     * Constant for configuration type to generate: one big file
     * @var int
     */
    const GENERATED_CONFIG_TYPE_MULTIFILE = 2;


    /**
     * Generate the configuration file(s) for this graphing backend
     *
     * For monolithic files, returns a single element array. Otherwise
     * an array keyed by the filename (with optional local directory path).
     *
     * @param int $config_type The type of configuration to generate
     * @return array
     */
    public function generateConfiguration( int $type = self::GENERATED_CONFIG_TYPE_MONOLITHIC ): array;


}
