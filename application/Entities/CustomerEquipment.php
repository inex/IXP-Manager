<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\CustomerEquipment
 */
class CustomerEquipment
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Entities\Customer
     */
    private $Customer;

    /**
     * @var Entities\Cabinet
     */
    private $Cabinet;


    /**
     * Set name
     *
     * @param string $name
     * @return CustomerEquipment
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
     * Set description
     *
     * @param string $description
     * @return CustomerEquipment
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
     * @return CustomerEquipment
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
     * Set Cabinet
     *
     * @param Entities\Cabinet $cabinet
     * @return CustomerEquipment
     */
    public function setCabinet(\Entities\Cabinet $cabinet = null)
    {
        $this->Cabinet = $cabinet;
    
        return $this;
    }

    /**
     * Get Cabinet
     *
     * @return Entities\Cabinet 
     */
    public function getCabinet()
    {
        return $this->Cabinet;
    }
    /**
     * @var string $descr
     */
    private $descr;


    /**
     * Set descr
     *
     * @param string $descr
     * @return CustomerEquipment
     */
    public function setDescr($descr)
    {
        $this->descr = $descr;
    
        return $this;
    }

    /**
     * Get descr
     *
     * @return string 
     */
    public function getDescr()
    {
        return $this->descr;
    }
}