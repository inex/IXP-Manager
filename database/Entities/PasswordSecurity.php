<?php

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

namespace Entities;

use DateTime;

use Entities\{
    User as UserEntity
};

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entities\PatchPanel
 */
class PasswordSecurity
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var boolean $active
     */
    private $google2fa_enable = false;

    /**
     * @var string
     */
    private $google2fa_secret;

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
     * @return PasswordSecurity
     */
    public function setId( int $id ): PasswordSecurity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isGoogle2faEnable(): bool
    {
        return $this->google2fa_enable;
    }

    /**
     * @param bool $google2fa_enable
     * @return PasswordSecurity
     */
    public function setGoogle2faEnable( bool $google2fa_enable ): PasswordSecurity
    {
        $this->google2fa_enable = $google2fa_enable;
        return $this;
    }

    /**
     * @return string
     */
    public function getGoogle2faSecret(): string
    {
        return $this->google2fa_secret;
    }

    /**
     * @param string $google2fa_secret
     * @return PasswordSecurity
     */
    public function setGoogle2faSecret( string $google2fa_secret ): PasswordSecurity
    {
        $this->google2fa_secret = $google2fa_secret;
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
     * @return PasswordSecurity
     */
    public function setCreatedAt( DateTime $created_at ): PasswordSecurity
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
     * @return PasswordSecurity
     */
    public function setUpdatedAt( DateTime $updated_at ): PasswordSecurity
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
     * @return PasswordSecurity
     */
    public function setUser( UserEntity $User ): PasswordSecurity
    {
        $this->User = $User;
        return $this;
    }


}

