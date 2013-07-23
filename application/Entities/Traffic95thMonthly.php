<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Traffic95thMonthly
 */
class Traffic95thMonthly
{
    /**
     * @var \DateTime $month
     */
    protected $month;

    /**
     * @var integer $max_95th
     */
    protected $max_95th;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\Customer
     */
    protected $Customer;


    /**
     * Set month
     *
     * @param \DateTime $month
     * @return Traffic95thMonthly
     */
    public function setMonth($month)
    {
        $this->month = $month;
    
        return $this;
    }

    /**
     * Get month
     *
     * @return \DateTime 
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set max_95th
     *
     * @param integer $max95th
     * @return Traffic95thMonthly
     */
    public function setMax95th($max95th)
    {
        $this->max_95th = $max95th;
    
        return $this;
    }

    /**
     * Get max_95th
     *
     * @return integer 
     */
    public function getMax95th()
    {
        return $this->max_95th;
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
     * @return Traffic95thMonthly
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