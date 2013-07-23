<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserLoginHistory
 */
class UserLoginHistory
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @var \DateTime
     */
    protected $at;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Entities\User
     */
    protected $User;


    /**
     * Set ip
     *
     * @param string $ip
     * @return UserLoginHistory
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set at
     *
     * @param \DateTime $at
     * @return UserLoginHistory
     */
    public function setAt($at)
    {
        $this->at = $at;
    
        return $this;
    }

    /**
     * Get at
     *
     * @return \DateTime 
     */
    public function getAt()
    {
        return $this->at;
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
     * @return UserLoginHistory
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