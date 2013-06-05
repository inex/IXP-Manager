<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * NetInfo
 */
class NetInfo
{
    /**
     * @var integer
     */
    private $protocol;

    /**
     * @var string
     */
    private $property;

    /**
     * @var integer
     */
    private $ix;

    /**
     * @var string
     */
    private $value;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\Vlan
     */
    private $Vlan;


    /**
     * Set protocol
     *
     * @param integer $protocol
     * @return NetInfo
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    
        return $this;
    }

    /**
     * Get protocol
     *
     * @return integer 
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set property
     *
     * @param string $property
     * @return NetInfo
     */
    public function setProperty($property)
    {
        $this->property = $property;
    
        return $this;
    }

    /**
     * Get property
     *
     * @return string 
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set ix
     *
     * @param integer $ix
     * @return NetInfo
     */
    public function setIx($ix)
    {
        $this->ix = $ix;
    
        return $this;
    }

    /**
     * Get ix
     *
     * @return integer 
     */
    public function getIx()
    {
        return $this->ix;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return NetInfo
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
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
     * Set Vlan
     *
     * @param \Entities\Vlan $vlan
     * @return NetInfo
     */
    public function setVlan(\Entities\Vlan $vlan)
    {
        $this->Vlan = $vlan;
    
        return $this;
    }

    /**
     * Get Vlan
     *
     * @return \Entities\Vlan 
     */
    public function getVlan()
    {
        return $this->Vlan;
    }
}
