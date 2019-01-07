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
    Contact as ContactEntity
};
/**
 * ContactGroup
 */
class ContactGroup
{
    const TYPE_ROLE = 'ROLE';
    
    public static $TYPES = [
        self::TYPE_ROLE => 'Role'
    ];
    
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var integer $limited_to
     */
    protected $limited_to;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $Contacts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Contacts = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return ContactGroup
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
     * Set description
     *
     * @param string $description
     * @return ContactGroup
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return ContactGroup
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return ContactGroup
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set limited_to
     *
     * @param integer $limitedTo
     * @return ContactGroup
     */
    public function setLimitedTo($limitedTo)
    {
        $this->limited_to = $limitedTo;
        return $this;
    }

    /**
     * Get limited_to
     *
     * @return integer
     */
    public function getLimitedTo()
    {
        return $this->limited_to;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ContactGroup
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
     * Add Contacts
     *
     * @param ContactEntity $contacts
     * @return ContactGroup
     */
    public function addContact( ContactEntity $contacts)
    {
        $this->Contacts[] = $contacts;
    
        return $this;
    }

    /**
     * Remove Contacts
     *
     * @param ContactEntity $contacts
     */
    public function removeContact( ContactEntity $contacts)
    {
        $this->Contacts->removeElement($contacts);
    }

    /**
     * Get Contacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->Contacts;
    }
    
}
