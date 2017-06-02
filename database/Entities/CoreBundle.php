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
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES array (or 'Unknown')
     * @return string
     */
    public function resolveType(): string {
        return self::$TYPES[ $this->getType() ] ?? 'Unknown';
    }

}

