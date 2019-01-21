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
 * CoreInterface
 */
class CoreInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\PhysicalInterface
     */
    private $physicalInterface;

    /**
     * @var \Entities\CoreLink
     */
    private $coreLink;

    /**
     * @var \Entities\CoreLink
     */
    private $coreLink2;


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
     * Get CoreBundle
     *
     * @return \Entities\PhysicalInterface
     */
    public function getPhysicalInterface()
    {
        return $this->physicalInterface;
    }

    /**
     * Get Core Link A
     *
     * @return \Entities\CoreLink
     */
    public function getCoreLinkA()
    {
        return $this->coreLink;
    }

    /**
     * Get CoreLink B
     *
     * @return \Entities\CoreLink
     */
    public function getCoreLinkB()
    {
        return $this->coreLink2;
    }

    /**
     * Check which side has a core link linked
     *
     * @return \Entities\CoreLink
     */
    public function getCoreLink()
    {
        if( $this->getCoreLinkA() ){
            return $this->getCoreLinkA();
        } else {
            return $this->getCoreLinkB();
        }
    }

    /**
     * Set Physical Interface
     *
     * @param \Entities\PhysicalInterface $physicalInterface
     *
     * @return CoreInterface
     */
    public function setPhysicalInterface( PhysicalInterface $physicalInterface = null )
    {
        $this->physicalInterface = $physicalInterface;
        return $this;
    }

    /**
     * Set Core Link A
     *
     * @param \Entities\CoreLink $coreLinkA
     *
     * @return CoreInterface
     */
    public function setCoreLinkA( CoreLink $coreLinkA = null )
    {
        $this->coreLink = $coreLinkA;
        return $this;
    }

    /**
     * Set Core Link B
     *
     * @param \Entities\CoreLink $coreLinkB
     *
     * @return CoreInterface
     */
    public function setCoreLinkB( CoreLink $coreLinkB = null )
    {
        $this->coreLink2 = $coreLinkB;
        return $this;
    }

}

