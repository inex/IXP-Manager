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
 * Entities\MACAddress
 */
class MACAddress
{
    /**
     * @var \DateTime $firstseen
     */
    protected $firstseen;

    /**
     * @var \DateTime $lastseen
     */
    protected $lastseen;

    /**
     * @var string $mac
     */
    protected $mac;

    /**
     * @var integer $id
     */
    protected $id;

    private $created_at;
    private $updated_at;

    /**
     * @var Entities\VirtualInterface
     */
    protected $VirtualInterface;


    /**
     * Set firstseen
     *
     * @param \DateTime $firstseen
     * @return MACAddress
     */
    public function setFirstseen($firstseen)
    {
        $this->firstseen = $firstseen;

        return $this;
    }

    /**
     * Get firstseen
     *
     * @return \DateTime
     */
    public function getFirstseen()
    {
        return $this->firstseen;
    }

    /**
     * Set lastseen
     *
     * @param \DateTime $lastseen
     * @return MACAddress
     */
    public function setLastseen($lastseen)
    {
        $this->lastseen = $lastseen;

        return $this;
    }

    /**
     * Get lastseen
     *
     * @return \DateTime
     */
    public function getLastseen()
    {
        return $this->lastseen;
    }

    /**
     * Set mac
     *
     * @param string $mac
     * @return MACAddress
     */
    public function setMac($mac)
    {
        $this->mac = $mac;

        return $this;
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set VirtualInterface
     *
     * @param Entities\VirtualInterface $virtualInterface
     * @return MACAddress
     */
    public function setVirtualInterface(\Entities\VirtualInterface $virtualInterface = null)
    {
        $this->VirtualInterface = $virtualInterface;

        return $this;
    }

    /**
     * Get VirtualInterface
     *
     * @return Entities\VirtualInterface
     */
    public function getVirtualInterface()
    {
        return $this->VirtualInterface;
    }


    public function getMacFormattedWithColons() {
        return strtolower( implode( ':', str_split( $this->getMac(), 2 ) ) );
    }
}
