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
 * Entities\IRRDBConfig
 */
class IRRDBConfig
{
    /**
     * @var string $host
     */
    protected $host;

    /**
     * @var string $protocol
     */
    protected $protocol;

    protected $created_at;
    protected $updated_at;

    /**
     * @var string $source
     */
    protected $source;

    /**
     * @var string $notes
     */
    protected $notes;

    /**
     * @var integer $id
     */
    protected $id;


    /**
     * Set host
     *
     * @param string $host
     * @return IRRDBConfig
     */
    public function setHost($host)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return string 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set protocol
     *
     * @param string $protocol
     * @return IRRDBConfig
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    
        return $this;
    }

    /**
     * Get protocol
     *
     * @return string 
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return IRRDBConfig
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return IRRDBConfig
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
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $Customers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Customers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add Customers
     *
     * @param \Entities\Customer $customers
     * @return IRRDBConfig
     */
    public function addCustomer(\Entities\Customer $customers)
    {
        $this->Customers[] = $customers;
    
        return $this;
    }

    /**
     * Remove Customers
     *
     * @param \Entities\Customer $customers
     */
    public function removeCustomer(\Entities\Customer $customers)
    {
        $this->Customers->removeElement($customers);
    }

    /**
     * Get Customers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCustomers()
    {
        return $this->Customers;
    }
}
