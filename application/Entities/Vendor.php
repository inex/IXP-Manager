<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Vendor
 */
class Vendor
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $Switches;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Switches = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Vendor
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add Switches
     *
     * @param Entities\Switcher $switches
     * @return Vendor
     */
    public function addSwitche(\Entities\Switcher $switches)
    {
        $this->Switches[] = $switches;
    
        return $this;
    }

    /**
     * Remove Switches
     *
     * @param Entities\Switcher $switches
     */
    public function removeSwitche(\Entities\Switcher $switches)
    {
        $this->Switches->removeElement($switches);
    }

    /**
     * Get Switches
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSwitches()
    {
        return $this->Switches;
    }
}