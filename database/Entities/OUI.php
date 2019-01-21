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
 * OUI
 */
class OUI
{
    /**
     * @var string
     */
    private $oui;

    /**
     * @var string
     */
    private $organisation;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set oui
     *
     * @param string $oui
     * @return OUI
     */
    public function setOui($oui)
    {
        $this->oui = $oui;
    
        return $this;
    }

    /**
     * Get oui
     *
     * @return string 
     */
    public function getOui()
    {
        return $this->oui;
    }

    /**
     * Set organisation
     *
     * @param string $organisation
     * @return OUI
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    
        return $this;
    }

    /**
     * Get organisation
     *
     * @return string 
     */
    public function getOrganisation()
    {
        return $this->organisation;
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
}
