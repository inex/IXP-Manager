<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\VlanInterface
 */
class VlanInterface
{
    /**
     * @var boolean $ipv4enabled
     */
    protected $ipv4enabled;

    /**
     * @var string $ipv4hostname
     */
    protected $ipv4hostname;

    /**
     * @var boolean $ipv6enabled
     */
    protected $ipv6enabled;

    /**
     * @var string $ipv6hostname
     */
    protected $ipv6hostname;

    /**
     * @var boolean $mcastenabled
     */
    protected $mcastenabled;

    /**
     * @var boolean $irrdbfilter
     */
    protected $irrdbfilter;

    /**
     * @var string $bgpmd5secret
     */
    protected $bgpmd5secret;

    /**
     * @var string $ipv4bgpmd5secret
     */
    protected $ipv4bgpmd5secret;

    /**
     * @var string $ipv6bgpmd5secret
     */
    protected $ipv6bgpmd5secret;

    /**
     * @var integer $maxbgpprefix
     */
    protected $maxbgpprefix;

    /**
     * @var boolean $rsclient
     */
    protected $rsclient;

    /**
     * @var boolean $ipv4canping
     */
    protected $ipv4canping;

    /**
     * @var boolean $ipv6canping
     */
    protected $ipv6canping;

    /**
     * @var boolean $ipv4monitorrcbgp
     */
    protected $ipv4monitorrcbgp;

    /**
     * @var boolean $ipv6monitorrcbgp
     */
    protected $ipv6monitorrcbgp;

    /**
     * @var boolean $as112client
     */
    protected $as112client;

    /**
     * @var boolean $busyhost
     */
    protected $busyhost;

    /**
     * @var string $notes
     */
    protected $notes;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\IPv4Address
     */
    protected $IPv4Address;

    /**
     * @var Entities\IPv6Address
     */
    protected $IPv6Address;

    /**
     * @var Entities\VirtualInterface
     */
    protected $VirtualInterface;

    /**
     * @var Entities\Vlan
     */
    protected $Vlan;


    /**
     * Set ipv4enabled
     *
     * @param boolean $ipv4enabled
     * @return VlanInterface
     */
    public function setIpv4enabled($ipv4enabled)
    {
        $this->ipv4enabled = $ipv4enabled;
    
        return $this;
    }

    /**
     * Get ipv4enabled
     *
     * @return boolean 
     */
    public function getIpv4enabled()
    {
        return $this->ipv4enabled;
    }

    /**
     * Set ipv4hostname
     *
     * @param string $ipv4hostname
     * @return VlanInterface
     */
    public function setIpv4hostname($ipv4hostname)
    {
        $this->ipv4hostname = $ipv4hostname;
    
        return $this;
    }

    /**
     * Get ipv4hostname
     *
     * @return string 
     */
    public function getIpv4hostname()
    {
        return $this->ipv4hostname;
    }

    /**
     * Set ipv6enabled
     *
     * @param boolean $ipv6enabled
     * @return VlanInterface
     */
    public function setIpv6enabled($ipv6enabled)
    {
        $this->ipv6enabled = $ipv6enabled;
    
        return $this;
    }

    /**
     * Get ipv6enabled
     *
     * @return boolean 
     */
    public function getIpv6enabled()
    {
        return $this->ipv6enabled;
    }

    /**
     * Set ipv6hostname
     *
     * @param string $ipv6hostname
     * @return VlanInterface
     */
    public function setIpv6hostname($ipv6hostname)
    {
        $this->ipv6hostname = $ipv6hostname;
    
        return $this;
    }

    /**
     * Get ipv6hostname
     *
     * @return string 
     */
    public function getIpv6hostname()
    {
        return $this->ipv6hostname;
    }

    /**
     * Set mcastenabled
     *
     * @param boolean $mcastenabled
     * @return VlanInterface
     */
    public function setMcastenabled($mcastenabled)
    {
        $this->mcastenabled = $mcastenabled;
    
        return $this;
    }

    /**
     * Get mcastenabled
     *
     * @return boolean 
     */
    public function getMcastenabled()
    {
        return $this->mcastenabled;
    }

    /**
     * Set irrdbfilter
     *
     * @param boolean $irrdbfilter
     * @return VlanInterface
     */
    public function setIrrdbfilter($irrdbfilter)
    {
        $this->irrdbfilter = $irrdbfilter;
    
        return $this;
    }

    /**
     * Get irrdbfilter
     *
     * @return boolean 
     */
    public function getIrrdbfilter()
    {
        return $this->irrdbfilter;
    }

    /**
     * Set bgpmd5secret
     *
     * @param string $bgpmd5secret
     * @return VlanInterface
     */
    public function setBgpmd5secret($bgpmd5secret)
    {
        $this->bgpmd5secret = $bgpmd5secret;
    
        return $this;
    }

    /**
     * Get bgpmd5secret
     *
     * @return string 
     */
    public function getBgpmd5secret()
    {
        return $this->bgpmd5secret;
    }

    /**
     * Set ipv4bgpmd5secret
     *
     * @param string $ipv4bgpmd5secret
     * @return VlanInterface
     */
    public function setIpv4bgpmd5secret($ipv4bgpmd5secret)
    {
        $this->ipv4bgpmd5secret = $ipv4bgpmd5secret;
    
        return $this;
    }

    /**
     * Get ipv4bgpmd5secret
     *
     * @return string 
     */
    public function getIpv4bgpmd5secret()
    {
        return $this->ipv4bgpmd5secret;
    }

    /**
     * Set ipv6bgpmd5secret
     *
     * @param string $ipv6bgpmd5secret
     * @return VlanInterface
     */
    public function setIpv6bgpmd5secret($ipv6bgpmd5secret)
    {
        $this->ipv6bgpmd5secret = $ipv6bgpmd5secret;
    
        return $this;
    }

    /**
     * Get ipv6bgpmd5secret
     *
     * @return string 
     */
    public function getIpv6bgpmd5secret()
    {
        return $this->ipv6bgpmd5secret;
    }

    /**
     * Set maxbgpprefix
     *
     * @param integer $maxbgpprefix
     * @return VlanInterface
     */
    public function setMaxbgpprefix($maxbgpprefix)
    {
        $this->maxbgpprefix = $maxbgpprefix;
    
        return $this;
    }

    /**
     * Get maxbgpprefix
     *
     * @return integer 
     */
    public function getMaxbgpprefix()
    {
        return $this->maxbgpprefix;
    }

    /**
     * Set rsclient
     *
     * @param boolean $rsclient
     * @return VlanInterface
     */
    public function setRsclient($rsclient)
    {
        $this->rsclient = $rsclient;
    
        return $this;
    }

    /**
     * Get rsclient
     *
     * @return boolean 
     */
    public function getRsclient()
    {
        return $this->rsclient;
    }

    /**
     * Set ipv4canping
     *
     * @param boolean $ipv4canping
     * @return VlanInterface
     */
    public function setIpv4canping($ipv4canping)
    {
        $this->ipv4canping = $ipv4canping;
    
        return $this;
    }

    /**
     * Get ipv4canping
     *
     * @return boolean 
     */
    public function getIpv4canping()
    {
        return $this->ipv4canping;
    }

    /**
     * Set ipv6canping
     *
     * @param boolean $ipv6canping
     * @return VlanInterface
     */
    public function setIpv6canping($ipv6canping)
    {
        $this->ipv6canping = $ipv6canping;
    
        return $this;
    }

    /**
     * Get ipv6canping
     *
     * @return boolean 
     */
    public function getIpv6canping()
    {
        return $this->ipv6canping;
    }

    /**
     * Set ipv4monitorrcbgp
     *
     * @param boolean $ipv4monitorrcbgp
     * @return VlanInterface
     */
    public function setIpv4monitorrcbgp($ipv4monitorrcbgp)
    {
        $this->ipv4monitorrcbgp = $ipv4monitorrcbgp;
    
        return $this;
    }

    /**
     * Get ipv4monitorrcbgp
     *
     * @return boolean 
     */
    public function getIpv4monitorrcbgp()
    {
        return $this->ipv4monitorrcbgp;
    }

    /**
     * Set ipv6monitorrcbgp
     *
     * @param boolean $ipv6monitorrcbgp
     * @return VlanInterface
     */
    public function setIpv6monitorrcbgp($ipv6monitorrcbgp)
    {
        $this->ipv6monitorrcbgp = $ipv6monitorrcbgp;
    
        return $this;
    }

    /**
     * Get ipv6monitorrcbgp
     *
     * @return boolean 
     */
    public function getIpv6monitorrcbgp()
    {
        return $this->ipv6monitorrcbgp;
    }

    /**
     * Set as112client
     *
     * @param boolean $as112client
     * @return VlanInterface
     */
    public function setAs112client($as112client)
    {
        $this->as112client = $as112client;
    
        return $this;
    }

    /**
     * Get as112client
     *
     * @return boolean 
     */
    public function getAs112client()
    {
        return $this->as112client;
    }

    /**
     * Set busyhost
     *
     * @param boolean $busyhost
     * @return VlanInterface
     */
    public function setBusyhost($busyhost)
    {
        $this->busyhost = $busyhost;
    
        return $this;
    }

    /**
     * Get busyhost
     *
     * @return boolean 
     */
    public function getBusyhost()
    {
        return $this->busyhost;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return VlanInterface
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
     * Set IPv4Address
     *
     * @param Entities\IPv4Address $iPv4Address
     * @return VlanInterface
     */
    public function setIPv4Address(\Entities\IPv4Address $iPv4Address = null)
    {
        $this->IPv4Address = $iPv4Address;
    
        return $this;
    }

    /**
     * Get IPv4Address
     *
     * @return Entities\IPv4Address 
     */
    public function getIPv4Address()
    {
        return $this->IPv4Address;
    }

    /**
     * Set IPv6Address
     *
     * @param Entities\IPv6Address $iPv6Address
     * @return VlanInterface
     */
    public function setIPv6Address(\Entities\IPv6Address $iPv6Address = null)
    {
        $this->IPv6Address = $iPv6Address;
    
        return $this;
    }

    /**
     * Get IPv6Address
     *
     * @return Entities\IPv6Address 
     */
    public function getIPv6Address()
    {
        return $this->IPv6Address;
    }

    /**
     * Set VirtualInterface
     *
     * @param Entities\VirtualInterface $virtualInterface
     * @return VlanInterface
     */
    public function setVirtualInterface(\Entities\VirtualInterface $virtualInterface = null)
    {
        $this->VirtualInterface = $virtualInterface;
    
        return $this;
    }

    /**
     * Get VirtualInterface
     *
     * @return Entities\VirtualInterface 
     */
    public function getVirtualInterface()
    {
        return $this->VirtualInterface;
    }

    /**
     * Set Vlan
     *
     * @param Entities\Vlan $vlan
     * @return VlanInterface
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
