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

namespace IXP\Models;

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Prunable
};

use IXP\Services\Auth\PasswordLockoutPolicy;

/**
 * IXP\Models\UnknownUserLoginAttempt
 *
 * Failed login state for usernames that do not exist. The columns mirror the lockout columns on the user
 * table so that the login form can throttle unknown usernames exactly as it throttles real ones. If it did
 * not, the difference would reveal which usernames exist.
 *
 * The username column has the same collation as user.username, so MySQL decides which spellings are the
 * same name in the same way for both tables.
 *
 * @property int $id
 * @property string $username
 * @property int $password_fail_count
 * @property \Illuminate\Support\Carbon|null $no_login_until
 * @property \Illuminate\Support\Carbon|null $password_last_fail_at
 * @method static Builder<static>|UnknownUserLoginAttempt newModelQuery()
 * @method static Builder<static>|UnknownUserLoginAttempt newQuery()
 * @method static Builder<static>|UnknownUserLoginAttempt query()
 * @method static Builder<static>|UnknownUserLoginAttempt whereId($value)
 * @method static Builder<static>|UnknownUserLoginAttempt whereNoLoginUntil($value)
 * @method static Builder<static>|UnknownUserLoginAttempt wherePasswordFailCount($value)
 * @method static Builder<static>|UnknownUserLoginAttempt wherePasswordLastFailAt($value)
 * @method static Builder<static>|UnknownUserLoginAttempt whereUsername($value)
 * @mixin \Eloquent
 */
class UnknownUserLoginAttempt extends Model
{
    use Prunable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'unknown_user_login_attempts';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
    ];

    /**
     * Defaults, so a newly created row has the same shape as one read back from the database.
     *
     * @var array
     */
    protected $attributes = [
        'password_fail_count' => 0,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'password_fail_count'   => 'integer',
        'no_login_until'        => 'datetime',
        'password_last_fail_at' => 'datetime',
    ];

    /**
     * Rows whose fail count has expired carry no state and can be deleted (artisan model:prune).
     *
     * @return Builder<static>
     */
    public function prunable(): Builder
    {
        return static::where( function( Builder $q ) {
            $q->whereNull( 'password_last_fail_at' )
                ->orWhere( 'password_last_fail_at', '<', PasswordLockoutPolicy::expiryCutoff() );
        } );
    }
}
