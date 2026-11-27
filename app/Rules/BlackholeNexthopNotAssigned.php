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
use IXP\Models\{
    IPv4Address,
    IPv6Address,
    VlanInterface
};

readonly class BlackholeNexthopNotAssigned implements ValidationRule
{
    /**
     * @param class-string<IPv4Address|IPv6Address> $model
     */
    public function __construct( private string $model, private string $column, private ?int $vlanid ) {}

    #[\Override]
    public function validate( string $attribute, mixed $value, Closure $fail ): void
    {
        $inUse = $this->model::where( 'address', $value )
            ->where( 'vlanid', $this->vlanid )
            ->whereIn( 'id', VlanInterface::select( $this->column )->whereNotNull( $this->column ) )
            ->exists();

        if( $inUse ) {
            $fail( 'This address is assigned to a VLAN interface and cannot be used as the blackhole next-hop.' );
        }
    }
}
