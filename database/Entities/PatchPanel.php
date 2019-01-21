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

use D2EM;
use Doctrine\Common\Collections\ArrayCollection;

use Entities\{
    PatchPanelPort              as PatchPanelPortEntity
};

/**
 * Entities\PatchPanel
 */
class PatchPanel
{

    /**
     * CONST Cable types
     */
    const CABLE_TYPE_UTP                = 1;
    const CABLE_TYPE_SMF                = 2;
    const CABLE_TYPE_MMF                = 3;
    const CABLE_TYPE_OTHER              = 999;

    /**
     * Array Cable types
     */
    public static $CABLE_TYPES = [
        self::CABLE_TYPE_UTP            => 'UTP',
        self::CABLE_TYPE_SMF            => 'SMF',
        self::CABLE_TYPE_MMF            => 'MMF',
        self::CABLE_TYPE_OTHER          => 'Other',
    ];


    /**
     * CONST Connector types
     */
    const CONNECTOR_TYPE_RJ45           = 1;
    const CONNECTOR_TYPE_SC             = 2;
    const CONNECTOR_TYPE_LC             = 3;
    const CONNECTOR_TYPE_MU             = 4;
    const CONNECTOR_TYPE_OTHER          = 999;

    /**
     * Array Connector types
     */
    public static $CONNECTOR_TYPES = [
        self::CONNECTOR_TYPE_RJ45      => 'RJ45',
        self::CONNECTOR_TYPE_SC        => 'SC',
        self::CONNECTOR_TYPE_LC        => 'LC',
        self::CONNECTOR_TYPE_MU        => 'MU',
        self::CONNECTOR_TYPE_OTHER     => 'Other',
    ];

    /**
     * Counts from patch panel mount position
     */
    const MOUNTED_AT_FRONT = 1;
    const MOUNTED_AT_REAR  = 2;

    /**
     * Mounted at textual representations
     */
    public static $MOUNTED_AT = [
        self::MOUNTED_AT_FRONT => 'Front',
        self::MOUNTED_AT_REAR  => 'Rear',
    ];

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $colo_reference;

    /**
     * @var integer
     */
    private $cable_type;

    /**
     * @var integer
     */
    private $connector_type;

    /**
     * @var \DateTime
     */
    private $installation_date;

    /**
     * @var string
     */
    private $port_prefix = '';

    /**
     * @var boolean $active
     */
    private $active = true;

    /**
     * @var string $location_notes
     */
    private $location_notes = '';


    /**
     * @var int
     */
    private $chargeable = PatchPanelPort::CHARGEABLE_NO;

    /**
     * @var int
     */
    private $mounted_at;

    /**
     * @var int
     */
    private $u_position;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $patchPanelPorts;

    /**
     * @var \Entities\Cabinet
     */
    private $cabinet;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->patchPanelPorts = new ArrayCollection();
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get coloReference
     *
     * @return string
     */
    public function getColoReference()
    {
        return $this->colo_reference;
    }

    /**
     * Get location notes
     *
     * @return string
     */
    public function getLocationNotes()
    {
        return $this->location_notes;
    }


    /**
     * Get cableType
     *
     * @return integer
     */
    public function getCableType()
    {
        return $this->cable_type;
    }

    /**
     * Get connectorType
     *
     * @return integer
     */
    public function getConnectorType()
    {
        return $this->connector_type;
    }

    /**
     * Get mounted_at
     *
     * @return integer
     */
    public function getMountedAt()
    {
        return $this->mounted_at;
    }

    /**
     * Get u position
     *
     * @return integer
     */
    public function getUPosition()
    {
        return $this->u_position;
    }

    /**
     * Get installationDate
     *
     * @return \DateTime
     */
    public function getInstallationDate()
    {
        return $this->installation_date;
    }

    /**
     * Get port prefix
     *
     * @return string
     */
    public function getPortPrefix()
    {
        return $this->port_prefix;
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
     * Get cabinet
     *
     * @return \Entities\Cabinet
     */
    public function getCabinet()
    {
        return $this->cabinet;
    }

    /**
     * Get patchPanelPorts
     *
     * @return \Doctrine\Common\Collections\Collection|\Entities\PatchPanelPort[]
     */
    public function getPatchPanelPorts()
    {
        return $this->patchPanelPorts;
    }




    /**
     * Set name
     *
     * @param string $name
     *
     * @return PatchPanel
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set coloReference
     *
     * @param string $coloReference
     *
     * @return PatchPanel
     */
    public function setColoReference($coloReference)
    {
        $this->colo_reference = $coloReference;

        return $this;
    }

    /**
     * Set location notes
     *
     * @param string $location_notes
     *
     * @return PatchPanel
     */
    public function setLocationNotes(string $location_notes)
    {
        $this->location_notes = $location_notes;

        return $this;
    }


    /**
     * Set cableType
     *
     * @param integer $cableType
     *
     * @return PatchPanel
     */
    public function setCableType($cableType)
    {
        $this->cable_type = $cableType;

        return $this;
    }

    /**
     * Set connectorType
     *
     * @param integer $connectorType
     *
     * @return PatchPanel
     */
    public function setConnectorType($connectorType)
    {
        $this->connector_type = $connectorType;

        return $this;
    }

    /**
     * Set mounted at
     *
     * @param integer $ma
     *
     * @return PatchPanel
     */
    public function setMountedAt(int $ma): PatchPanel
    {
        $this->mounted_at = $ma;

        return $this;
    }

    /**
     * Set u position
     *
     * @param integer $up
     *
     * @return PatchPanel
     */
    public function setUPosition(int $up): PatchPanel
    {
        $this->u_position = $up;

        return $this;
    }

    /**
     * Set installationDate
     *
     * @param \DateTime $installationDate
     *
     * @return PatchPanel
     */
    public function setInstallationDate($installationDate)
    {
        $this->installation_date = $installationDate;

        return $this;
    }

    /**
     * Set port prefix
     *
     * @param string $port_prefix
     *
     * @return PatchPanel
     */
    public function setPortPrefix($port_prefix)
    {
        $this->port_prefix = $port_prefix;
        return $this;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return PatchPanel
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Set chargeable
     *
     * @param int $chargeable
     *
     * @return PatchPanel
     */
    public function setChargeable(int $chargeable)
    {
        $this->chargeable = $chargeable;
        return $this;
    }

    /**
     * Get chargeable
     *
     * @return int
     */
    public function getChargeable(): int
    {
        return $this->chargeable;
    }

    /**
     * Set cabinet
     *
     * @param Cabinet $cabinet
     *
     * @return PatchPanel
     */
    public function setCabinet(Cabinet $cabinet = null)
    {
        $this->cabinet = $cabinet;

        return $this;
    }



    /**
     * Add patchPanelPort
     *
     * @param PatchPanelPort $patchPanelPort
     *
     * @return PatchPanel
     */
    public function addPatchPanelPort(PatchPanelPort $patchPanelPort)
    {
        $this->patchPanelPorts[] = $patchPanelPort;

        return $this;
    }

    /**
     * Remove patchPanelPort
     *
     * @param PatchPanelPort $patchPanelPort
     */
    public function removePatchPanelPort(PatchPanelPort $patchPanelPort)
    {
        $this->patchPanelPorts->removeElement($patchPanelPort);
    }

    /**
     * Check if all ports on a patch panel are available.
     *
     * @return boolean
     */
    public function areAllPortsAvailable() {
        return $this->getPortCount() == $this->getAvailableForUsePortCount();
    }

    /**
     * Turn the database integer representation of the cable type into text as
     * defined in the self::$CABLE_TYPES array (or 'Unknown')
     * @return string
     */
    public function resolveCableType(): string {
        return self::$CABLE_TYPES[ $this->getCableType() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the connector type into text as
     * defined in the self::$CONNECTOR_TYPES array (or 'Unknown')
     * @return string
     */
    public function resolveConnectorType(): string {
        return self::$CONNECTOR_TYPES[ $this->getConnectorType() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the PatchPanelPort::$CHARGEABLES array (or 'Unknown')
     * @return string
     */
    public function resolveChargeable(): string {
        return PatchPanelPort::$CHARGEABLES[ $this->getChargeable() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the PatchPanelPort::$CHARGEABLES array (or 'Unknown')
     * @return string
     */
    public function resolveMountedAt(): string {
        return self::$MOUNTED_AT[ $this->getMountedAt() ] ?? 'Unknown';
    }

    /**
     * Get number of patch panel ports
     *
     * @return int
     */
    public function getPortCount(): int {
        return count( $this->patchPanelPorts );
    }

    /**
     * Get number of patch panel ports
     *
     * @return int
     */
    public function getAvailableForUsePortCount(): int {
        $cnt = 0;
        foreach( $this->getPatchPanelPorts() as $ppp ) {

            if( $ppp->getDuplexMasterPort() ) {
                if( $ppp->getDuplexMasterPort()->isAvailableForUse() ) {
                    $cnt++;
                }
            } else if( $ppp->isAvailableForUse() ) {
                $cnt++;
            }

        }

        return $cnt;
    }

    /**
     * get the css class used to display the value => available ports / total ports
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @return string
     */
    public function getCssClassPortCount(){
        $total = $this->getPortCount();
        $available = $this->getAvailableForUsePortCount();
        if($total != 0):
            if( ($total - $available) / $total < 0.7 ):
                $class = "success";
            elseif( ($total - $available ) / $total < 0.85 ):
                $class = "warning";
            else:
                $class = "danger";
            endif;
        else:
            $class = "danger";
        endif;

        return $class;
    }

    /**
    * get the value availble port / total port
    *
    *
    * @param  bool $divide if the value need to be divide by 2 (use when some patch panel ports have duplex port)
    * @return string
    */
    public function getAvailableOnTotalPort($divide = false){
        $available = ($divide)? floor( $this->getAvailableForUsePortCount() / 2 ) :$this->getAvailableForUsePortCount();
        $total     = ($divide)? floor( $this->getPortCount() / 2 ) :$this->getPortCount();

        return $available.' / '.$total;
    }

    /**
     * Does this patch panel have any duplex ports?
     *
     * @return bool
     */
    public function hasDuplexPort(): bool {
        foreach( $this->patchPanelPorts as $ppp ) {
            if( $ppp->isDuplexPort() ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Convert this object to an array
     *
     * @return array
     */
    public function toArray(): array {
        return [
            'id'               => $this->getId(),
            'cabinetId'        => $this->getCabinet() ? $this->getCabinet()->getId() : null,
            'name'             => $this->getName(),
            'coloRef'          => $this->getColoReference(),
            'cableTypeId'      => $this->getCableType(),
            'cableType'        => $this->resolveCableType(),
            'connectorTypeId'  => $this->getConnectorType(),
            'connectorType'    => $this->resolveConnectorType(),
            'active'           => $this->getActive(),
        ];
    }

    /**
     * Create patch panel ports for a patch panel
     *
     * @param  int $n the number of port needed
     * @return PatchPanel
     */
    public function createPorts( int $n ): PatchPanel {
        // what's the current maximum port number?
        // (we need this to add new ones to the end)
        $max = 0;

        foreach( $this->getPatchPanelPorts() as $port ) {
            if( $port->getNumber() > $max ) {
                $max = $port->getNumber();
            }
        }
        $max++;

        for( $i = 0; $i < $n; $i++ ) {
            $ppp = new PatchPanelPort;
            $ppp->setNumber( ( $max + $i ) );
            $ppp->setState( PatchPanelPort::STATE_AVAILABLE );
            $ppp->setPatchPanel( $this );
            $ppp->setChargeable( $this->getChargeable() );
            $ppp->setLastStateChange( new \DateTime );
            $this->addPatchPanelPort($ppp);
            D2EM::persist($ppp);
        }
        return $this;
    }


    /**
     * A descriptive position of the patch panel in the rack
     * @return string
     */
    public function getLocationDescription(): string {
        $loc = '';

        if( $this->getUPosition() ) {
            $loc .= 'Located at U' . $this->getUPosition();

            if( $cf = $this->getCabinet()->getUCountsFrom() ) {
                $loc .= ' (counting from the ' . strtolower( Cabinet::$U_COUNTS_FROM[ $cf ] ) . ')';
            }

            if( $ma = $this->getMountedAt() ) {
                $loc .= ' at the ' . strtolower( self::$MOUNTED_AT[$ma] ) . ' of the cabinet';
            }

            $loc .= '.';
        }

        return $loc;
    }
}

