<?php

namespace IXP\Rules;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Contracts\Validation\Rule;

/**
 * IPv4Cidr
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   Rules
 * @package    IXP\Rules
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IPv4Cidr implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if( !strpos( $value, '/' ) ) {
            return false;
        }

        $parts = explode( '/', $value );

        if( sizeof( $parts ) !== 2 ) {
            return false;
        }

        // mask:
        $mask = (int)$parts[1];
        if( $mask < 0 || $mask > 32 ) {
            return false;
        }

        if( filter_var( $parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) === false ) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid IPv4 address in CIDR format (e.g. 192.0.2.0/24).';
    }
}
