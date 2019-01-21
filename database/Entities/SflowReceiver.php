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

/**
 * SflowReceiver
 */
class SflowReceiver
{
    /**
     * @var string
     */
    private $dst_ip;

    /**
     * @var integer
     */
    private $dst_port;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\VirtualInterface
     */
    private $VirtualInterface;


    /**
     * Set dstIp
     *
     * @param string $dstIp
     *
     * @return SflowReceiver
     */
    public function setDstIp($dstIp)
    {
        $this->dst_ip = $dstIp;

        return $this;
    }

    /**
     * Get dstIp
     *
     * @return string
     */
    public function getDstIp()
    {
        return $this->dst_ip;
    }

    /**
     * Set dstPort
     *
     * @param integer $dstPort
     *
     * @return SflowReceiver
     */
    public function setDstPort($dstPort)
    {
        $this->dst_port = $dstPort;

        return $this;
    }

    /**
     * Get dstPort
     *
     * @return integer
     */
    public function getDstPort()
    {
        return $this->dst_port;
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
     * Set virtualInterface
     *
     * @param \Entities\VirtualInterface $virtualInterface
     *
     * @return SflowReceiver
     */
    public function setVirtualInterface(\Entities\VirtualInterface $virtualInterface = null)
    {
        $this->VirtualInterface = $virtualInterface;

        return $this;
    }

    /**
     * Get virtualInterface
     *
     * @return \Entities\VirtualInterface
     */
    public function getVirtualInterface()
    {
        return $this->VirtualInterface;
    }

    public function getCustomer()
    {
        return $this->getVirtualInterface() ? $this->getVirtualInterface()->getCustomer() : null;
    }


}
