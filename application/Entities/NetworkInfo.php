<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\NetworkInfo
 */
class NetworkInfo
{
    /**
     * @var integer $protocol
     */
    protected $protocol;

    /**
     * @var string $network
     */
    protected $network;

    /**
     * @var integer $masklen
     */
    protected $masklen;

    /**
     * @var string $rs1address
     */
    protected $rs1address;

    /**
     * @var string $rs2address
     */
    protected $rs2address;

    /**
     * @var string $dnsfile
     */
    protected $dnsfile;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\Vlan
     */
    protected $Vlan;


    /**
     * Set protocol
     *
     * @param integer $protocol
     * @return NetworkInfo
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
     * Set network
     *
     * @param string $network
     * @return NetworkInfo
     */
    public function setNetwork($network)
    {
        $this->network = $network;
    
        return $this;
    }

    /**
     * Get network
     *
     * @return string 
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * Set masklen
     *
     * @param integer $masklen
     * @return NetworkInfo
     */
    public function setMasklen($masklen)
    {
        $this->masklen = $masklen;
    
        return $this;
    }

    /**
     * Get masklen
     *
     * @return integer 
     */
    public function getMasklen()
    {
        return $this->masklen;
    }

    /**
     * Set rs1address
     *
     * @param string $rs1address
     * @return NetworkInfo
     */
    public function setRs1address($rs1address)
    {
        $this->rs1address = $rs1address;
    
        return $this;
    }

    /**
     * Get rs1address
     *
     * @return string 
     */
    public function getRs1address()
    {
        return $this->rs1address;
    }

    /**
     * Set rs2address
     *
     * @param string $rs2address
     * @return NetworkInfo
     */
    public function setRs2address($rs2address)
    {
        $this->rs2address = $rs2address;
    
        return $this;
    }

    /**
     * Get rs2address
     *
     * @return string 
     */
    public function getRs2address()
    {
        return $this->rs2address;
    }

    /**
     * Set dnsfile
     *
     * @param string $dnsfile
     * @return NetworkInfo
     */
    public function setDnsfile($dnsfile)
    {
        $this->dnsfile = $dnsfile;
    
        return $this;
    }

    /**
     * Get dnsfile
     *
     * @return string 
     */
    public function getDnsfile()
    {
        return $this->dnsfile;
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
     * @param Entities\Vlan $vlan
     * @return NetworkInfo
     */
    public function setVlan(\Entities\Vlan $vlan = null)
    {
        $this->Vlan = $vlan;
    
        return $this;
    }

    /**
     * Get Vlan
     *
     * @return Entities\Vlan 
     */
    public function getVlan()
    {
        return $this->Vlan;
    }
}