<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\SecEvent
 */
class SecEvent
{

    const TYPE_SECURITY_VIOLATION     = 'SECURITY_VIOLATION';
    const TYPE_PORT_UPDOWN            = 'PORT_UPDOWN';
    const TYPE_LINEPROTO_UPDOWN       = 'LINEPROTO_UPDOWN';
    const TYPE_BGP_AUTH               = 'BGP_AUTH';
                
    public static $TYPES_DEFAULTS = array(
        self::TYPE_BGP_AUTH           => 1,
        self::TYPE_PORT_UPDOWN        => 1,
        self::TYPE_SECURITY_VIOLATION => 1
    );

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $message
     */
    protected $message;

    /**
     * @var string $recorded_date
     */
    protected $recorded_date;

    /**
     * @var \DateTime $timestamp
     */
    protected $timestamp;

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
    protected $Switch;

    /**
     * @var Entities\SwitchPort
     */
    protected $SwitchPort;


    /**
     * Set type
     *
     * @param string $type
     * @return SecEvent
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
     * Set message
     *
     * @param string $message
     * @return SecEvent
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set recorded_date
     *
     * @param string $recordedDate
     * @return SecEvent
     */
    public function setRecordedDate($recordedDate)
    {
        $this->recorded_date = $recordedDate;
    
        return $this;
    }

    /**
     * Get recorded_date
     *
     * @return string 
     */
    public function getRecordedDate()
    {
        return $this->recorded_date;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return SecEvent
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
     * @return SecEvent
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
     * Set Switch
     *
     * @param Entities\Switcher $switch
     * @return SecEvent
     */
    public function setSwitch(\Entities\Switcher $switch = null)
    {
        $this->Switch = $switch;
    
        return $this;
    }

    /**
     * Get Switch
     *
     * @return Entities\Switcher 
     */
    public function getSwitch()
    {
        return $this->Switch;
    }

    /**
     * Set SwitchPort
     *
     * @param Entities\SwitchPort $switchPort
     * @return SecEvent
     */
    public function setSwitchPort(\Entities\SwitchPort $switchPort = null)
    {
        $this->SwitchPort = $switchPort;
    
        return $this;
    }

    /**
     * Get SwitchPort
     *
     * @return Entities\SwitchPort 
     */
    public function getSwitchPort()
    {
        return $this->SwitchPort;
    }
}