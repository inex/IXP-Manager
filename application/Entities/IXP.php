<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * IXP
 */
class IXP
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $shortname;

    /**
     * @var string
     */
    private $address1;

    /**
     * @var string
     */
    private $address2;

    /**
     * @var string
     */
    private $address3;

    /**
     * @var string
     */
    private $address4;

    /**
     * @var string
     */
    private $country;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Infrastructures;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Infrastructures = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return IXP
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
     * @return IXP
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
     * Set address1
     *
     * @param string $address1
     * @return IXP
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    
        return $this;
    }

    /**
     * Get address1
     *
     * @return string 
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return IXP
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    
        return $this;
    }

    /**
     * Get address2
     *
     * @return string 
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set address3
     *
     * @param string $address3
     * @return IXP
     */
    public function setAddress3($address3)
    {
        $this->address3 = $address3;
    
        return $this;
    }

    /**
     * Get address3
     *
     * @return string 
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * Set address4
     *
     * @param string $address4
     * @return IXP
     */
    public function setAddress4($address4)
    {
        $this->address4 = $address4;
    
        return $this;
    }

    /**
     * Get address4
     *
     * @return string 
     */
    public function getAddress4()
    {
        return $this->address4;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return IXP
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
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
     * Add Infrastructures
     *
     * @param \Entities\Infrastructure $infrastructures
     * @return IXP
     */
    public function addInfrastructure(\Entities\Infrastructure $infrastructures)
    {
        $this->Infrastructures[] = $infrastructures;
    
        return $this;
    }

    /**
     * Remove Infrastructures
     *
     * @param \Entities\Infrastructure $infrastructures
     */
    public function removeInfrastructure(\Entities\Infrastructure $infrastructures)
    {
        $this->Infrastructures->removeElement($infrastructures);
    }

    /**
     * Get Infrastructures
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInfrastructures()
    {
        return $this->Infrastructures;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Customers;


    /**
     * Add Customers
     *
     * @param \Entities\Customer $customers
     * @return IXP
     */
    public function addCustomer(\Entities\Customer $customers)
    {
        $this->Customers[] = $customers;
    
        return $this;
    }

    /**
     * Remove Customers
     *
     * @param \Entities\Customer $customers
     */
    public function removeCustomer(\Entities\Customer $customers)
    {
        $this->Customers->removeElement($customers);
    }

    /**
     * Get Customers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCustomers()
    {
        return $this->Customers;
    }
}