<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Infrastructure
 */
class Infrastructure
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $shortname;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Entities\IXP
     */
    protected $IXP;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $Switchers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Vlans;

    /**
     * @var string
     */
    private $mrtg_path;

    /**
     * @var string
     */
    private $mrtg_p2p_path;

    /**
     * Set name
     *
     * @param string $name
     * @return Infrastructure
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
     * Set shortname
     *
     * @param string $shortname
     * @return Infrastructure
     */
    public function setShortname($shortname)
    {
        $this->shortname = $shortname;
    
        return $this;
    }

    /**
     * Get shortname
     *
     * @return string 
     */
    public function getShortname()
    {
        return $this->shortname;
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
     * Set IXP
     *
     * @param \Entities\IXP $iXP
     * @return Infrastructure
     */
    public function setIXP(\Entities\IXP $iXP = null)
    {
        $this->IXP = $iXP;
    
        return $this;
    }

    /**
     * Get IXP
     *
     * @return \Entities\IXP 
     */
    public function getIXP()
    {
        return $this->IXP;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Switchers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add Switchers
     *
     * @param \Entities\Switcher $switchers
     * @return Infrastructure
     */
    public function addSwitcher(\Entities\Switcher $switchers)
    {
        $this->Switchers[] = $switchers;
    
        return $this;
    }

    /**
     * Remove Switchers
     *
     * @param \Entities\Switcher $switchers
     */
    public function removeSwitcher(\Entities\Switcher $switchers)
    {
        $this->Switchers->removeElement($switchers);
    }

    /**
     * Get Switchers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSwitchers()
    {
        return $this->Switchers;
    }

    /**
     * Add Vlans
     *
     * @param \Entities\Vlan $vlans
     * @return Infrastructure
     */
    public function addVlan(\Entities\Vlan $vlans)
    {
        $this->Vlans[] = $vlans;
    
        return $this;
    }

    /**
     * Remove Vlans
     *
     * @param \Entities\Vlan $vlans
     */
    public function removeVlan(\Entities\Vlan $vlans)
    {
        $this->Vlans->removeElement($vlans);
    }

    /**
     * Get Vlans
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVlans()
    {
        return $this->Vlans;
    }

    /**
     * Set mrtg_path
     *
     * @param string $mrtgPath
     * @return Infrastructure
     */
    public function setMrtgPath($mrtgPath)
    {
        $this->mrtg_path = $mrtgPath;
    
        return $this;
    }

    /**
     * Get mrtg_path
     *
     * @return string 
     */
    public function getMrtgPath()
    {
        return $this->mrtg_path;
    }

    /**
     * Set mrtg_p2p_path
     *
     * @param string $mrtgP2pPath
     * @return Infrastructure
     */
    public function setMrtgP2pPath($mrtgP2pPath)
    {
        $this->mrtg_p2p_path = $mrtgP2pPath;
        return $this;
    }
                    
    /**
     * @var boolean
     */
    private $isPrimary;


    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     * @return Infrastructure
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }

    /**
     * Get mrtg_p2p_path
     *
     * @return string 
     */
    public function getMrtgP2pPath()
    {
        return $this->mrtg_p2p_path;
    }
    
    /*
     * Get isPrimary
     *
     * @return boolean 
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }
}
