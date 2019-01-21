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
 * Entities\PeeringMatrix
 */
class PeeringMatrix
{
    /**
     * @var integer $x_as
     */
    protected $x_as;

    /**
     * @var integer $y_as
     */
    protected $y_as;

    /**
     * @var string $peering_status
     */
    protected $peering_status;

    /**
     * @var \DateTime $updated
     */
    protected $updated;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\Customer
     */
    protected $XCustomer;

    /**
     * @var Entities\Customer
     */
    protected $YCustomer;


    /**
     * Set x_as
     *
     * @param integer $xAs
     * @return PeeringMatrix
     */
    public function setXAs($xAs)
    {
        $this->x_as = $xAs;
    
        return $this;
    }

    /**
     * Get x_as
     *
     * @return integer 
     */
    public function getXAs()
    {
        return $this->x_as;
    }

    /**
     * Set y_as
     *
     * @param integer $yAs
     * @return PeeringMatrix
     */
    public function setYAs($yAs)
    {
        $this->y_as = $yAs;
    
        return $this;
    }

    /**
     * Get y_as
     *
     * @return integer 
     */
    public function getYAs()
    {
        return $this->y_as;
    }

    /**
     * Set peering_status
     *
     * @param string $peeringStatus
     * @return PeeringMatrix
     */
    public function setPeeringStatus($peeringStatus)
    {
        $this->peering_status = $peeringStatus;
    
        return $this;
    }

    /**
     * Get peering_status
     *
     * @return string 
     */
    public function getPeeringStatus()
    {
        return $this->peering_status;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return PeeringMatrix
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
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
     * Set XCustomer
     *
     * @param Entities\Customer $xCustomer
     * @return PeeringMatrix
     */
    public function setXCustomer(\Entities\Customer $xCustomer = null)
    {
        $this->XCustomer = $xCustomer;
    
        return $this;
    }

    /**
     * Get XCustomer
     *
     * @return Entities\Customer 
     */
    public function getXCustomer()
    {
        return $this->XCustomer;
    }

    /**
     * Set YCustomer
     *
     * @param Entities\Customer $yCustomer
     * @return PeeringMatrix
     */
    public function setYCustomer(\Entities\Customer $yCustomer = null)
    {
        $this->YCustomer = $yCustomer;
    
        return $this;
    }

    /**
     * Get YCustomer
     *
     * @return Entities\Customer 
     */
    public function getYCustomer()
    {
        return $this->YCustomer;
    }
    /**
     * @var integer $vlan
     */
    protected $vlan;


    /**
     * Set vlan
     *
     * @param integer $vlan
     * @return PeeringMatrix
     */
    public function setVlan($vlan)
    {
        $this->vlan = $vlan;
    
        return $this;
    }

    /**
     * Get vlan
     *
     * @return integer 
     */
    public function getVlan()
    {
        return $this->vlan;
    }
}
