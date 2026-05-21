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

/**
 * A Validator performs a series of checks and reports the findings in a ValidationBackend
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
interface Validator
{
    /**
     * Human-readable name for the validator
     */
    public function getName(): string;

    /**
     * Priority of the validator - lower numbers are rendered earlier.
     */
    public function getPriority(): int;

    /**
     * Execute validator routines, reporting findings to ValidationBackend
     */
    public function run( ValidationBackend $backend): void;
}