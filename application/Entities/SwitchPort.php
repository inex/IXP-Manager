<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\SwitchPort
 */
class SwitchPort
{
    /**
     * @var integer $type
     */
    private $type;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Entities\PhysicalInterface
     */
    private $PhysicalInterface;

    /**
     * @var Entities\Switcher
     */
    private $Switcher;


    /**
     * Set type
     *
     * @param integer $type
     * @return SwitchPort
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SwitchPort
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
     * Set PhysicalInterface
     *
     * @param Entities\PhysicalInterface $physicalInterface
     * @return SwitchPort
     */
    public function setPhysicalInterface(\Entities\PhysicalInterface $physicalInterface = null)
    {
        $this->PhysicalInterface = $physicalInterface;
    
        return $this;
    }

    /**
     * Get PhysicalInterface
     *
     * @return Entities\PhysicalInterface 
     */
    public function getPhysicalInterface()
    {
        return $this->PhysicalInterface;
    }

    /**
     * Set Switcher
     *
     * @param Entities\Switcher $switcher
     * @return SwitchPort
     */
    public function setSwitcher(\Entities\Switcher $switcher = null)
    {
        $this->Switcher = $switcher;
    
        return $this;
    }

    /**
     * Get Switcher
     *
     * @return Entities\Switcher 
     */
    public function getSwitcher()
    {
        return $this->Switcher;
    }
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $SecEvents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->SecEvents = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add SecEvents
     *
     * @param Entities\SecEvent $secEvents
     * @return SwitchPort
     */
    public function addSecEvent(\Entities\SecEvent $secEvents)
    {
        $this->SecEvents[] = $secEvents;
    
        return $this;
    }

    /**
     * Remove SecEvents
     *
     * @param Entities\SecEvent $secEvents
     */
    public function removeSecEvent(\Entities\SecEvent $secEvents)
    {
        $this->SecEvents->removeElement($secEvents);
    }

    /**
     * Get SecEvents
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSecEvents()
    {
        return $this->SecEvents;
    }
}