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

namespace IXP\Utils\Whois;

use IXP\Exceptions\Utils\Whois\WhoisException;

/**
 * Resolver class for whois servers.
 * Allows loading different WHOIS hosts from configuration files.
 *
 * @package IXP\Services\Whois
 */
class WhoisResolver
{
    /**
     * Given a provided server, this function loads the host/port from configuration and returns a Whois instance.
     *
     * @param  string $server
     * @return Whois
     * @throws WhoisException
     */
    public function get( string $server ): Whois
    {
        if ( !( config()->has("ixp_api.whois.{$server}.host") && config()->has("ixp_api.whois.{$server}.port") ) ) {
            throw new WhoisException( "Configuration not found for whois server '$server'" );
        }

        return new Whois( config( "ixp_api.whois.{$server}.host" ), (int) config( "ixp_api.whois.{$server}.port" ) );
    }
}