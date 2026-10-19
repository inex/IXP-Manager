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

namespace IXP\Rules\Trait;

trait ParsesCidr
{
    /**
     * Extracts IP and mask from a CIDR string, or returns null if invalid
     *
     * @psalm-return array{0:string, 1:int}|null
     */
    protected function parseCidr( mixed $value ): ?array
    {
        if ( !is_string( $value ) ) {
            return null;
        }

        if( !strpos( $value, '/' ) ) {
            return null;
        }

        $parts = explode( '/', $value );

        if( sizeof( $parts ) !== 2 ) {
            return null;
        }

        if ( false === filter_var( $parts[1], FILTER_VALIDATE_INT ) ) {
            return null;
        }

        return [ $parts[0], (int) $parts[1] ];
    }
}