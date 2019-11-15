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

use DateTime;

use Entities\{
    User                as UserEntity,
    UserRememberTokens  as UserRememberTokensEntity
};

use Doctrine\Common\Persistence\ObjectRepository;

use IXP\Utils\IpAddress;

use LaravelDoctrine\ORM\Auth\DoctrineUserProvider as DoctrineUserProviderBase;

use Wolfcast\BrowserDetection;


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
        if (! $model = $this->getRememberPasswordRepository()->findOneBy( [ "User" => $identifier, "token" => $token ] ) ){
            return null;
        }

        $now = new DateTime();
        return $model->getExpires()->format( 'Y-m-d H:i:s' ) > $now->format( 'Y-m-d H:i:s' ) ? $model->getUser() : null;
    }

    /**
     * Add a token value for the "remember me" session.
     *
     * @param $identifier
     * @param string $value
     * @param int $expire
     * @return void
     *
     * @throws
     */
    public function addRememberToken( $identifier, $value, $expire )
    {
        /** @var $user UserEntity */
        if (! $user = $this->getRepository()->findOneBy( [ $this->getEntity()->getAuthIdentifierName() => $identifier ]  ) ) {
            return null;
        }

        $rememberToken = new UserRememberTokensEntity;
        $this->em->persist( $rememberToken );

        $browser = new BrowserDetection();

        $rememberToken->setUser( $user );
        $rememberToken->setToken( $value );
        $rememberToken->setExpires( new DateTime( "+$expire minutes" ) );
        $rememberToken->setCreated( new DateTime() );
        $rememberToken->setDevice( $browser->getPlatform() . " " . $browser->getPlatformVersion(true) . " / " . $browser->getName() . " " . $browser->getVersion() );
        $rememberToken->setId( IpAddress::getIp() );

        $user->addUserRememberTokens( $rememberToken );

        $this->em->flush();
    }

    /**
     * Replace "remember me" token with new token.
     *
     * @param $identifier
     * @param string $token
     * @param string $newToken
     * @param int $expire
     *
     * @return void
     *
     * @throws
     */
    public function replaceRememberToken($identifier, $token, $newToken, $expire)
    {
        if (! $rt = $this->getRememberPasswordRepository()->findOneBy( [ "User" => $identifier, "token" => $token ]  ) ) {
            return null;
        }
        $rt->setToken( $newToken );
        $rt->setExpires( new DateTime( "+$expire minutes" ) );

        $this->em->flush();
    }

    /**
     * Delete the specified "remember me" token for the given user.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return null
     */
    public function deleteRememberToken( $identifier, $token )
    {
        if (! $rt = $this->getRememberPasswordRepository()->findOneBy( [ "User" => $identifier, "token" => $token ]  ) ) {
            return null;
        }
        $this->em->remove( $rt );
        $this->em->flush();

        return true;
    }

    /**
     * Purge old or expired "remember me" tokens.
     *
     * @param  mixed $identifier
     * @param  bool $expired
     * @return null
     *
     * @throws
     */
    public function purgeRememberTokens( $identifier, $expired = false )
    {
        $sql = "DELETE FROM Entities\\UserRememberTokens rt WHERE rt.User = " . $identifier;

        if ( $expired ) {
            $now = new DateTime();
            $sql .= " AND rt.expires < '" . $now->format( 'Y-m-d H:i:s' ) . "'";
        }

        return $this->em->createQuery( $sql )->execute();
    }

    /**
     * Returns repository for the remember token entity.
     * @return ObjectRepository
     */
    protected function getRememberPasswordRepository()
    {
        return $this->em->getRepository( UserRememberTokensEntity::class );
    }
}
