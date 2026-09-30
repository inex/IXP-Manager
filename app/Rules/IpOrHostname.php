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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
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

/**
 * This validation checks if the provided input is an IP address (public or private, any protocol)
 * or a valid hostname.
 */
class IpOrHostname implements ValidationRule
{
    #[\Override]
    public function validate( string $attribute, mixed $value, Closure $fail ): void
    {
        if( filter_var( $value, FILTER_VALIDATE_IP , FILTER_FLAG_IPV4) === false &&
            filter_var( $value, FILTER_VALIDATE_IP , FILTER_FLAG_IPV6) === false &&
            filter_var( $value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME ) === false ) {
            $fail( 'The :attribute must be a valid IP address or hostname.' );
        }
    }
}