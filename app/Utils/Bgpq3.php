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
 * Interface for the BQPQ3 command line utility
 *
 * @see http://snar.spb.ru/prog/bgpq3/
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 */
class Bgpq3 extends BgpqBase
{
    protected string $utility = 'BGPQ3';

    /**
     * Constructor
     *
     * @param string $path The full executable path of the BGPQ3 utility
     * @param ?string $whois Whois server - defaults to BGPQ3's own default
     * @param ?string $sources Whois server sources - defaults to BGPQ3's own default
     * @throws ConfigurationException
     */
    public function __construct( #[Config('ixp.irrdb.bgpq3.path')] protected string $path, protected ?string $whois = null, protected ?string $sources = null )
    {
        $this->validatePath($path);
    }
}