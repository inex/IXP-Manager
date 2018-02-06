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
    protected $name;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $Switches;

    /**
     * @var ArrayCollection
     */
    protected $consoleServers;

    /**
     * @var string $bundle_name
     */
    protected $bundle_name;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Switches = new \Doctrine\Common\Collections\ArrayCollection();
        $this->consoleServers = new \Doctrine\Common\Collections\ArrayCollection();
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
    /**
     * @var string
     */
    private $shortname;

    /**
     * @var string
     */
    private $nagios_name;


    /**
     * Set shortname
     *
     * @param string $shortname
     * @return Vendor
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
     * Set nagios_name
     *
     * @param string $nagiosName
     * @return Vendor
     */
    public function setNagiosName($nagiosName)
    {
        $this->nagios_name = $nagiosName;
    
        return $this;
    }

    /**
     * Get nagios_name
     *
     * @return string 
     */
    public function getNagiosName()
    {
        return $this->nagios_name;
    }

    /**
     * Add Switches
     *
     * @param \Entities\Switcher $switches
     * @return Vendor
     */
    public function addSwitch(\Entities\Switcher $switches)
    {
        $this->Switches[] = $switches;

        return $this;
    }

    /**
     * Remove Switches
     *
     * @param \Entities\Switcher $switches
     */
    public function removeSwitch(\Entities\Switcher $switches)
    {
        $this->Switches->removeElement($switches);
    }

    /**
     * @return string
     */
    public function getBundleName(): string {
        return $this->bundle_name ?? '';
    }

    /**
     * @param string $bundle_name
     */
    public function setBundleName( string $bundle_name = null ) {
        $this->bundle_name = $bundle_name;
    }





    /**
     * Add consoleServer
     *
     * @param ConsoleServer $consoleServer
     * @return Vendor
     */
    public function addConsoleServer(ConsoleServer $consoleServer)
    {
        $this->consoleServers[] = $consoleServer;

        return $this;
    }

    /**
     * Remove consoleServer
     *
     * @param ConsoleServer $consoleServer
     */
    public function removeConsoleServer(ConsoleServer $consoleServer)
    {
        $this->consoleServers->removeElement($consoleServer);
    }

    /**
     * Get consoleServers
     *
     * @return ArrayCollection
     */
    public function getConsoleServers()
    {
        return $this->consoleServers;
    }



}
