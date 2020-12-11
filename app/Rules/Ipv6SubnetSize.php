<?php

namespace IXP\Rules;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * Ipv6SubnetSize
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   Rules
 * @package    IXP\Rules
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Ipv6SubnetSize implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes( $attribute, $value ): bool
    {
        $parts = explode( '/', $value );

        // mask:
        $mask = (int)$parts[1];
        if( $mask < config( "ixp.irrdb.min_v6_subnet_size" ) ) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Invalid subnet, must be minimum ' . config( "ixp.irrdb.min_v6_subnet_size" );
    }
}