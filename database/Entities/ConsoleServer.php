<?php

namespace Entities;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


use Doctrine\Common\Collections\{
    ArrayCollection,
    Collection
};

use Entities\{
    ConsoleServerConnection     as ConsoleServerConnectionEntity,
    Vendor                      as VendorEntity,
    Cabinet                     as CabinetEntity
};

/**
 * ConsoleServer
 */
class ConsoleServer
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $serial_number;

    /**
     * @var boolean
     */
    private $active = '1';

    /**
     * @var string
     */
    private $notes;

    private $created_at;
    private $updated_at;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Collection|ConsoleServerConnection[]
     */
    private $consoleServerConnections;

    /**
     * @var \Entities\Vendor
     */
    private $vendor;

    /**
     * @var \Entities\Cabinet
     */
    private $cabinet;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->consoleServerConnections = new ArrayCollection();
    }


    /**
     * Add ConsoleServerConnections
     *
     * @param ConsoleServerConnectionEntity $consoleServerConnection
     *
     * @return ConsoleServer
     *
     * @throws
     */
    public function addConsoleServerConnection( ConsoleServerConnectionEntity $consoleServerConnection )
    {
        $this->consoleServerConnections[] = $consoleServerConnection;

        return $this;
    }

    /**
     * Remove ConsoleServerConnections
     *
     * @param ConsoleServerConnectionEntity $consoleServerConnection
     *
     * @return void
     */
    public function removeConsoleServerConnection( ConsoleServerConnectionEntity $consoleServerConnection )
    {
        $this->consoleServerConnections->removeElement($consoleServerConnection);
    }

    /**
     * Get ConsoleServerConnections
     *
     * @return Collection|ConsoleServerConnection[]
     */
    public function getConsoleServerConnections()
    {
        return $this->consoleServerConnections;
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
     * Set name
     *
     * @param string $name
     * @return ConsoleServer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set hostname
     *
     * @param string $hostname
     * @return ConsoleServer
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get hostname
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set model
     *
     * @param string $model
     * @return ConsoleServer
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set serial number
     *
     * @param string $serial_number
     * @return ConsoleServer
     */
    public function setSerialNumber($serial_number)
    {
        $this->serial_number = $serial_number;

        return $this;
    }

    /**
     * Get serial number
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serial_number;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return ConsoleServer
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set note
     *
     * @param string $notes
     * @return ConsoleServer
     */
    public function setNote($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->notes;
    }

    /**
     * Set Vendor
     *
     * @param VendorEntity $vendor
     *
     * @return ConsoleServer
     */
    public function setVendor( VendorEntity $vendor = null )
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get Vendor
     *
     * @return \Entities\Vendor
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set Cabinet
     *
     * @param CabinetEntity $cabinet
     * @return ConsoleServer
     */
    public function setCabinet( CabinetEntity $cabinet = null )
    {
        $this->cabinet = $cabinet;

        return $this;
    }

    /**
     * Get Cabinet
     *
     * @return \Entities\Cabinet
     */
    public function getCabinet()
    {
        return $this->cabinet;
    }
}

