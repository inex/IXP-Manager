<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\ChangeLog
 */
class ChangeLog
{
    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $details
     */
    protected $details;

    /**
     * @var integer $visibility
     */
    protected $visibility;

    /**
     * @var \DateTime $livedate
     */
    protected $livedate;

    /**
     * @var integer $version
     */
    protected $version;

    /**
     * @var \DateTime $created_at
     */
    protected $created_at;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\User
     */
    protected $User;


    /**
     * Set title
     *
     * @param string $title
     * @return ChangeLog
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return ChangeLog
     */
    public function setDetails($details)
    {
        $this->details = $details;
    
        return $this;
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set visibility
     *
     * @param integer $visibility
     * @return ChangeLog
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    
        return $this;
    }

    /**
     * Get visibility
     *
     * @return integer 
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set livedate
     *
     * @param \DateTime $livedate
     * @return ChangeLog
     */
    public function setLivedate($livedate)
    {
        $this->livedate = $livedate;
    
        return $this;
    }

    /**
     * Get livedate
     *
     * @return \DateTime 
     */
    public function getLivedate()
    {
        return $this->livedate;
    }

    /**
     * Set version
     *
     * @param integer $version
     * @return ChangeLog
     */
    public function setVersion($version)
    {
        $this->version = $version;
    
        return $this;
    }

    /**
     * Get version
     *
     * @return integer 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return ChangeLog
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
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
     * @param Entities\User $user
     * @return ChangeLog
     */
    public function setUser(\Entities\User $user = null)
    {
        $this->User = $user;
    
        return $this;
    }

    /**
     * Get User
     *
     * @return Entities\User 
     */
    public function getUser()
    {
        return $this->User;
    }
}