<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\User
 */
class User
{
    /**
     * @var string $username
     */
    private $username;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $authorisedMobile
     */
    private $authorisedMobile;

    /**
     * @var integer $uid
     */
    private $uid;

    /**
     * @var integer $privs
     */
    private $privs;

    /**
     * @var boolean $disabled
     */
    private $disabled;

    /**
     * @var \DateTime $lastupdated
     */
    private $lastupdated;

    /**
     * @var integer $lastupdatedby
     */
    private $lastupdatedby;

    /**
     * @var string $creator
     */
    private $creator;

    /**
     * @var \DateTime $created
     */
    private $created;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $Parent;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $Preferences;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $ChangeLogs;

    /**
     * @var Entities\Customer
     */
    private $Customer;

    /**
     * @var Entities\User
     */
    private $Children;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Parent = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Preferences = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ChangeLogs = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set authorisedMobile
     *
     * @param string $authorisedMobile
     * @return User
     */
    public function setAuthorisedMobile($authorisedMobile)
    {
        $this->authorisedMobile = $authorisedMobile;
    
        return $this;
    }

    /**
     * Get authorisedMobile
     *
     * @return string 
     */
    public function getAuthorisedMobile()
    {
        return $this->authorisedMobile;
    }

    /**
     * Set uid
     *
     * @param integer $uid
     * @return User
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    
        return $this;
    }

    /**
     * Get uid
     *
     * @return integer 
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set privs
     *
     * @param integer $privs
     * @return User
     */
    public function setPrivs($privs)
    {
        $this->privs = $privs;
    
        return $this;
    }

    /**
     * Get privs
     *
     * @return integer 
     */
    public function getPrivs()
    {
        return $this->privs;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return User
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    
        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean 
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set lastupdated
     *
     * @param \DateTime $lastupdated
     * @return User
     */
    public function setLastupdated($lastupdated)
    {
        $this->lastupdated = $lastupdated;
    
        return $this;
    }

    /**
     * Get lastupdated
     *
     * @return \DateTime 
     */
    public function getLastupdated()
    {
        return $this->lastupdated;
    }

    /**
     * Set lastupdatedby
     *
     * @param integer $lastupdatedby
     * @return User
     */
    public function setLastupdatedby($lastupdatedby)
    {
        $this->lastupdatedby = $lastupdatedby;
    
        return $this;
    }

    /**
     * Get lastupdatedby
     *
     * @return integer 
     */
    public function getLastupdatedby()
    {
        return $this->lastupdatedby;
    }

    /**
     * Set creator
     *
     * @param string $creator
     * @return User
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    
        return $this;
    }

    /**
     * Get creator
     *
     * @return string 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add Parent
     *
     * @param Entities\User $parent
     * @return User
     */
    public function addParent(\Entities\User $parent)
    {
        $this->Parent[] = $parent;
    
        return $this;
    }

    /**
     * Remove Parent
     *
     * @param Entities\User $parent
     */
    public function removeParent(\Entities\User $parent)
    {
        $this->Parent->removeElement($parent);
    }

    /**
     * Get Parent
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getParent()
    {
        return $this->Parent;
    }

    /**
     * Add Preferences
     *
     * @param Entities\UserPreference $preferences
     * @return User
     */
    public function addPreference(\Entities\UserPreference $preferences)
    {
        $this->Preferences[] = $preferences;
    
        return $this;
    }

    /**
     * Remove Preferences
     *
     * @param Entities\UserPreference $preferences
     */
    public function removePreference(\Entities\UserPreference $preferences)
    {
        $this->Preferences->removeElement($preferences);
    }

    /**
     * Get Preferences
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPreferences()
    {
        return $this->Preferences;
    }

    /**
     * Add ChangeLogs
     *
     * @param Entities\ChangeLog $changeLogs
     * @return User
     */
    public function addChangeLog(\Entities\ChangeLog $changeLogs)
    {
        $this->ChangeLogs[] = $changeLogs;
    
        return $this;
    }

    /**
     * Remove ChangeLogs
     *
     * @param Entities\ChangeLog $changeLogs
     */
    public function removeChangeLog(\Entities\ChangeLog $changeLogs)
    {
        $this->ChangeLogs->removeElement($changeLogs);
    }

    /**
     * Get ChangeLogs
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getChangeLogs()
    {
        return $this->ChangeLogs;
    }

    /**
     * Set Customer
     *
     * @param Entities\Customer $customer
     * @return User
     */
    public function setCustomer(\Entities\Customer $customer = null)
    {
        $this->Customer = $customer;
    
        return $this;
    }

    /**
     * Get Customer
     *
     * @return Entities\Customer 
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * Set Children
     *
     * @param Entities\User $children
     * @return User
     */
    public function setChildren(\Entities\User $children = null)
    {
        $this->Children = $children;
    
        return $this;
    }

    /**
     * Get Children
     *
     * @return Entities\User 
     */
    public function getChildren()
    {
        return $this->Children;
    }
}