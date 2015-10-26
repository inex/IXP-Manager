<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * NetInfo
 */
class NetInfo
{
    const PROTOCOL_IPV4 = 4;
    const PROTOCOL_IPV6 = 6;

    public static $PROTOCOLS = [
        self::PROTOCOL_IPV4 => "IPv4",
        self::PROTOCOL_IPV6 => "IPv6"
    ];

    /**
     * @var integer
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $property;

    /**
     * @var integer
     */
    protected $ix;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Entities\Vlan
     */
    protected $Vlan;


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
