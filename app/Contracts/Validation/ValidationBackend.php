<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace IXP\Contracts\Validation;

use IXP\Utils\Validation\Result;

/**
 * ValidationBackend is the interface used by Validators to report results.
 *
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
interface ValidationBackend
{
    /**
     * Report a name/version of software encountered during validation
     */
    public function software(string $name, string $version): void;

    /**
     * Report debug level information
     */
    public function debug( string $message ): Result;

    /**
     * Report a positive validation outcome
     */
    public function info( string $message ): Result;

    /**
     * Report a suggestion to the user
     */
    public function suggestion( string $message ): Result;

    /**
     * Report an warning condition detected during validation
     */
    public function warning( string $message ): Result;

    /**
     * Report an erroneous condition detected during validation
     */
    public function error( string $message ): Result;
}