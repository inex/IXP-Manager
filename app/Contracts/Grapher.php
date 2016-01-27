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
     * Return the grapher debug information
     *
     * Your implentation should catch API errors, set the $debug member with additional details and throw an ApiException
     */
    // public function getDebug();


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
    

}
