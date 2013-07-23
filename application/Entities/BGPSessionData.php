<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\BGPSessionData
 */
class BGPSessionData
{
    /**
     * @var integer $srcipaddressid
     */
    protected $srcipaddressid;

    /**
     * @var integer $desipaddressid
     */
    protected $desipaddressid;

    /**
     * @var integer $protocol
     */
    protected $protocol;

    /**
     * @var integer $vlan
     */
    protected $vlan;

    /**
     * @var integer $packetcount
     */
    protected $packetcount;

    /**
     * @var \DateTime $timestamp
     */
    protected $timestamp;

    /**
     * @var string $source
     */
    protected $source;

    /**
     * @var integer $id
     */
    protected $id;


    /**
     * Set srcipaddressid
     *
     * @param integer $srcipaddressid
     * @return BGPSessionData
     */
    public function setSrcipaddressid($srcipaddressid)
    {
        $this->srcipaddressid = $srcipaddressid;
    
        return $this;
    }

    /**
     * Get srcipaddressid
     *
     * @return integer
     */
    public function getSrcipaddressid()
    {
        return $this->srcipaddressid;
    }

    /**
     * Set desipaddressid
     *
     * @param integer $desipaddressid
     * @return BGPSessionData
     */
    public function setDesipaddressid($desipaddressid)
    {
        $this->desipaddressid = $desipaddressid;
    
        return $this;
    }

    /**
     * Get desipaddressid
     *
     * @return integer
     */
    public function getDesipaddressid()
    {
        return $this->desipaddressid;
    }

    /**
     * Set protocol
     *
     * @param integer $protocol
     * @return BGPSessionData
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
     * Set vlan
     *
     * @param integer $vlan
     * @return BGPSessionData
     */
    public function setVlan($vlan)
    {
        $this->vlan = $vlan;
    
        return $this;
    }

    /**
     * Get vlan
     *
     * @return integer
     */
    public function getVlan()
    {
        return $this->vlan;
    }

    /**
     * Set packetcount
     *
     * @param integer $packetcount
     * @return BGPSessionData
     */
    public function setPacketcount($packetcount)
    {
        $this->packetcount = $packetcount;
    
        return $this;
    }

    /**
     * Get packetcount
     *
     * @return integer
     */
    public function getPacketcount()
    {
        return $this->packetcount;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return BGPSessionData
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    
        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return BGPSessionData
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
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
     * @var integer $dstipaddressid
     */
    protected $dstipaddressid;


    /**
     * Set dstipaddressid
     *
     * @param integer $dstipaddressid
     * @return BGPSessionData
     */
    public function setDstipaddressid($dstipaddressid)
    {
        $this->dstipaddressid = $dstipaddressid;
    
        return $this;
    }

    /**
     * Get dstipaddressid
     *
     * @return integer
     */
    public function getDstipaddressid()
    {
        return $this->dstipaddressid;
    }
}