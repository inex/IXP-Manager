<?php

namespace IXP\Contracts\Grapher;

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

use IXP\Services\Grapher\Graph;

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
interface Backend {

    /**
     * The name of the backend (as would be entered in the config file for example)
     *
     * @return string
     */
    public function name(): string;

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
     * @param Entities\IXP $ixp The IXP to generate the config for (multi-IXP mode)
     * @param int $config_type The type of configuration to generate
     * @return array
     */
    public function generateConfiguration( IXP $ixp, int $type = self::GENERATED_CONFIG_TYPE_MONOLITHIC ): array;

    /**
     * Examines the provided graph object and determines if this backend is able to
     * process the request or not.
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return bool
     */
    public function canProcess( Graph $graph ): bool;

    /**
     * Get the data points for a given graph
     *
     * It **MUST** be returned as an indexed array of arrays where the five elements
     * of these arrays are:
     *
     *     [
     *       [
     *         0 =>  unixtime stamp
     *         1 =>  average incoming rate
     *         2 =>  average outgoing rate
     *         3 =>  maximum incoming rate
     *         4 =>  maximum outgoing rate
     *       ],
     *       ....
     *     ]
     *
     * NB: For errors, discards and packets, the rate is packets per second. For bits, it's bits per second.
     *
     * NB: The above **MUST** be ordered with the oldest first.
     *
     * @param IXP\Services\Grapher\Graph $graph
     * @return array
     */
    public function data( Graph $graph ): array;

}
