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

declare(strict_types=1);

namespace IXP\Utils;

use Illuminate\Container\Attributes\Config;
use IXP\Exceptions\ConfigurationException;

/**
 * Interface for the BQPQ4 command line utility
 *
 * @see https://github.com/bgp/bgpq4
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @author Thomas Kerin    <thomas@islandbridgenetworks.ie>
 */
class Bgpq4 extends BgpqBase
{
    protected string $utility = 'BGPQ4';

    /**
     * Constructor
     *
     * @param string $path The full executable path of the BGPQ4 utility
     * @param ?string $whois Whois server - defaults to BGPQ4's own default
     * @param ?string $sources Whois server sources - defaults to BGPQ4's own default
     * @throws ConfigurationException
     */
    public function __construct( #[Config('ixp.irrdb.bgpq4.path')] protected string $path, protected ?string $whois = null, protected ?string $sources = null )
    {
        $this->validatePath($path);
    }
}