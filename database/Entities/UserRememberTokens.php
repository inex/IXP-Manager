<?php

namespace Entities;

use Datetime;

use Entities\{
    User as UserEntity
};

/**
 * RememberTokens
 */
class UserRememberTokens
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
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken( string $token ): void
    {
        $this->token = $token;
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
     */
    public function setDevice( string $device ): void
    {
        $this->device = $device;
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
     */
    public function setId( string $ip ): void
    {
        $this->ip = $ip;
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
     */
    public function setCreated( DateTime $created ): void
    {
        $this->created = $created;
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
     */
    public function setExpires( DateTime $expires ): void
    {
        $this->expires = $expires;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->User;
    }

    /**
     * @param User $User
     */
    public function setUser( User $User ): void
    {
        $this->User = $User;
    }



}
