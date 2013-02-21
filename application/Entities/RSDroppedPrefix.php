<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\RSDroppedPrefix
 */
class RSDroppedPrefix
{
    /**
     * Map prefix acceptance types to summary functions
     * @var array Map prefix acceptance types to summary functions
     */
    public static $SUMMARY_TYPES_FNS = [
        'adv_acc'  => 'getSummaryRoutesAdvertisedAndAccepted',
        'adv_nacc' => 'getSummaryRoutesAdvertisedAndNotAccepted',
        'nadv_acc' => 'getSummaryRoutesNotAdvertisedButAcceptable'
    ];
    
    /**
     * Map prefix acceptance types to lookup functions
     * @var array Map prefix acceptance types to lookup functions
     */
    public static $ROUTES_TYPES_FNS = [
        'adv_acc'  => 'getRoutesAdvertisedAndAccepted',
        'adv_nacc' => 'getRoutesAdvertisedAndNotAccepted',
        'nadv_acc' => 'getRoutesNotAdvertisedButAcceptable'
    ];
    
    /**
     * @var \DateTime $timestamp
     */
    private $timestamp;

    /**
     * @var string $prefix
     */
    private $prefix;

    /**
     * @var integer $protocol
     */
    private $protocol;

    /**
     * @var integer $irrdb
     */
    private $irrdb;

    /**
     * @var integer $rs_origin
     */
    private $rs_origin;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Entities\Customer
     */
    private $Customer;


    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return RSDroppedPrefix
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
     * Set prefix
     *
     * @param string $prefix
     * @return RSDroppedPrefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    
        return $this;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set protocol
     *
     * @param integer $protocol
     * @return RSDroppedPrefix
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
     * Set irrdb
     *
     * @param integer $irrdb
     * @return RSDroppedPrefix
     */
    public function setIrrdb($irrdb)
    {
        $this->irrdb = $irrdb;
    
        return $this;
    }

    /**
     * Get irrdb
     *
     * @return integer
     */
    public function getIrrdb()
    {
        return $this->irrdb;
    }

    /**
     * Set rs_origin
     *
     * @param integer $rsOrigin
     * @return RSDroppedPrefix
     */
    public function setRsOrigin($rsOrigin)
    {
        $this->rs_origin = $rsOrigin;
    
        return $this;
    }

    /**
     * Get rs_origin
     *
     * @return integer
     */
    public function getRsOrigin()
    {
        return $this->rs_origin;
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
     * @return RSDroppedPrefix
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
}