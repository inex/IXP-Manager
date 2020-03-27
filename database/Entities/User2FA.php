<?php

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Entities;

use DateTime;

use Entities\{
    User as UserEntity
};

use Doctrine\Common\Collections\ArrayCollection;

/**

 */
class User2FA
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var boolean $active
     */
    private $enabled = false;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var DateTime
     */
    private $created_at;

    /**
     * @var DateTime
     */
    private $updated_at;

    /**
     * @var UserEntity
     */
    protected $User;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User2FA
     */
    public function setId( int $id ): User2FA
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function enabled(): bool
    {
        return $this->getEnabled();
    }

    /**
     * @param bool $enabled
     * @return User2FA
     */
    public function setEnabled( bool $enabled ): User2FA
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     * @return User2FA
     */
    public function setSecret( string $secret ): User2FA
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * @param DateTime $created_at
     * @return User2FA
     */
    public function setCreatedAt( DateTime $created_at ): User2FA
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    /**
     * @param DateTime $updated_at
     * @return User2FA
     */
    public function setUpdatedAt( DateTime $updated_at ): User2FA
    {
        $this->updated_at = $updated_at;
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
     * @return User2FA
     */
    public function setUser( UserEntity $User ): User2FA
    {
        $this->User = $User;
        return $this;
    }


}

