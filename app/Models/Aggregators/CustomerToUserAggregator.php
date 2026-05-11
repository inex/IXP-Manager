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

namespace IXP\Models\Aggregators;



use IXP\Models\CustomerToUser;

/**
 * CustomerToUserAggregator
 *
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 * @category IXP
 * @copyright Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 * @property-read \IXP\Models\Customer|null $customer
 * @property-read \IXP\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \IXP\Models\UserLoginHistory> $userLoginHistories
 * @property-read int|null $user_login_histories_count
 * @method static Builder<static>|CustomerToUserAggregator custAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerToUserAggregator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerToUserAggregator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerToUserAggregator query()
 * @mixin \Eloquent
 */
class CustomerToUserAggregator extends CustomerToUser
{
    /**
     * Count the number of *active* users assigned to $customerId, which have the provided $privilege.
     */
    public static function countActiveUsersWithPrivilege( int $customerId, int $privilege ): int
    {
        return CustomerToUser::join( 'user', 'user.id', '=', 'customer_to_users.user_id' )
            ->where('customer_to_users.customer_id', '=', $customerId)
            ->where('customer_to_users.privs', '=', $privilege)
            ->where('user.disabled', '=', false)
            ->count();
    }
}