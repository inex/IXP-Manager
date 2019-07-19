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

use Str;

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
     * @var string
     */
    private $description;

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
        return config( 'ixp_fe.api_keys.show_keys' ) ? $this->apiKey : Str::limit( $this->apiKey, 6 ) ;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ApiKey
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
