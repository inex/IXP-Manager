<?php

namespace Entities;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Datetime;

use Entities\{
    User as UserEntity,
    UserRememberToken as UserRememberTokenEntity,
};

/**
 * UserRememberToken
 */
class UserRememberToken
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $device;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $expires;

    /**
     * @var int
     */
    private $id;

    /**
     * @var UserEntity
     */
    private $User;

    /**
     * If the user has 2fa enabled, we need to ensure that they have originally completed
     * that process before allowing them in.
     *
     * @var bool
     */
    private $is_2fa_complete = false;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return UserRememberToken
     */
    public function setToken( string $token ): UserRememberToken
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getDevice(): string
    {
        return $this->device;
    }

    /**
     * @param string $device
     * @return UserRememberToken
     */
    public function setDevice( string $device ): UserRememberToken
    {
        $this->device = $device;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return UserRememberToken
     */
    public function setIp( string $ip ): UserRememberToken
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     * @return UserRememberToken
     */
    public function setCreated( DateTime $created ): UserRememberToken
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpires(): DateTime
    {
        return $this->expires;
    }

    /**
     * @param DateTime $expires
     * @return UserRememberToken
     */
    public function setExpires( DateTime $expires ): UserRememberToken
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return UserRememberToken
     */
    public function setId( int $id ): UserRememberToken
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return UserEntity
     */
    public function getUser(): UserEntity
    {
        return $this->User;
    }

    /**
     * @param UserEntity $User
     * @return UserRememberToken
     */
    public function setUser( UserEntity $User ): UserRememberToken
    {
        $this->User = $User;
        return $this;
    }

    /**
     * Set 2fa complete
     *
     * @param bool $is_2fa_complete
     * @return UserRememberTokenEntity
     */
    public function setIs2faComplete( bool $is_2fa_complete ): UserRememberToken
    {
        $this->is_2fa_complete = $is_2fa_complete;

        return $this;
    }

    /**
     * Get is_2fa_complete
     *
     * @return bool
     */
    public function getIs2faComplete(): bool
    {
        return $this->is_2fa_complete;
    }


    /**
     * Has this token expired?
     *
     * @return bool
     */
    public function isExpired(): bool {
        return $this->getExpires() < now();
    }
}
