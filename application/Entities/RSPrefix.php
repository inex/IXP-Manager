<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * RSPrefix
 */
class RSPrefix
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
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var integer
     */
    private $protocol;

    /**
     * @var integer
     */
    private $irrdb;

    /**
     * @var integer
     */
    private $rs_origin;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\Customer
     */
    private $Customer;


    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return RSPrefix
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
     * @return RSPrefix
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
     * @return RSPrefix
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
     * @return RSPrefix
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
     * @return RSPrefix
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
     * @param \Entities\Customer $customer
     * @return RSPrefix
     */
    public function setCustomer(\Entities\Customer $customer = null)
    {
        $this->Customer = $customer;
    
        return $this;
    }

    /**
     * Get Customer
     *
     * @return \Entities\Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }
}