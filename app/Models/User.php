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

use Eloquent;

use Illuminate\Database\Eloquent\{Builder,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany,
    Relations\HasOne};

use Illuminate\Notifications\Notifiable;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;

use Illuminate\Contracts\Auth\{
    CanResetPassword as CanResetPasswordContract,
    Authenticatable as AuthenticatableContract
};

use Illuminate\Support\Str;

use IXP\Events\Auth\ForgotPassword as ForgotPasswordEvent;

use PragmaRX\Google2FALaravel\Support\Authenticator as GoogleAuthenticator;

use IXP\Traits\Observable;

/**
 * IXP\Models\User
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $username
 * @property string|null $password
 * @property string|null $email
 * @property string|null $authorisedMobile
 * @property int|null $uid
 * @property int|null $privs
 * @property int|null $disabled
 * @property int|null $lastupdatedby
 * @property string|null $creator
 * @property string|null $name
 * @property int|null $peeringdb_id
 * @property array|null $extra_attributes
 * @property array|null $prefs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ApiKey[] $apiKeys
 * @property-read int|null $api_keys_count
 * @property-read \IXP\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\CustomerToUser[] $customerToUser
 * @property-read int|null $customer_to_user_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Customer[] $customers
 * @property-read int|null $customers_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \IXP\Models\User2FA|null $user2FA
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\UserRememberToken[] $userRememberTokens
 * @property-read int|null $user_remember_tokens_count
 * @method static Builder|User activeOnly()
 * @method static Builder|User byPrivs(?int $priv = null)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAuthorisedMobile($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereCreator($value)
 * @method static Builder|User whereCustid($value)
 * @method static Builder|User whereDisabled($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereExtraAttributes($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastupdatedby($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePeeringdbId($value)
 * @method static Builder|User wherePrefs($value)
 * @method static Builder|User wherePrivs($value)
 * @method static Builder|User whereUid($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @mixin Eloquent
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword, Notifiable, Observable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'extra_attributes'  => 'json',
        'prefs'             => 'json',
    ];

    protected $attributes = [
        'extra_attributes'  => '[]',
        'prefs'             => '{}',
    ];

    public const AUTH_PUBLIC    = 0;
    public const AUTH_CUSTUSER  = 1;
    public const AUTH_CUSTADMIN = 2;
    public const AUTH_SUPERUSER = 3;

    public static $PRIVILEGES = [
        self::AUTH_CUSTUSER  => 'CUSTUSER',
        self::AUTH_CUSTADMIN => 'CUSTADMIN',
        self::AUTH_SUPERUSER => 'SUPERUSER',
    ];

    public static $PRIVILEGES_ALL = [
        self::AUTH_PUBLIC    => 'PUBLIC',
        self::AUTH_CUSTUSER  => 'CUSTUSER',
        self::AUTH_CUSTADMIN => 'CUSTADMIN',
        self::AUTH_SUPERUSER => 'SUPERUSER',
    ];

    public static $PRIVILEGES_TEXT = [
        self::AUTH_CUSTUSER  => 'Customer User',
        self::AUTH_CUSTADMIN => 'Customer Administrator',
        self::AUTH_SUPERUSER => 'Superuser',
    ];

    public static $PRIVILEGES_TEXT_ALL = [
        self::AUTH_PUBLIC    => 'Public / Non-User',
        self::AUTH_CUSTUSER  => 'Customer User',
        self::AUTH_CUSTADMIN => 'Customer Administrator',
        self::AUTH_SUPERUSER => 'Superuser',
    ];

    public static $PRIVILEGES_TEXT_NONSUPERUSER = [
        self::AUTH_CUSTUSER  => 'Customer User',
        self::AUTH_CUSTADMIN => 'Customer Administrator',
    ];

    public static $PRIVILEGES_TEXT_VSHORT = [
        self::AUTH_CUSTUSER  => 'CU',
        self::AUTH_CUSTADMIN => 'CA',
        self::AUTH_SUPERUSER => 'SU',
    ];

    /**
     * Get the remember tokens for the user
     */
    public function userRememberTokens(): HasMany
    {
        return $this->hasMany(UserRememberToken::class, 'user_id' );
    }

    /**
     * Get the remember tokens for the user
     */
    public function user2FA(): HasOne
    {
        return $this->hasOne(User2FA::class, 'user_id' );
    }

    /**
     * Get the customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'custid');
    }

    /**
     * Get the api keys for the user
     */
    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class, 'user_id' );
    }

    /**
     * Get all the customers for the user
     */
    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_to_users', 'user_id' )
            ->orderBy( 'id', 'asc' );
    }

    /**
     * Get all the customers for the user
     */
    public function customerToUser(): HasMany
    {
        return $this->HasMany(CustomerToUser::class, 'user_id' );
    }

    /**
     * Scope a query to match active user only
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActiveOnly( Builder $query ): Builder
    {
        return $query->where('disabled', false );
    }

    /**
     * Scope a query to match user by privs
     *
     * @param  Builder  $query
     * @param  int|null  $priv
     *
     * @return Builder
     */
    public function scopeByPrivs( Builder $query, int $priv = null ): Builder
    {
        return $query->select( 'user.*' )->leftJoin( 'customer_to_users AS c2u', 'c2u.user_id', 'user.id' )
            ->when( $priv, function( Builder $q, $priv ){
                return $q->where( 'c2u.privs', $priv );
            } )->distinct();
    }

    /**
     * Is the user of the named type?
     *
     * @return bool
     */
    public function isCustUser(): bool
    {
        return $this->privs() === self::AUTH_CUSTUSER;
    }

    /**
     * Is the user of the named type?
     * @return bool
     */
    public function isCustAdmin(): bool
    {
        return $this->privs() === self::AUTH_CUSTADMIN;
    }

    /**
     * Is the user of the named type?
     *
     * @return bool
     */
    public function isSuperUser(): bool
    {
        return $this->privs() === self::AUTH_SUPERUSER;
    }

    /**
     * Get privilege from the table CustomerToUser
     *
     * @return int|null
     */
    public function privs(): ?int
    {
        $c2u = CustomerToUser::where( 'customer_id' , $this->custid )->where( 'user_id' , $this->id )->first();
        if( $c2u ) {
            return $c2u->privs;
        }
        return null;
    }

    /**
     * Does 2fa need to be enforced for this user?
     *
     * @return bool
     */
    public function is2faEnforced(): bool
    {
        if( !config('google2fa.enabled') ) {
            return false;
        }

        return $this->privs() >= config( "google2fa.ixpm_2fa_enforce_for_users" )
            && ( !$this->user2FA || !$this->user2FA->enabled );
    }

    /**
     * Check if the user is required to authenticate with 2FA for the current session
     *
     * @return bool
     */
    public function is2faAuthRequiredForSession(): bool
    {
        if( !config('google2fa.enabled') ) {
            return false;
        }

        if( !$this->user2FA || !$this->user2FA->enabled ) {
            // If the user does not have 2fa configured or enabled but it is required, then return true:
            if( $this->is2faEnforced() ) {
                return true;
            }
            return false;
        }

        $authenticator = new GoogleAuthenticator( request() );

        if( $authenticator->isAuthenticated() ) {
            return false;
        }
        return true;
    }

    /**
     * Get the current customer to user - if one exists.
     *
     * @return CustomerToUser|null
     */
    public function currentCustomerToUser(): ?CustomerToUser
    {
        if( !$this->customer ) {
            return null;
        }

        $c2u = CustomerToUser::where( 'customer_id', $this->custid )
            ->where( 'user_id', $this->id )->first();

        return $c2u ?? null;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     *
     * @return void
     */
    public function sendPasswordResetNotification( $token ): void
    {
        event( new ForgotPasswordEvent( $token, $this ) );
    }

    /**
     * Get the "remember me" token value.
     *
     * // We have overridden Laravel's remember token functionality and do not rely on this.
     * // However, some Laravel functionality if triggered on this returning a non-false value
     * // to execute certain functionality. As such, we'll just return something random:
     *
     * @return string
     */
    public function getRememberToken(): string
    {
        // We have overridden Laravel's remember token functionality and do not rely on this.
        // However, some Laravel functionality if triggered on this returning a non-false value
        // to execute certain functionality. As such, we'll just return something random:
        return Str::random(60);
    }

    /**
     * Allow direct access to the 2FA secret code
     */
    public function __get( $key )
    {
        switch( $key ) {
            // google2fa Laravel bridge looking for 2fa secret
            case 'secret':
                return $this->user2FA->secret ?? null;
                break;
        }

        return parent::__get( $key );
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
            "User [id:%d] '%s'",
            $model->id,
            $model->username,
        );
    }
}