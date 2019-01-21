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
 * RSPrefix
 */
class RSPrefix
{
    /**
     * Map prefix acceptance types to summary functions
     * @var array Map prefix acceptance types to summary functions
     */
    public static $SUMMARY_TYPES_FNS = [
        'adv_acc'  => 'getSummaryRoutesAdvertisedAndAccepted',
        'adv_nacc' => 'getSummaryRoutesAdvertisedAndNotAccepted',
        'nadv_acc' => 'getSummaryRoutesNotAdvertisedButAcceptable'
    ];
    
    /**
     * Map prefix acceptance types to lookup functions
     * @var array Map prefix acceptance types to lookup functions
     */
    public static $ROUTES_TYPES_FNS = [
        'adv_acc'  => 'getRoutesAdvertisedAndAccepted',
        'adv_nacc' => 'getRoutesAdvertisedAndNotAccepted',
        'nadv_acc' => 'getRoutesNotAdvertisedButAcceptable'
    ];
    
    
    /**
     * @var \DateTime
     */
    protected $timestamp;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var integer
     */
    protected $protocol;

    /**
     * @var integer
     */
    protected $irrdb;

    /**
     * @var integer
     */
    protected $rs_origin;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Entities\Customer
     */
    protected $Customer;


    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return RSPrefix
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    
        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set prefix
     *
     * @param string $prefix
     * @return RSPrefix
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
     * @return RSPrefix
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
     * Set irrdb
     *
     * @param integer $irrdb
     * @return RSPrefix
     */
    public function setIrrdb($irrdb)
    {
        $this->irrdb = $irrdb;
    
        return $this;
    }

    /**
     * Get irrdb
     *
     * @return integer
     */
    public function getIrrdb()
    {
        return $this->irrdb;
    }

    /**
     * Set rs_origin
     *
     * @param integer $rsOrigin
     * @return RSPrefix
     */
    public function setRsOrigin($rsOrigin)
    {
        $this->rs_origin = $rsOrigin;
    
        return $this;
    }

    /**
     * Get rs_origin
     *
     * @return integer
     */
    public function getRsOrigin()
    {
        return $this->rs_origin;
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
     * @return RSPrefix
     */
    public function setCustomer(\Entities\Customer $customer = null)
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
