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

use Carbon\Carbon;

/**
 * Layer2Address
 */
class Layer2Address {
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $mac;

    /**
     * @var \DateTime
     */
    private $firstseen;

    /**
     * @var \DateTime
     */
    private $lastseen;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \Entities\VlanInterface
     */
    private $vlanInterface;


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
     * Get mac
     *
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * Get mac formated with comma (xx:xx:xx:xx:xx:xx)
     *
     * @return string
     */
    public function getMacFormattedWithColons()
    {
        return wordwrap($this->mac, 2, ':',true);
    }

    /**
     * Get mac formated (xxxx.xxxx.xxxx)
     *
     * @return string
     */
    public function getMacFormattedWithDots()
    {
        return wordwrap($this->mac, 4, '.',true);
    }

    /**
     * Get mac formated (xx-xx-xx-xx-xx-xx)
     *
     * @return string
     */
    public function getMacFormattedWithDashes()
    {
        return wordwrap($this->mac, 2, '-',true);
    }


    /**
     * Get firstseen
     *
     * @return \DateTime
     */
    public function getFirstSeenAt()
    {
        return $this->firstseen;
    }

    /**
     * Get lastseen
     *
     * @return \DateTime
     */
    public function getLastSeenAt()
    {
        return $this->lastseen;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreatedAtFormated()
    {
        return $this->getCreatedAt();
    }

    /**
     * Get vlanInterface
     *
     * @return \Entities\VlanInterface
     */
    public function getVlanInterface()
    {
        return $this->vlanInterface;
    }

    /**
     * Get switch ports where this MAC is associated
     *
     * Returns a sorted array of unique "switchname :: switchport name" strings
     *
     * @return array
     */
    public function getSwitchPorts(): array {
        $sps = [];
        foreach( $this->getVlanInterface()->getVirtualInterface()->getPhysicalInterfaces() as $pi ) {
            /** @var PhysicalInterface $pi */
            $sp = $pi->getSwitchPort()->getSwitcher()->getName() . '::' . $pi->getSwitchPort()->getName();
            if( !in_array( $sp, $sps ) ) {
                $sps[] = $sp;
            }
        }
        asort( $sps, SORT_NATURAL );
        return $sps;
    }


    /**
     * Set mac
     *
     * @param string $mac
     *
     * @return Layer2Address
     */
    public function setMac( $mac ): Layer2Address
    {
        $this->mac = $mac;
        return $this;
    }

    /**
     * Set firstseen
     *
     * @param \DateTime $firstSeenAt
     *
     * @return Layer2Address
     */
    public function setFirstSeenAt( $firstSeenAt ): Layer2Address
    {
        $this->firstseen = $firstSeenAt;
        return $this;
    }

    /**
     * Set lastseen
     *
     * @param \DateTime $lastSeenAt
     *
     * @return Layer2Address
     */
    public function setLastSeenAt( $lastSeenAt ): Layer2Address
    {
        $this->lastseen = $lastSeenAt;
        return $this;
    }

    /**
     * Set created
     *
     * @param \DateTime $createdAt
     *
     * @return Layer2Address
     */
    public function setCreatedAt( $createdAt ): Layer2Address
    {
        $this->created = $createdAt;
        return $this;
    }

    /**
     * Set vlanInterface
     *
     * @param VlanInterface $vlanInterface
     *
     * @return Layer2Address
     */
    public function setVlanInterface(VlanInterface $vlanInterface = null): Layer2Address
    {
        $this->vlanInterface = $vlanInterface;
        return $this;
    }



    /**
     * Convert this object to an array
     *
     * @return array
     */
    public function toArray(){
        $a = [
            'id'                        => $this->getId(),
            'mac'                       => $this->getMac() ,
            'macFormattedWithColons'    => $this->getMacFormattedWithColons(),
            'macFormattedWithDots'      => $this->getMacFormattedWithDots(),
            'macFormattedWithDashes'    => $this->getMacFormattedWithDashes(),
            'vliId'                     => $this->getVlanInterface()->getId(),
            'createdAt'                 => $this->getCreatedAt(),
            'firstSeenAt'               => $this->getFirstSeenAt(),
            'lastSeenAt'                => $this->getLastSeenAt()
        ];

        return $a;
    }

    /**
     * Get layer@address as JSON-compatibale array
     * @return array
     */
    public function jsonArray( ): array {
        $a = $this->toArray();

        $a['createdAt']     = $a['createdAt']       ? Carbon::instance( $a['createdAt']     )->toIso8601String() : null;
        $a['firstSeenAt']   = $a['firstSeenAt']     ? Carbon::instance( $a['firstSeenAt']   )->toIso8601String() : null;
        $a['lastSeenAt']    = $a['lastSeenAt']      ? Carbon::instance( $a['lastSeenAt']    )->toIso8601String() : null;

        return $a;
    }

}

