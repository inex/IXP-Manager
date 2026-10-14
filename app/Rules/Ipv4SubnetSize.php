<?php

declare(strict_types=1);

namespace IXP\Rules;

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

use Illuminate\Contracts\Validation\Rule;
use IXP\Rules\Trait\ParsesCidr;

/**
 * Ipv4SubnetSize - NOTE! This rule is currently only used in conjunction with the IPv6Cidr rule
 * which implements stricter checks on the input. This class should only be used when these
 * checks have taken place.
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   Rules
 * @package    IXP\Rules
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Ipv4SubnetSize implements Rule
{
    use ParsesCidr;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    #[\Override]
    public function passes( $attribute, $value ): bool
    {
        $parsed = $this->parseCidr( $value );
        if ($parsed === null) {
            return false;
        }

        [, $mask] = $parsed;
        if( $mask > config( "ixp.irrdb.min_v4_subnet_size" ) ) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    #[\Override]
    public function message(): string
    {
        return 'Invalid subnet, must be minimum ' . config( "ixp.irrdb.min_v4_subnet_size" );
    }
}