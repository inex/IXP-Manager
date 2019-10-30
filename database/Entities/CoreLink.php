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
    CoreBundle      as CoreBundleEntity,
    CoreInterface   as CoreInterfaceEntity,
    CoreLink        as CoreLinkEntity,
};

/**
 * CoreLink
 */
class CoreLink
{
    /**
     * @var boolean
     */
    private $bfd = '0';

    /**
     * @var boolean
     */
    private $enabled = '0';

    /**
     * @var string
     */
    private $ipv4_subnet;

    /**
     * @var string
     */
    private $ipv6_subnet;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var CoreInterfaceEntity
     */
    private $coreInterfaceSideA;

    /**
     * @var CoreInterfaceEntity
     */
    private $coreInterfaceSideB;

    /**
     * @var CoreBundleEntity
     */
    private $coreBundle;


    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get bfd
     *
     * @return bool
     */
    public function getBFD(): bool
    {
        return $this->bfd;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get IPv4 Subnet
     *
     * @return string
     */
    public function getIPv4Subnet()
    {
        return $this->ipv4_subnet;
    }

    /**
     * Get IPv6 Subnet
     *
     * @return string
     */
    public function getIPv6Subnet()
    {
        return $this->ipv6_subnet;
    }


    /**
     * Get CoreInterface side A
     *
     * @return CoreInterfaceEntity
     */
    public function getCoreInterfaceSideA(): CoreInterfaceEntity
    {
        return $this->coreInterfaceSideA;
    }

    /**
     * Get CoreInterface side B
     *
     * @return CoreInterfaceEntity
     */
    public function getCoreInterfaceSideB(): CoreInterfaceEntity
    {
        return $this->coreInterfaceSideB;
    }

    /**
     * Get the core interface (A/B)
     *
     * @return array
     */
    public function getCoreInterfaces(): array
    {
        return [ $this->getCoreInterfaceSideA(), $this->getCoreInterfaceSideB() ];
    }

    /**
     * Get CoreBundle
     *
     * @return CoreBundleEntity
     */
    public function getCoreBundle()
    {
        return $this->coreBundle;
    }



    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return CoreLinkEntity
     */
    public function setEnabled( $enabled )
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Set BFD
     *
     * @param boolean $bfd
     *
     * @return CoreLinkEntity
     */
    public function setBFD( $bfd )
    {
        $this->bfd = $bfd;
        return $this;
    }

    /**
     * Set IPv4 Subnet
     *
     * @param string $ipv4_subnet
     *
     * @return CoreLinkEntity
     */
    public function setIPv4Subnet( $ipv4_subnet )
    {
        $this->ipv4_subnet = $ipv4_subnet;
        return $this;
    }

    /**
     * Set IPv6 Subnet
     *
     * @param string $ipv6_subnet
     *
     * @return CoreLinkEntity
     */
    public function setIPv6Subnet( $ipv6_subnet )
    {
        $this->ipv6_subnet = $ipv6_subnet;
        return $this;
    }


    /**
     * Set CoreInterface side A
     *
     * @param CoreInterfaceEntity $coreInterfaceSideA
     *
     * @return CoreLink
     */
    public function setCoreInterfaceSideA( CoreInterfaceEntity $coreInterfaceSideA = null)
    {
        $this->coreInterfaceSideA = $coreInterfaceSideA;
        return $this;
    }

    /**
     * Set CoreInterface side B
     *
     * @param CoreInterfaceEntity $coreInterfaceSideB
     *
     * @return CoreLink
     */
    public function setCoreInterfaceSideB( CoreInterfaceEntity $coreInterfaceSideB = null )
    {
        $this->coreInterfaceSideB = $coreInterfaceSideB;
        return $this;
    }

    /**
     * Set CoreBundle
     *
     * @param CoreBundleEntity $coreBundle
     *
     * @return CoreLink
     */
    public function setCoreBundle( CoreBundleEntity $coreBundle = null )
    {
        $this->coreBundle = $coreBundle;
        return $this;
    }

}

