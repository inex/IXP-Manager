<?php

namespace IXP\Models;

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

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo
};

/**
 * IXP\Models\UserRememberToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $device
 * @property string $ip
 * @property string $expires
 * @property int $is_2fa_complete
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\User $user
 * @method static Builder|UserRememberToken newModelQuery()
 * @method static Builder|UserRememberToken newQuery()
 * @method static Builder|UserRememberToken query()
 * @method static Builder|UserRememberToken whereCreatedAt($value)
 * @method static Builder|UserRememberToken whereDevice($value)
 * @method static Builder|UserRememberToken whereExpires($value)
 * @method static Builder|UserRememberToken whereId($value)
 * @method static Builder|UserRememberToken whereIp($value)
 * @method static Builder|UserRememberToken whereIs2faComplete($value)
 * @method static Builder|UserRememberToken whereToken($value)
 * @method static Builder|UserRememberToken whereUpdatedAt($value)
 * @method static Builder|UserRememberToken whereUserId($value)
 * @mixin \Eloquent
 */
class UserRememberToken extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'device',
        'ip',
        'expires',
        'is_2fa_complete',
    ];

    /**
     * Get the user that own the remember token
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Is this token expired?
     *
     * @return bool
     */
    public function expired(): bool
    {
        return $this->expires < now();
    }
}