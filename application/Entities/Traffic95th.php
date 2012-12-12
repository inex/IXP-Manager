<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Traffic95th
 */
class Traffic95th
{
    /**
     * @var \DateTime $datetime
     */
    private $datetime;

    /**
     * @var integer $average
     */
    private $average;

    /**
     * @var integer $max
     */
    private $max;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Entities\Customer
     */
    private $Customer;


    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     * @return Traffic95th
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    
        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime 
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set average
     *
     * @param integer $average
     * @return Traffic95th
     */
    public function setAverage($average)
    {
        $this->average = $average;
    
        return $this;
    }

    /**
     * Get average
     *
     * @return integer 
     */
    public function getAverage()
    {
        return $this->average;
    }

    /**
     * Set max
     *
     * @param integer $max
     * @return Traffic95th
     */
    public function setMax($max)
    {
        $this->max = $max;
    
        return $this;
    }

    /**
     * Get max
     *
     * @return integer 
     */
    public function getMax()
    {
        return $this->max;
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
     * @return Traffic95th
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