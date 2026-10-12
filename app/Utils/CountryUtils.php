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

use Webpatser\Countries\CountriesFacade;

class CountryUtils
{
    /**
     * A list of keys that uniquely identify a country and are present on each country.
     */
    public const UNIQUE_KEYS = [
        'country-code',
        'iso_3166_2',
        'iso_3166_3',
        'name',
    ];

    public static function isKnownBy( ?string $identifier, string $key = "iso_3166_2"): bool
    {
        if (!in_array($key, self::UNIQUE_KEYS)) {
            throw new \InvalidArgumentException(  "Provided key is not a unique country identifier: {$key}" );
        }

        if (null === $identifier || '' === $identifier) {
            return false;
        }

        return array_any( CountriesFacade::getList(), fn( $country ) => $country[ $key ] === $identifier );
    }
}