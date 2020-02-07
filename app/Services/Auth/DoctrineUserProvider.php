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

use Illuminate\Support\Str;
use Entities\{
    User                as UserEntity,
    UserRememberToken
};

use Doctrine\Common\Persistence\ObjectRepository;

use IXP\Utils\IpAddress;

use LaravelDoctrine\ORM\Auth\DoctrineUserProvider as DoctrineUserProviderBase;

use Wolfcast\BrowserDetection;


/**
 * Class DoctrineUserProvider
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
class DoctrineUserProvider extends DoctrineUserProviderBase
{
    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     *
     * @throws
     */
    public function retrieveByToken( $identifier, $token )
    {
        $urt = $this->em->getRepository( UserRememberToken::class )->findOneBy( [ "User" => $identifier, "token" => $token ] );

        if( !$urt  ) {
            return null;
        }

        return $urt->getExpires() > now() ? $urt->getUser() : null;
    }

    /**
     * Add a new user remember token for a "remember me" session.
     *
     * @param UserEntity|\Illuminate\Contracts\Auth\Authenticatable $user
     * @return UserRememberToken
     *
     * @throws
     */
    public function addRememberToken( $user ): UserRememberToken
    {
        $urt = new UserRememberToken;
        $browser = new BrowserDetection();

        $urt->setUser( $user )
            ->setToken(Str::random(60))
            ->setExpires( now()->addMinutes( config('auth.guards.web.expire', 60) ) )
            ->setCreated( now() )
            ->setDevice( $browser->getPlatform() . " " . $browser->getPlatformVersion(true) . " / " . $browser->getName() . " " . $browser->getVersion() )
            ->setIp( IpAddress::getIp() );

        $this->em->persist( $urt );
        $user->addUserRememberToken( $urt );
        $this->em->flush();
        
        return $urt;
    }

    /**
     * Purge old or expired "remember me" tokens.
     *
     * @param  UserEntity $user
     *
     * @throws
     */
    public function purgeExpiredRememberTokens( $user )
    {
        $this->em->createQuery(
            "DELETE FROM Entities\\UserRememberToken urt WHERE urt.User = " . $user->getId()
                . " AND urt.expires <= '" . now()->format( 'Y-m-d H:i:s' ) . "'"
        )->execute();
    }
}
