<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\ConsoleServerConnection
 */
class ConsoleServerConnection
{
    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var string $port
     */
    protected $port;

    /**
     * @var integer $speed
     */
    protected $speed;

    /**
     * @var integer $parity
     */
    protected $parity;

    /**
     * @var integer $stopbits
     */
    protected $stopbits;

    /**
     * @var integer $flowcontrol
     */
    protected $flowcontrol;

    /**
     * @var boolean $autobaud
     */
    protected $autobaud;

    /**
     * @var string $notes
     */
    protected $notes;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\Customer
     */
    protected $Customer;

    /**
     * @var Entities\Switcher
     */
    protected $Switcher;


    /**
     * Set description
     *
     * @param string $description
     * @return ConsoleServerConnection
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set port
     *
     * @param string $port
     * @return ConsoleServerConnection
     */
    public function setPort($port)
    {
        $this->port = $port;
    
        return $this;
    }

    /**
     * Get port
     *
     * @return string 
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set speed
     *
     * @param integer $speed
     * @return ConsoleServerConnection
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
    
        return $this;
    }

    /**
     * Get speed
     *
     * @return integer 
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Set parity
     *
     * @param integer $parity
     * @return ConsoleServerConnection
     */
    public function setParity($parity)
    {
        $this->parity = $parity;
    
        return $this;
    }

    /**
     * Get parity
     *
     * @return integer 
     */
    public function getParity()
    {
        return $this->parity;
    }

    /**
     * Set stopbits
     *
     * @param integer $stopbits
     * @return ConsoleServerConnection
     */
    public function setStopbits($stopbits)
    {
        $this->stopbits = $stopbits;
    
        return $this;
    }

    /**
     * Get stopbits
     *
     * @return integer 
     */
    public function getStopbits()
    {
        return $this->stopbits;
    }

    /**
     * Set flowcontrol
     *
     * @param integer $flowcontrol
     * @return ConsoleServerConnection
     */
    public function setFlowcontrol($flowcontrol)
    {
        $this->flowcontrol = $flowcontrol;
    
        return $this;
    }

    /**
     * Get flowcontrol
     *
     * @return integer 
     */
    public function getFlowcontrol()
    {
        return $this->flowcontrol;
    }

    /**
     * Set autobaud
     *
     * @param boolean $autobaud
     * @return ConsoleServerConnection
     */
    public function setAutobaud($autobaud)
    {
        $this->autobaud = $autobaud;
    
        return $this;
    }

    /**
     * Get autobaud
     *
     * @return boolean 
     */
    public function getAutobaud()
    {
        return $this->autobaud;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return ConsoleServerConnection
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
     * Set Customer
     *
     * @param Entities\Customer $customer
     * @return ConsoleServerConnection
     */
    public function setCustomer(\Entities\Customer $customer = null)
    {
        $this->Customer = $customer;
    
        return $this;
    }

    /**
     * Get Customer
     *
     * @return Entities\Customer 
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * Set Switcher
     *
     * @param Entities\Switcher $switcher
     * @return ConsoleServerConnection
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
}