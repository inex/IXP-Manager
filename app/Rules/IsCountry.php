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

namespace IXP\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use IXP\Utils\CountryUtils;
use \Webpatser\Countries\CountriesFacade;

readonly class IsCountry implements ValidationRule
{
    public function __construct(private string $key = "iso_3166_2")
    {
        if (!in_array($this->key, CountryUtils::UNIQUE_KEYS)) {
            throw new \InvalidArgumentException(  "Provided key is not a unique country identifier: {$this->key}" );
        }
    }

    #[\Override]
    public function validate( string $attribute, mixed $value, Closure $fail ): void
    {
        if( CountryUtils::isKnownBy($value, $this->key) ) {
            return;
        }

        $fail( "The selected :attribute is invalid" );
    }
}