<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrrdbAsn
 */
class IrrdbAsn
{
    /**
     * @var integer
     */
    private $asn;

    /**
     * @var integer
     */
    private $protocol;

    /**
     * @var \DateTime
     */
    private $first_seen;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\Customer
     */
    private $Customer;


    /**
     * Set asn
     *
     * @param integer $asn
     * @return IrrdbAsn
     */
    public function setAsn($asn)
    {
        $this->asn = $asn;
    
        return $this;
    }

    /**
     * Get asn
     *
     * @return integer 
     */
    public function getAsn()
    {
        return $this->asn;
    }

    /**
     * Set protocol
     *
     * @param integer $protocol
     * @return IrrdbAsn
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
     * Set first_seen
     *
     * @param \DateTime $firstSeen
     * @return IrrdbAsn
     */
    public function setFirstSeen($firstSeen)
    {
        $this->first_seen = $firstSeen;
    
        return $this;
    }

    /**
     * Get first_seen
     *
     * @return \DateTime 
     */
    public function getFirstSeen()
    {
        return $this->first_seen;
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
     * @return IrrdbAsn
     */
    public function setCustomer(\Entities\Customer $customer)
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