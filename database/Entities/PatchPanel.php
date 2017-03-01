<?php

namespace Entities;

/**
 * PatchPanel
 */
class PatchPanel
{

    /**
     * CONST Cable types
     */
    const CABLE_TYPE_UTP        = 1;
    const CABLE_TYPE_SMF        = 2;
    const CABLE_TYPE_MMF        = 3;
    const CABLE_TYPE_OTHER      = 999;

    /**
     * Array Cable types
     */
    public static $CABLE_TYPES = [
        self::CABLE_TYPE_UTP        => 'UTP',
        self::CABLE_TYPE_SMF        => 'SMF',
        self::CABLE_TYPE_MMF        => 'MMF',
        self::CABLE_TYPE_OTHER      => 'Other',
    ];


    /**
     * CONST Connector types
     */
    const CONNECTOR_TYPE_RJ45      = 1;
    const CONNECTOR_TYPE_SC        = 2;
    const CONNECTOR_TYPE_LC        = 3;
    const CONNECTOR_TYPE_MU        = 4;
    const CONNECTOR_TYPE_OTHER     = 999;

    /**
     * Array Connector types
     */
    public static $CONNECTOR_TYPES = [
        self::CONNECTOR_TYPE_RJ45        => 'RJ45',
        self::CONNECTOR_TYPE_SC        => 'SC',
        self::CONNECTOR_TYPE_LC        => 'LC',
        self::CONNECTOR_TYPE_MU        => 'MU',
        self::CONNECTOR_TYPE_OTHER        => 'Other',
    ];

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
     * @var integer
     */
    private $id;

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
        $this->patchPanelPorts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Get coloReference
     *
     * @return string
     */
    public function getColoReference()
    {
        return $this->colo_reference;
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
     * Get cableType
     *
     * @return integer
     */
    public function getCableType()
    {
        return $this->cable_type;
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
     * Get connectorType
     *
     * @return integer
     */
    public function getConnectorType()
    {
        return $this->connector_type;
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
     * Get installationDate
     *
     * @return \DateTime
     */
    public function getInstallationDate()
    {
        return $this->installation_date;
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
     * Add patchPanelPort
     *
     * @param \Entities\PatchPanelPort $patchPanelPort
     *
     * @return PatchPanel
     */
    public function addPatchPanelPort(\Entities\PatchPanelPort $patchPanelPort)
    {
        $this->patchPanelPorts[] = $patchPanelPort;

        return $this;
    }

    /**
     * Remove patchPanelPort
     *
     * @param \Entities\PatchPanelPort $patchPanelPort
     */
    public function removePatchPanelPort(\Entities\PatchPanelPort $patchPanelPort)
    {
        $this->patchPanelPorts->removeElement($patchPanelPort);
    }

    /**
     * Get patchPanelPorts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPatchPanelPorts()
    {
        return $this->patchPanelPorts;
    }

    /**
     * Set cabinet
     *
     * @param \Entities\Cabinet $cabinet
     *
     * @return PatchPanel
     */
    public function setCabinet(\Entities\Cabinet $cabinet = null)
    {
        $this->cabinet = $cabinet;

        return $this;
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
}
