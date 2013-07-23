<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyRegisteredDetail
 */
class CompanyRegisteredDetail
{
    /**
     * @var string
     */
    protected $companyNumber;

    /**
     * @var string
     */
    protected $jurisdiction;

    /**
     * @var string
     */
    protected $address1;

    /**
     * @var string
     */
    protected $address2;

    /**
     * @var string
     */
    protected $address3;

    /**
     * @var string
     */
    protected $townCity;

    /**
     * @var string
     */
    protected $postcode;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $Customer;
    
    public function __construct()
    {
        $this->Customer = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set companyNumber
     *
     * @param string $companyNumber
     * @return CompanyRegisteredDetail
     */
    public function setCompanyNumber($companyNumber)
    {
        $this->companyNumber = $companyNumber;
    
        return $this;
    }

    /**
     * Get companyNumber
     *
     * @return string 
     */
    public function getCompanyNumber()
    {
        return $this->companyNumber;
    }

    /**
     * Set jurisdiction
     *
     * @param string $jurisdiction
     * @return CompanyRegisteredDetail
     */
    public function setJurisdiction($jurisdiction)
    {
        $this->jurisdiction = $jurisdiction;
    
        return $this;
    }

    /**
     * Get jurisdiction
     *
     * @return string 
     */
    public function getJurisdiction()
    {
        return $this->jurisdiction;
    }

    /**
     * Set address1
     *
     * @param string $address1
     * @return CompanyRegisteredDetail
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
     * @return CompanyRegisteredDetail
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
     * @return CompanyRegisteredDetail
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
     * Set townCity
     *
     * @param string $townCity
     * @return CompanyRegisteredDetail
     */
    public function setTownCity($townCity)
    {
        $this->townCity = $townCity;
    
        return $this;
    }

    /**
     * Get townCity
     *
     * @return string 
     */
    public function getTownCity()
    {
        return $this->townCity;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return CompanyRegisteredDetail
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    
        return $this;
    }

    /**
     * Get postcode
     *
     * @return string 
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return CompanyRegisteredDetail
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
     * Set Company
     *
     * @param \Entities\Customer $company
     * @return CompanyRegisteredDetail
     */
    public function setCompany(\Entities\Customer $company = null)
    {
        $this->Company = $company;
    
        return $this;
    }

    /**
     * Get Company
     *
     * @return \Entities\Customer 
     */
    public function getCompany()
    {
        return $this->Company;
    }
    /**
     * @var string
     */
    protected $registeredName;


    /**
     * Set registeredName
     *
     * @param string $registeredName
     * @return CompanyRegisteredDetail
     */
    public function setRegisteredName($registeredName)
    {
        $this->registeredName = $registeredName;
    
        return $this;
    }

    /**
     * Get registeredName
     *
     * @return string 
     */
    public function getRegisteredName()
    {
        return $this->registeredName;
    }
    
    /**
     * Add Customer
     *
     * @param Entities\Customer $customer
     * @return CompanyRegisteredDetail
     */
    public function addCustomer(\Entities\Customer $customer)
    {
        $this->Customer[] = $customer;
        return $this;
    }

    /**
     * Remove Customer
     *
     * @param Entities\Customer $customer
     */
    public function removeCustomer(\Entities\Customer $customer)
    {
        $this->Customer->removeElement($customer);
    }

    /**
     * Get Customer
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCustomer()
    {
        return $this->Customer;
    }
}