<?php
/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace IXP\Models;

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo
};

/**
 * IXP\Models\UserLoginHistory
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $ip
 * @property \Illuminate\Support\Carbon $at
 * @property int|null $customer_to_user_id
 * @property string|null $via
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\CustomerToUser|null $customerToUser
 * @method static Builder<static>|UserLoginHistory newModelQuery()
 * @method static Builder<static>|UserLoginHistory newQuery()
 * @method static Builder<static>|UserLoginHistory query()
 * @method static Builder<static>|UserLoginHistory whereAt($value)
 * @method static Builder<static>|UserLoginHistory whereCreatedAt($value)
 * @method static Builder<static>|UserLoginHistory whereCustomerToUserId($value)
 * @method static Builder<static>|UserLoginHistory whereId($value)
 * @method static Builder<static>|UserLoginHistory whereIp($value)
 * @method static Builder<static>|UserLoginHistory whereUpdatedAt($value)
 * @method static Builder<static>|UserLoginHistory whereUserId($value)
 * @method static Builder<static>|UserLoginHistory whereVia($value)
 * @mixin \Eloquent
 */
class UserLoginHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_logins';

    protected $casts = [
        'at' => 'datetime',
    ];

    /**
     * Get the customer to user
     *
     * @return BelongsTo<CustomerToUser, UserLoginHistory>
     */
    public function customerToUser(): BelongsTo
    {
        return $this->belongsTo(CustomerToUser::class, 'customer_to_user_id');
    }

    public static function recordLogin( CustomerToUser $c2u, string $ip, string $via ): self
    {
        $loginHistory = new self();
        $loginHistory->customer_to_user_id = $c2u->id;
        $loginHistory->ip = $ip;
        $loginHistory->via = $via;
        $loginHistory->at = now();
        $loginHistory->save();
        return $loginHistory;
    }
}