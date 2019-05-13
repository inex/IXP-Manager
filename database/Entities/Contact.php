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

use Entities\{
    ContactGroup    as ContactGroupEntity,
    Customer        as CustomerEntity
};

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
    protected $facilityaccess = false;

    /**
     * @var boolean $mayauthorize
     */
    protected $mayauthorize = false;

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
     * @var CustomerEntity
     */
    protected $Customer;
    
     /**
     * @var string
     */
    protected $position;

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
     * @param CustomerEntity $customer
     * @return Contact
     */
    public function setCustomer( CustomerEntity $customer = null)
    {
        $this->Customer = $customer;
    
        return $this;
    }

    /**
     * Get Customer
     *
     * @return CustomerEntity
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
     * Add Groups
     *
     * @param ContactGroupEntity $groups
     * @return Contact
     */
    public function addGroup( ContactGroupEntity $groups)
    {
        $this->Groups[] = $groups;
    
        return $this;
    }

    /**
     * Remove Groups
     *
     * @param ContactGroupEntity $groups
     */
    public function removeGroup( ContactGroupEntity $groups)
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
