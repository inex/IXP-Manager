<?php

namespace IXP\Models;

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

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo,
    Relations\HasMany
};

use IXP\Traits\Observable;

/**
 * IXP\Models\CustomerToUser
 *
 * @property int $id
 * @property int $customer_id
 * @property int $user_id
 * @property int $privs
 * @property array|null $extra_attributes
 * @property string|null $last_login_date
 * @property string|null $last_login_from
 * @property string|null $last_login_via
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer $customer
 * @property-read \IXP\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\UserLoginHistory[] $userLoginHistories
 * @property-read int|null $user_login_histories_count
 * @method static Builder|CustomerToUser custAdmin()
 * @method static Builder|CustomerToUser newModelQuery()
 * @method static Builder|CustomerToUser newQuery()
 * @method static Builder|CustomerToUser query()
 * @method static Builder|CustomerToUser whereCreatedAt($value)
 * @method static Builder|CustomerToUser whereCustomerId($value)
 * @method static Builder|CustomerToUser whereExtraAttributes($value)
 * @method static Builder|CustomerToUser whereId($value)
 * @method static Builder|CustomerToUser whereLastLoginDate($value)
 * @method static Builder|CustomerToUser whereLastLoginFrom($value)
 * @method static Builder|CustomerToUser whereLastLoginVia($value)
 * @method static Builder|CustomerToUser wherePrivs($value)
 * @method static Builder|CustomerToUser whereUpdatedAt($value)
 * @method static Builder|CustomerToUser whereUserId($value)
 * @mixin \Eloquent
 */
class CustomerToUser extends Model
{
    use Observable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'user_id',
        'privs',
        'extra_attributes',
        'last_login_date',
        'last_login_from',
        'last_login_via',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'extra_attributes' => 'json',
    ];

    /**
     * The attributes that should not be logged
     *
     * @var array
     */
    public $field_log_exception = [
        'last_login_date',
        'updated_at',
    ];

    /**
     * Get the customer for the customertouser
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id' );
    }

    /**
     * Get the user for the customertouser
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id' );
    }

    /**
     * Get the user Login Histories for the customertouser
     */
    public function userLoginHistories(): HasMany
    {
        return $this->hasMany(UserLoginHistory::class, 'customer_to_user_id');
    }

    /**
     * Scope a query to only include cust admins.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeCustAdmin( Builder $query ): Builder
    {
        return $query->where('privs', User::AUTH_CUSTADMIN );
    }

    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Model $model
     *
     * @return string
     */
    public static function logSubject( Model $model ): string
    {
        return sprintf(
            "%s To User [id:%d] belonging to %s [id:%d] '%s' and User [id:%d] '%s'",
            ucfirst( config( 'ixp_fe.lang.customer.one' ) ),
            $model->id,
            ucfirst( config( 'ixp_fe.lang.customer.one' ) ),
            $model->customer_id,
            $model->customer->name,
            $model->user_id,
            $model->user->username,
        );
    }
}