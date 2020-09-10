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

use Doctrine\ORM\Mapping as ORM;

/**
 * IrrdbPrefix
 */
class IrrdbPrefix
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var integer
     */
    private $protocol;

    protected $created_at;
    protected $updated_at;

    /**
     * @var \DateTime
     */
    private $first_seen;

    /**
     * @var \DateTime
     */
    private $last_seen;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\Customer
     */
    private $Customer;


    /**
     * Set prefix
     *
     * @param string $prefix
     * @return IrrdbPrefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    
        return $this;
    }

    /**
     * Get prefix
     *
     * @return string 
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set protocol
     *
     * @param integer $protocol
     * @return IrrdbPrefix
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    
        return $this;
    }

    /**
     * Get protocol
     *
     * @return integer 
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set first_seen
     *
     * @param \DateTime $firstSeen
     * @return IrrdbPrefix
     */
    public function setFirstSeen($firstSeen)
    {
        $this->first_seen = $firstSeen;
    
        return $this;
    }

    /**
     * Get first_seen
     *
     * @return \DateTime 
     */
    public function getFirstSeen()
    {
        return $this->first_seen;
    }

    /**
     * Set last_seen
     *
     * @param \DateTime $lastSeen
     * @return IrrdbPrefix
     */
    public function setLastSeen($lastSeen)
    {
        $this->last_seen = $lastSeen;
    
        return $this;
    }

    /**
     * Get last_seen
     *
     * @return \DateTime 
     */
    public function getLastSeen()
    {
        return $this->last_seen;
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
     * @param \Entities\Customer $customer
     * @return IrrdbPrefix
     */
    public function setCustomer(\Entities\Customer $customer)
    {
        $this->Customer = $customer;
    
        return $this;
    }

    /**
     * Get Customer
     *
     * @return \Entities\Customer 
     */
    public function getCustomer()
    {
        return $this->Customer;
    }
}
