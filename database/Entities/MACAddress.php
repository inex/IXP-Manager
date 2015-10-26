<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\MACAddress
 */
class MACAddress
{
    /**
     * @var \DateTime $firstseen
     */
    protected $firstseen;

    /**
     * @var \DateTime $lastseen
     */
    protected $lastseen;

    /**
     * @var string $mac
     */
    protected $mac;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\VirtualInterface
     */
    protected $VirtualInterface;


    /**
     * Set firstseen
     *
     * @param \DateTime $firstseen
     * @return MACAddress
     */
    public function setFirstseen($firstseen)
    {
        $this->firstseen = $firstseen;
    
        return $this;
    }

    /**
     * Get firstseen
     *
     * @return \DateTime 
     */
    public function getFirstseen()
    {
        return $this->firstseen;
    }

    /**
     * Set lastseen
     *
     * @param \DateTime $lastseen
     * @return MACAddress
     */
    public function setLastseen($lastseen)
    {
        $this->lastseen = $lastseen;
    
        return $this;
    }

    /**
     * Get lastseen
     *
     * @return \DateTime 
     */
    public function getLastseen()
    {
        return $this->lastseen;
    }

    /**
     * Set mac
     *
     * @param string $mac
     * @return MACAddress
     */
    public function setMac($mac)
    {
        $this->mac = $mac;
    
        return $this;
    }

    /**
     * Get mac
     *
     * @return string 
     */
    public function getMac()
    {
        return $this->mac;
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
     * Set VirtualInterface
     *
     * @param Entities\VirtualInterface $virtualInterface
     * @return MACAddress
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
}
