<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Cabinet
 */
class Cabinet
{
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $Switches;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $CustomerEquipment;

    /**
     * @var Entities\Location
     */
    protected $Location;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Switches = new \Doctrine\Common\Collections\ArrayCollection();
        $this->CustomerEquipment = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add Switches
     *
     * @param Entities\Switcher $switches
     * @return Cabinet
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

    /**
     * Add CustomerEquipment
     *
     * @param Entities\CustomerEquipment $customerEquipment
     * @return Cabinet
     */
    public function addCustomerEquipment(\Entities\CustomerEquipment $customerEquipment)
    {
        $this->CustomerEquipment[] = $customerEquipment;
    
        return $this;
    }

    /**
     * Remove CustomerEquipment
     *
     * @param Entities\CustomerEquipment $customerEquipment
     */
    public function removeCustomerEquipment(\Entities\CustomerEquipment $customerEquipment)
    {
        $this->CustomerEquipment->removeElement($customerEquipment);
    }

    /**
     * Get CustomerEquipment
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCustomerEquipment()
    {
        return $this->CustomerEquipment;
    }

    /**
     * Set Location
     *
     * @param Entities\Location $location
     * @return Cabinet
     */
    public function setLocation(\Entities\Location $location = null)
    {
        $this->Location = $location;
    
        return $this;
    }

    /**
     * Get Location
     *
     * @return Entities\Location 
     */
    public function getLocation()
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
}