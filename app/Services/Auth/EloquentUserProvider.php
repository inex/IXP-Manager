<?php

namespace IXP\Services\Auth;

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

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use Illuminate\Contracts\Auth\UserProvider as IlluminateUserProvider;

use IXP\Models\{
    User,
    UserRememberToken
};

use IXP\Utils\IpAddress;

use Wolfcast\BrowserDetection;


/**
 * Class EloquentUserProvider
 *
 * A small set of functions we need to override from LaravelDoctrine's provider to allow for IXP Manager's
 * user session management functionality.
 *
 * @see        https://docs.ixpmanager.org/dev/authentication/
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @package    IXP\Services\Auth
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class EloquentUserProvider implements IlluminateUserProvider
{
    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * @var string
     */
    protected $model;

    /**
     * @param Hasher    $hasher
     * @param           $model
     */
    public function __construct( Hasher $hasher,  $model)
    {
        $this->hasher   = $hasher;
        $this->model    = $model;
    }
    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed    $identifier
     * @param  string   $token
     *
     * @return Authenticatable|null
     *
     * @throws
     */
    public function retrieveByToken( $identifier, $token ): ?Authenticatable
    {
        $urt = UserRememberToken::where( [ 'user_id' => $identifier ] )->where( [ 'token' => $token ] )->first();

        if( !$urt  ) {
            return null;
        }

        return $urt->expires > now() ? $urt->user : null;
    }

    /**
     * Add a new user remember token for a "remember me" session.
     *
     * @param User|Authenticatable $user
     *
     * @return UserRememberToken
     *
     * @throws
     */
    public function addRememberToken( $user ): UserRememberToken
    {
        $browser = new BrowserDetection();

        return UserRememberToken::create([
            'user_id'   => $user->id,
            'token'     => Str::random(60),
            'device'    => $browser->getPlatform() . " " . $browser->getPlatformVersion(true) . " / " . $browser->getName() . " " . $browser->getVersion() ,
            'ip'        => IpAddress::getIp(),
            'expires'   => now()->addMinutes( config('auth.guards.web.expire', 60) ),
        ]);
    }

    /**
     * Purge old or expired "remember me" tokens.
     *
     * @param  User $user
     *
     * @throws
     */
    public function purgeExpiredRememberTokens( User $user ): void
    {
        UserRememberToken::where( 'user_id', $user->id )
            ->where( 'expires', '<=', now()->format( 'Y-m-d H:i:s' ) )
            ->delete();
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     *
     * @return Authenticatable|null
     */
    public function retrieveById( $identifier ): ?Authenticatable
    {
        return $this->model::find( $identifier );
    }

    /**
     * We do not need this as we have a multi remember token per user
     *
     * @param Authenticatable|Model  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken( Authenticatable $user, $token ){}

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     *
     * @return Authenticatable|null
     */
    public function retrieveByCredentials( array $credentials ): ?Authenticatable
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->model::query();

        foreach( $credentials as $key => $value ) {
            if( Str::contains( $key, 'password' ) ) {
                continue;
            }

            if( is_array($value) || $value instanceof Arrayable ) {
                $query->whereIn( $key, $value );
            } else {
                $query->where( $key, $value );
            }
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $user
     * @param array           $credentials
     *
     * @return bool
     */
    public function validateCredentials( Authenticatable $user, array $credentials ): bool
    {
        return $this->hasher->check( $credentials['password'], $user->getAuthPassword() );
    }
}