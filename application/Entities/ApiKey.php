<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApiKey
 */
class ApiKey
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var \DateTime
     */
    private $expires;

    /**
     * @var string
     */
    private $allowedIPs;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $lastseenAt;

    /**
     * @var string
     */
    private $lastseenFrom;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\User
     */
    private $User;


    /**
     * Set apiKey
     *
     * @param string $apiKey
     * @return ApiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    
        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string 
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set expires
     *
     * @param \DateTime $expires
     * @return ApiKey
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    
        return $this;
    }

    /**
     * Get expires
     *
     * @return \DateTime 
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Set allowedIPs
     *
     * @param string $allowedIPs
     * @return ApiKey
     */
    public function setAllowedIPs($allowedIPs)
    {
        $this->allowedIPs = $allowedIPs;
    
        return $this;
    }

    /**
     * Get allowedIPs
     *
     * @return string 
     */
    public function getAllowedIPs()
    {
        return $this->allowedIPs;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ApiKey
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set lastseenAt
     *
     * @param \DateTime $lastseenAt
     * @return ApiKey
     */
    public function setLastseenAt($lastseenAt)
    {
        $this->lastseenAt = $lastseenAt;
    
        return $this;
    }

    /**
     * Get lastseenAt
     *
     * @return \DateTime 
     */
    public function getLastseenAt()
    {
        return $this->lastseenAt;
    }

    /**
     * Set lastseenFrom
     *
     * @param string $lastseenFrom
     * @return ApiKey
     */
    public function setLastseenFrom($lastseenFrom)
    {
        $this->lastseenFrom = $lastseenFrom;
    
        return $this;
    }

    /**
     * Get lastseenFrom
     *
     * @return string 
     */
    public function getLastseenFrom()
    {
        return $this->lastseenFrom;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set User
     *
     * @param \Entities\User $user
     * @return ApiKey
     */
    public function setUser(\Entities\User $user)
    {
        $this->User = $user;
    
        return $this;
    }

    /**
     * Get User
     *
     * @return \Entities\User 
     */
    public function getUser()
    {
        return $this->User;
    }
}