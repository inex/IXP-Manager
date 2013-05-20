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
    private $companyNumber;

    /**
     * @var string
     */
    private $jurisidiction;

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
    private $towncity;

    /**
     * @var string
     */
    private $postcode;

    /**
     * @var string
     */
    private $country;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\Customer
     */
    private $Company;


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
     * Set jurisidiction
     *
     * @param string $jurisidiction
     * @return CompanyRegisteredDetail
     */
    public function setJurisidiction($jurisidiction)
    {
        $this->jurisidiction = $jurisidiction;
    
        return $this;
    }

    /**
     * Get jurisidiction
     *
     * @return string 
     */
    public function getJurisidiction()
    {
        return $this->jurisidiction;
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
     * Set towncity
     *
     * @param string $towncity
     * @return CompanyRegisteredDetail
     */
    public function setTowncity($towncity)
    {
        $this->towncity = $towncity;
    
        return $this;
    }

    /**
     * Get towncity
     *
     * @return string 
     */
    public function getTowncity()
    {
        return $this->towncity;
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
}
