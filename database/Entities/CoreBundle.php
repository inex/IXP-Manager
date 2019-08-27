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

use Doctrine\Common\Collections\{
    ArrayCollection,
    Collection
};

use Entities\{
    CoreBundle          as CoreBundleEntity,
    CoreLink            as CoreLinkEntity,
    Customer            as CustomerEntity,
    Switcher            as SwitcherEntity
};

/**
 * CoreBundle
 */
class CoreBundle
{

    /**
     * CONST TYPES
     */
    const TYPE_ECMP              = 1;
    const TYPE_L2_LAG            = 2;
    const TYPE_L3_LAG            = 3;

    /**
     * Array STATES
     */
    public static $TYPES = [
        self::TYPE_ECMP          => "ECMP",
        self::TYPE_L2_LAG        => "L2-LAG (e.g. LACP)",
        self::TYPE_L3_LAG        => "L3-LAG",
    ];

    /**
     * @var string
     */
    private $description;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $graph_title;

    /**
     * @var boolean
     */
    private $stp = false;

    /**
     * @var boolean
     */
    private $enabled = false;

    /**
     * @var boolean
     */
    private $bfd = '0';

    /**
     * @var string
     */
    private $ipv4_subnet;

    /**
     * @var string
     */
    private $ipv6_subnet;

    /**
     * @var int
     */
    private $cost;

    /**
     * @var int
     */
    private $preference;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Collection
     */
    private $coreLinks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->coreLinks = new ArrayCollection();
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
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
    * Is the type TYPE_ECMP?
    *
    * @return bool
    */
    public function isTypeECMP(): bool {
        return $this->getType() === self::TYPE_ECMP;
    }

    /**
    * Is the type isTypeL2Lag?
    *
    * @return bool
    */
    public function isTypeL2Lag(): bool {
        return $this->getType() === self::TYPE_L2_LAG;
    }

    /**
     * Is the type isTypeL3Lag?
     *
     * @return bool
     */
    public function isTypeL3Lag(): bool {
        return $this->getType() === self::TYPE_L3_LAG;
    }

    /**
     * Get graph title
     *
     * @return string
     */
    public function getGraphTitle()
    {
        return $this->graph_title;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get STP
     *
     * @return boolean
     */
    public function getSTP(): bool
    {
        return $this->stp;
    }

    /**
     * Get bfd
     *
     * @return boolean
     */
    public function getBFD()
    {
        return $this->bfd;
    }

    /**
     * Get IPv4 Subnet
     *
     * @return boolean
     */
    public function getIPv4Subnet()
    {
        return $this->ipv4_subnet;
    }

    /**
     * Get IPv6 Subnet
     *
     * @return boolean
     */
    public function getIPv46Subnet()
    {
        return $this->ipv6_subnet;
    }

    /**
     * Get cost
     *
     * @return boolean
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Get preference
     *
     * @return boolean
     */
    public function getPreference()
    {
        return $this->preference;
    }


    /**
     * Get CoreLinks
     *
     * @return Collection
     */
    public function getCoreLinks()
    {
        return $this->coreLinks;
    }

    /**
     * Add core Link
     *
     * @param CoreLink $coreLink
     *
     * @return CoreBundleEntity
     */
    public function addCoreLink( CoreLink $coreLink)
    {
        $this->coreLinks[] = $coreLink;

        return $this;
    }

    /**
     * Remove patchPanelPort
     *
     * @param CoreLink $coreLink
     */
    public function removeCoreLink( CoreLink $coreLink)
    {
        $this->coreLinks->removeElement( $coreLink );
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES array (or 'Unknown')
     * @return string
     */
    public function resolveType(): string {
        return self::$TYPES[ $this->getType() ] ?? 'Unknown';
    }




    /**
     * Set description
     *
     * @param string $description
     *
     * @return CoreBundle
     */
    public function setDescription( $description )
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return CoreBundle
     */
    public function setType( $type )
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set graph title
     *
     * @param string $graph_title
     *
     * @return CoreBundle
     */
    public function setGraphTitle( $graph_title )
    {
        $this->graph_title = $graph_title;
        return $this;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return CoreBundle
     */
    public function setEnabled( bool $enabled )
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Set STP
     *
     * @param boolean $stp
     *
     * @return CoreBundle
     */
    public function setSTP( bool $stp )
    {
        $this->stp = $stp;
        return $this;
    }

    /**
     * Set BFD
     *
     * @param boolean $bfd
     *
     * @return CoreBundle
     */
    public function setBFD( bool $bfd )
    {
        $this->bfd = $bfd;
        return $this;
    }

    /**
     * Set IPv4 Subnet
     *
     * @param string $ipv4_subnet
     *
     * @return CoreBundle
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
     * @return CoreBundle
     */
    public function setIPv6Subnet( $ipv6_subnet )
    {
        $this->ipv6_subnet = $ipv6_subnet;
        return $this;
    }

    /**
     * Set cost
     *
     * @param integer $cost
     *
     * @return CoreBundle
     */
    public function setCost( $cost )
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * Set preference
     *
     * @param integer $preference
     *
     * @return CoreBundle
     */
    public function setPreference( $preference )
    {
        $this->preference = $preference;
        return $this;
    }


    /**
     * Check if all the core links for the core bundle are enabled
     *
     * @return boolean
     */
    public function doAllCoreLinksEnabled( ): bool
    {
        foreach( $this->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            if( !$cl->getEnabled() ){
                return false;
            }
        }

        return true;
    }

    /**
     * get all core links enabled for a bundle core
     *
     * @return array
     */
    public function getCoreLinksEnabled( ): array
    {
        $cls = [];
        foreach( $this->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            if( $cl->getEnabled() ){
                $cls[] = $cl;
            }
        }

        return $cls;
    }

    /**
     * get switch from side A or B
     *
     * @param bool $sideA if true get the side A if false Side B
     *
     * @return SwitcherEntity|bool
     */
    public function getSwitchSideX( bool $sideA = true )
    {
        foreach( $this->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            $side = $sideA ? $cl->getCoreInterfaceSideA() : $cl->getCoreInterfaceSideB() ;

            return $side->getPhysicalInterface()->getSwitchPort()->getSwitcher();
        }

        return false;
    }

    /**
     * get the speed of the Physical interface
     *
     * @return int
     */
    public function getSpeedPi()
    {
        foreach( $this->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            return $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getSpeed();
        }

        return 0;
    }

    /**
     * get the duplex of the Physical interface
     *
     * @return int|false
     */
    public function getDuplexPi()
    {
        foreach( $this->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            return $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getDuplex();
        }

        return false;
    }

    /**
     * get the auto neg of the Physical interface
     *
     * @return int|false
     */
    public function getAutoNegPi()
    {
        foreach( $this->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            return $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getAutoneg();
        }

        return false;
    }

    /**
     * get the customer associated virtual interface of the core bundle
     *
     * @return CustomerEntity|false
     */
    public function getCustomer(): CustomerEntity
    {
        foreach( $this->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            return $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getVirtualInterface()->getCustomer();
        }

        return false;
    }


    /**
     * get the virtual interfaces linked to the core links of the side A and B
     *
     * @return array
     */
    public function getVirtualInterfaces(): array
    {
        $vis = [];

        foreach( $this->getCoreLinks() as $cl ){
            $vis[ 'A' ] = $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getVirtualInterface();
            $vis[ 'B' ] = $cl->getCoreInterfaceSideB()->getPhysicalInterface()->getVirtualInterface();
            return $vis;
        }

        return $vis;
    }

    /**
     * Is this core bundle has type ECMP ?
     *
     * @return bool
     */
    public function isECMP(): bool {
        return $this->getType() == self::TYPE_ECMP;
    }

    /**
     * Is this core bundle has type TYPE_L2_LAG ?
     *
     * @return bool
     */
    public function isL2LAG(): bool
    {
        return $this->getType() == self::TYPE_L2_LAG;
    }

    /**
     * Is this core bundle has type TYPE_L3_LAG ?
     *
     * @return bool
     */
    public function isL3LAG(): bool {
        return $this->getType() == self::TYPE_L3_LAG;
    }

    /**
     * Check if the switch is the same for the Physical interfaces of the core links associated to the core bundle
     *
     * @param bool $sideA if true get the side A if false Side B
     *
     * @return bool
     */
    public function sameSwitchForEachPIFromCL( bool $sideA = true ): bool
    {
        $switches = [];

        foreach( $this->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            $side = $sideA ? $cl->getCoreInterfaceSideA() : $cl->getCoreInterfaceSideB() ;

            $switches[] = $side->getPhysicalInterface()->getSwitchPort()->getSwitcher()->getId();
        }

        return ( count( array_unique( $switches ) ) == 1 ) ? true : false;
    }
}

