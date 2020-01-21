<?php

namespace IXP\Contracts\Auth;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Contracts\Auth\UserProvider as BaseProvider;

interface UserProvider extends BaseProvider
{
    /**
     * Add a token value for the "remember me" session.
     *
     * @param $identifier
     * @param string $value
     * @param int $expire
     *
     * @return void
     */
    public function addRememberToken( $identifier, $value, $expire );

    /**
     * Replace "remember me" token with a new token.
     *
     * @param $identifier
     * @param string $token
     * @param string $newToken
     * @param int $expire
     *
     * @return void
     */
    public function replaceRememberToken( $identifier, $token, $newToken, $expire );

    /**
     * Delete the specified "remember me" token for the given user.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return null
     */
    public function deleteRememberToken( $identifier, $token );

    /**
     * Purge old or expired "remember me" tokens.
     *
     * @param  mixed $identifier
     * @param  bool $expired
     * @return null
     */
    public function purgeRememberTokens( $identifier, $expired = false );
}