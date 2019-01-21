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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Entities\Cabinet
 */
class Cabinet
{
    /**
     * Constants to indicate whether 'u' positions count from top or bottom
     */
    const U_COUNTS_FROM_TOP    = 1;
    const U_COUNTS_FROM_BOTTOM = 2;

    /**
     * @var array Textual representations of where u's count from
     */
    public static $U_COUNTS_FROM = [
        self::U_COUNTS_FROM_TOP     => 'Top',
        self::U_COUNTS_FROM_BOTTOM  => 'Bottom',
    ];

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $colocation
     */
    protected $colocation;

    /**
     * @var integer $height
     */
    protected $height;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $notes
     */
    protected $notes;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var ArrayCollection
     */
    protected $Switches;

    /**
     * @var ArrayCollection
     */
    protected $consoleServers;


    /**
     * @var ArrayCollection
     */
    protected $CustomerEquipment;

    /**
     * @var Location
     */
    protected $Location;

    /**
     * @var int u_counts_from
     */
    protected $u_counts_from;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Switches = new ArrayCollection();
        $this->consoleServers = new ArrayCollection();
        $this->CustomerEquipment = new ArrayCollection();
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Cabinet
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
     * Set colocation
     *
     * @param string $colocation
     * @return Cabinet
     */
    public function setColocation($colocation)
    {
        $this->colocation = $colocation;
    
        return $this;
    }

    /**
     * Get colocation
     *
     * @return string 
     */
    public function getColocation()
    {
        return $this->colocation;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Cabinet
     */
    public function setHeight($height)
    {
        $this->height = $height;
    
        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Cabinet
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Cabinet
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    
        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
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
     * Get whether u's count from top or bottom
     * @return int
     */
    public function getUCountsFrom() {
        return $this->u_counts_from;
    }

    /**
     * Set whether u's count from top or bottom
     * @param int $u
     * @return Cabinet
     */
    public function setUCountsFrom( int $u ): Cabinet {
        assert( in_array( $u, array_keys( self::$U_COUNTS_FROM ) ) );
        $this->u_counts_from = $u;
        return $this;
    }

    /**
     * Resolve as text whether u's count from top or bottom
     * @return string
     */
    public function resolveUCountsFrom(): string {
        return $this->u_counts_from ? self::$U_COUNTS_FROM[ $this->u_counts_from ] : 'Unknown';
    }


    /**
     * Add Switches
     *
     * @param Switcher $switches
     * @return Cabinet
     */
    public function addSwitche(Switcher $switches)
    {
        $this->Switches[] = $switches;
    
        return $this;
    }

    /**
     * Remove Switches
     *
     * @param Switcher $switch
     */
    public function removeSwitche(Switcher $switch)
    {
        $this->Switches->removeElement($switch);
    }

    /**
     * Get Switches
     *
     * @return ArrayCollection
     */
    public function getSwitches()
    {
        return $this->Switches;
    }




    /**
     * Add consoleServer
     *
     * @param ConsoleServer $consoleServer
     * @return Cabinet
     */
    public function addConsoleServer(ConsoleServer $consoleServer)
    {
        $this->consoleServers[] = $consoleServer;

        return $this;
    }

    /**
     * Remove consoleServer
     *
     * @param ConsoleServer $consoleServer
     */
    public function removeConsoleServer(ConsoleServer $consoleServer)
    {
        $this->consoleServers->removeElement($consoleServer);
    }

    /**
     * Get consoleServers
     *
     * @return ArrayCollection
     */
    public function getConsoleServers()
    {
        return $this->consoleServers;
    }




    /**
     * Add CustomerEquipment
     *
     * @param CustomerEquipment $customerEquipment
     * @return Cabinet
     */
    public function addCustomerEquipment(CustomerEquipment $customerEquipment)
    {
        $this->CustomerEquipment[] = $customerEquipment;
    
        return $this;
    }

    /**
     * Remove CustomerEquipment
     *
     * @param CustomerEquipment $customerEquipment
     */
    public function removeCustomerEquipment(CustomerEquipment $customerEquipment)
    {
        $this->CustomerEquipment->removeElement($customerEquipment);
    }

    /**
     * Get CustomerEquipment
     *
     * @return ArrayCollection
     */
    public function getCustomerEquipment()
    {
        return $this->CustomerEquipment;
    }

    /**
     * Set Location
     *
     * @param Location $location
     * @return Cabinet
     */
    public function setLocation(Location $location = null)
    {
        $this->Location = $location;
    
        return $this;
    }

    /**
     * Get Location
     *
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->Location;
    }
    /**
     * @var string $cololocation
     */
    protected $cololocation;


    /**
     * Set cololocation
     *
     * @param string $cololocation
     * @return Cabinet
     */
    public function setCololocation($cololocation)
    {
        $this->cololocation = $cololocation;
    
        return $this;
    }

    /**
     * Get cololocation
     *
     * @return string 
     */
    public function getCololocation()
    {
        return $this->cololocation;
    }

    /**
     * Add Switches
     *
     * @param Switcher $switches
     * @return Cabinet
     */
    public function addSwitch(Switcher $switches)
    {
        $this->Switches[] = $switches;

        return $this;
    }

    /**
     * Remove Switches
     *
     * @param Switcher $switches
     */
    public function removeSwitch(Switcher $switches)
    {
        $this->Switches->removeElement($switches);
    }

    /**
     * @var Collection
     */
    private $patchPanels;

    /**
     * Add patchPanel
     *
     * @param PatchPanel $patchPanel
     *
     * @return Cabinet
     */
    public function addPatchPanel(PatchPanel $patchPanel)
    {
        $this->patchPanels[] = $patchPanel;

        return $this;
    }

    /**
     * Remove patchPanel
     *
     * @param \Entities\PatchPanel $patchPanel
     */
    public function removePatchPanel(PatchPanel $patchPanel)
    {
        $this->patchPanels->removeElement($patchPanel);
    }

    /**
     * Get patchPanels
     *
     * @return Collection
     */
    public function getPatchPanels()
    {
        return $this->patchPanels;
    }
}
