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
    CoreInterface       as CoreInterfaceEntity,
    CoreLink            as CoreLinkEntity,
    PhysicalInterface   as PhysicalInterfaceEntity,
};

/**
 * CoreInterface
 */
class CoreInterface
{
    /**
     * @var integer
     */
    private $id;

    private $created_at;
    private $updated_at;

    /**
     * @var PhysicalInterfaceEntity
     */
    private $physicalInterface;

    /**
     * @var CoreLinkEntity
     */
    private $coreLink;

    /**
     * @var CoreLinkEntity
     */
    private $coreLink2;


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
     * Get Physical Interface
     *
     * @return PhysicalInterface
     */
    public function getPhysicalInterface(): PhysicalInterface
    {
        return $this->physicalInterface;
    }

    /**
     * Get Core Link A
     *
     * @return CoreLinkEntity
     */
    public function getCoreLinkA()
    {
        return $this->coreLink;
    }

    /**
     * Get CoreLink B
     *
     * @return CoreLinkEntity
     */
    public function getCoreLinkB()
    {
        return $this->coreLink2;
    }

    /**
     * Check which side has a core link linked
     *
     * @return CoreLinkEntity
     */
    public function getCoreLink(): CoreLinkEntity
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
     * @param PhysicalInterface $physicalInterface
     *
     * @return CoreInterface
     */
    public function setPhysicalInterface( PhysicalInterface $physicalInterface = null ): CoreInterfaceEntity
    {
        $this->physicalInterface = $physicalInterface;
        return $this;
    }

    /**
     * Set Core Link A
     *
     * @param CoreLinkEntity $coreLinkA
     *
     * @return CoreInterface
     */
    public function setCoreLinkA( CoreLinkEntity $coreLinkA = null ): CoreInterface
    {
        $this->coreLink = $coreLinkA;
        return $this;
    }

    /**
     * Set Core Link B
     *
     * @param CoreLinkEntity $coreLinkB
     *
     * @return CoreInterface
     */
    public function setCoreLinkB( CoreLink $coreLinkB = null ): CoreInterface
    {
        $this->coreLink2 = $coreLinkB;
        return $this;
    }

}

