<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Contact
 */
class Contact
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @var string $phone
     */
    protected $phone;

    /**
     * @var string $mobile
     */
    protected $mobile;

    /**
     * @var integer $facilityaccess
     */
    protected $facilityaccess;

    /**
     * @var boolean $mayauthorize
     */
    protected $mayauthorize;

    /**
     * @var \DateTime $lastupdated
     */
    protected $lastupdated;

    /**
     * @var integer $lastupdatedby
     */
    protected $lastupdatedby;

    /**
     * @var string $creator
     */
    protected $creator;

    /**
     * @var \DateTime $created
     */
    protected $created;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\Customer
     */
    protected $Customer;
    
     /**
     * @var string
     */
    protected $position;

    /**
     * @var \Entities\User
     */
    protected $User;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $Groups;


    /**
     * Set name
     *
     * @param string $name
     * @return Contact
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Contact
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
     * Set phone
     *
     * @param string $phone
     * @return Contact
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return Contact
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    
        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set facilityaccess
     *
     * @param integer $facilityaccess
     * @return Contact
     */
    public function setFacilityaccess($facilityaccess)
    {
        $this->facilityaccess = $facilityaccess;
    
        return $this;
    }

    /**
     * Get facilityaccess
     *
     * @return integer
     */
    public function getFacilityaccess()
    {
        return $this->facilityaccess;
    }

    /**
     * Set mayauthorize
     *
     * @param boolean $mayauthorize
     * @return Contact
     */
    public function setMayauthorize($mayauthorize)
    {
        $this->mayauthorize = $mayauthorize;
    
        return $this;
    }

    /**
     * Get mayauthorize
     *
     * @return boolean
     */
    public function getMayauthorize()
    {
        return $this->mayauthorize;
    }

    /**
     * Set lastupdated
     *
     * @param \DateTime $lastupdated
     * @return Contact
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
     * @return Contact
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
     * @return Contact
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
     * @return Contact
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
     * Set Customer
     *
     * @param Entities\Customer $customer
     * @return Contact
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
     * Constructor
     */
    public function __construct()
    {
        $this->Groups = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set position
     *
     * @param string $position
     * @return Contact
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set User
     *
     * @param \Entities\User $user
     * @return Contact
     */
    public function setUser(\Entities\User $user)
    {
        $this->User = $user;
    
        return $this;
    }
    
    /**
     * Unset User
     *
     * @return Contact
     */
    public function unsetUser()
    {
        $this->User = null;
    
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

    /**
     * Add Groups
     *
     * @param \Entities\ContactGroup $groups
     * @return Contact
     */
    public function addGroup(\Entities\ContactGroup $groups)
    {
        $this->Groups[] = $groups;
    
        return $this;
    }

    /**
     * Remove Groups
     *
     * @param \Entities\ContactGroup $groups
     */
    public function removeGroup(\Entities\ContactGroup $groups)
    {
        $this->Groups->removeElement($groups);
    }

    /**
     * Get Groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->Groups;
    }
    /**
     * @var string
     */
    protected $notes;


    /**
     * Set notes
     *
     * @param string $notes
     * @return Contact
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    
        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
}