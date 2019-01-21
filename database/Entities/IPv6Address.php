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
 * Entities\IPv6Address
 */
class IPv6Address
{
    /**
     * @var string $address
     */
    protected $address;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\VlanInterface
     */
    protected $VlanInterface;

    /**
     * @var Entities\Vlan
     */
    protected $Vlan;


    /**
     * Set address
     *
     * @param string $address
     * @return IPv6Address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
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
     * Set VlanInterface
     *
     * @param Entities\VlanInterface $vlanInterface
     * @return IPv6Address
     */
    public function setVlanInterface(\Entities\VlanInterface $vlanInterface = null)
    {
        $this->VlanInterface = $vlanInterface;
    
        return $this;
    }

    /**
     * Get VlanInterface
     *
     * @return Entities\VlanInterface 
     */
    public function getVlanInterface()
    {
        return $this->VlanInterface;
    }

    /**
     * Set Vlan
     *
     * @param Entities\Vlan $vlan
     * @return IPv6Address
     */
    public function setVlan(\Entities\Vlan $vlan = null)
    {
        $this->Vlan = $vlan;
    
        return $this;
    }

    /**
     * Get Vlan
     *
     * @return Entities\Vlan 
     */
    public function getVlan()
    {
        return $this->Vlan;
    }
}
