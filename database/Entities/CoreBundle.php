<?php

namespace Entities;

/**
 * CoreBundle
 */
class CoreBundle
{

    /**
     * CONST TYPES
     */
    const TYPE_ECMP              = 1;
    const TYPE_L2_LAG            = 2;
    const TYPE_L3_LAG            = 3;
    const TYPE_OTHER             = 4;



    /**
     * Array STATES
     */
    public static $TYPES = [
        self::TYPE_ECMP          => "ECMP",
        self::TYPE_L2_LAG        => "L2-LAG (e.g. LACP)",
        self::TYPE_L3_LAG        => "L3-LAG",
        self::TYPE_OTHER         => "Other",
    ];
    /**
     * @var string
     */
    private $description;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $graph_title;

    /**
     * @var boolean
     */
    private $enabled = '0';

    /**
     * @var boolean
     */
    private $bfd = '0';

    /**
     * @var string
     */
    private $ipv4_subnet;

    /**
     * @var string
     */
    private $ipv6_subnet;

    /**
     * @var int
     */
    private $cost;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $coreLinks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->coreLinks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get type
     *
     * @return interger
     */
    public function getType()
    {
        return $this->type;
    }

    /**
    * Is the type TYPE_ECMP?
    *
    * @return bool
    */
    public function isTypeECMP(): bool {
        return $this->getType() === self::TYPE_ECMP;
    }

    /**
    * Is the type isTypeL2Lag?
    *
    * @return bool
    */
    public function isTypeL2Lag(): bool {
        return $this->getType() === self::TYPE_L2_LAG;
    }

    /**
     * Is the type isTypeL3Lag?
     *
     * @return bool
     */
    public function isTypeL3Lag(): bool {
        return $this->getType() === self::TYPE_L3_LAG;
    }

    /**
     * Is the type isTypeOther?
     *
     * @return bool
     */
    public function isTypeOther(): bool {
        return $this->getType() === self::TYPE_OTHER;
    }

    /**
     * Get graph title
     *
     * @return string
     */
    public function getGraphTitle()
    {
        return $this->graph_title;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get bfd
     *
     * @return boolean
     */
    public function getBFD()
    {
        return $this->bfd;
    }

    /**
     * Get IPv4 Subnet
     *
     * @return boolean
     */
    public function getIPv4Subnet()
    {
        return $this->ipv4_subnet;
    }

    /**
     * Get IPv6 Subnet
     *
     * @return boolean
     */
    public function getIPv46Subnet()
    {
        return $this->ipv6_subnet;
    }

    /**
     * Get cost
     *
     * @return boolean
     */
    public function getCost()
    {
        return $this->cost;
    }


    /**
     * Get CoreLinks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoreLinks()
    {
        return $this->coreLinks;
    }

    /**
     * Add core Link
     *
     * @param CoreLink $coreLink
     *
     * @return PatchPanel
     */
    public function addCoreLink( CoreLink $coreLink)
    {
        $this->coreLinks[] = $coreLink;

        return $this;
    }

    /**
     * Remove patchPanelPort
     *
     * @param CoreLink $coreLink
     */
    public function removeCoreLink( CoreLink $coreLink)
    {
        $this->coreLinks->removeElement( $coreLink );
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES array (or 'Unknown')
     * @return string
     */
    public function resolveType(): string {
        return self::$TYPES[ $this->getType() ] ?? 'Unknown';
    }




    /**
     * Set description
     *
     * @param string $description
     *
     * @return CoreBundle
     */
    public function setDescription( $description )
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return CoreBundle
     */
    public function setType( $type )
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set graph title
     *
     * @param string $graph_title
     *
     * @return CoreBundle
     */
    public function setGraphTitle( $graph_title )
    {
        $this->graph_title = $graph_title;
        return $this;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return CoreBundle
     */
    public function setEnabled( $enabled )
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Set BFD
     *
     * @param boolean $bfd
     *
     * @return CoreBundle
     */
    public function setBFD( $bfd )
    {
        $this->bfd = $bfd;
        return $this;
    }

    /**
     * Set IPv4 Subnet
     *
     * @param string $ipv4_subnet
     *
     * @return CoreBundle
     */
    public function setIPv4Subnet( $ipv4_subnet )
    {
        $this->ipv4_subnet = $ipv4_subnet;
        return $this;
    }

    /**
     * Set IPv6 Subnet
     *
     * @param string $ipv6_subnet
     *
     * @return CoreBundle
     */
    public function setIPv6Subnet( $ipv6_subnet )
    {
        $this->ipv6_subnet = $ipv6_subnet;
        return $this;
    }

    /**
     * Set cost
     *
     * @param integer $cost
     *
     * @return CoreBundle
     */
    public function setCost( $cost )
    {
        $this->cost = $cost;
        return $this;
    }

}

