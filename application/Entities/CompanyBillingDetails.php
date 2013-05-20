<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyBillingDetails
 */
class CompanyBillingDetails
{
    /**
     * @var string
     */
    private $billingContactName;

    /**
     * @var string
     */
    private $billingAddress1;

    /**
     * @var string
     */
    private $billingAddress2;

    /**
     * @var string
     */
    private $billingTownCity;

    /**
     * @var string
     */
    private $billingPostcode;

    /**
     * @var string
     */
    private $billingCountry;

    /**
     * @var string
     */
    private $billingEmail;

    /**
     * @var string
     */
    private $billingTelephone;

    /**
     * @var string
     */
    private $vatNumber;

    /**
     * @var string
     */
    private $vatRate;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\Customer
     */
    private $Company;


    /**
     * Set billingContactName
     *
     * @param string $billingContactName
     * @return CompanyBillingDetails
     */
    public function setBillingContactName($billingContactName)
    {
        $this->billingContactName = $billingContactName;
    
        return $this;
    }

    /**
     * Get billingContactName
     *
     * @return string 
     */
    public function getBillingContactName()
    {
        return $this->billingContactName;
    }

    /**
     * Set billingAddress1
     *
     * @param string $billingAddress1
     * @return CompanyBillingDetails
     */
    public function setBillingAddress1($billingAddress1)
    {
        $this->billingAddress1 = $billingAddress1;
    
        return $this;
    }

    /**
     * Get billingAddress1
     *
     * @return string 
     */
    public function getBillingAddress1()
    {
        return $this->billingAddress1;
    }

    /**
     * Set billingAddress2
     *
     * @param string $billingAddress2
     * @return CompanyBillingDetails
     */
    public function setBillingAddress2($billingAddress2)
    {
        $this->billingAddress2 = $billingAddress2;
    
        return $this;
    }

    /**
     * Get billingAddress2
     *
     * @return string 
     */
    public function getBillingAddress2()
    {
        return $this->billingAddress2;
    }

    /**
     * Set billingTownCity
     *
     * @param string $billingTownCity
     * @return CompanyBillingDetails
     */
    public function setBillingTownCity($billingTownCity)
    {
        $this->billingTownCity = $billingTownCity;
    
        return $this;
    }

    /**
     * Get billingTownCity
     *
     * @return string 
     */
    public function getBillingTownCity()
    {
        return $this->billingTownCity;
    }

    /**
     * Set billingPostcode
     *
     * @param string $billingPostcode
     * @return CompanyBillingDetails
     */
    public function setBillingPostcode($billingPostcode)
    {
        $this->billingPostcode = $billingPostcode;
    
        return $this;
    }

    /**
     * Get billingPostcode
     *
     * @return string 
     */
    public function getBillingPostcode()
    {
        return $this->billingPostcode;
    }

    /**
     * Set billingCountry
     *
     * @param string $billingCountry
     * @return CompanyBillingDetails
     */
    public function setBillingCountry($billingCountry)
    {
        $this->billingCountry = $billingCountry;
    
        return $this;
    }

    /**
     * Get billingCountry
     *
     * @return string 
     */
    public function getBillingCountry()
    {
        return $this->billingCountry;
    }

    /**
     * Set billingEmail
     *
     * @param string $billingEmail
     * @return CompanyBillingDetails
     */
    public function setBillingEmail($billingEmail)
    {
        $this->billingEmail = $billingEmail;
    
        return $this;
    }

    /**
     * Get billingEmail
     *
     * @return string 
     */
    public function getBillingEmail()
    {
        return $this->billingEmail;
    }

    /**
     * Set billingTelephone
     *
     * @param string $billingTelephone
     * @return CompanyBillingDetails
     */
    public function setBillingTelephone($billingTelephone)
    {
        $this->billingTelephone = $billingTelephone;
    
        return $this;
    }

    /**
     * Get billingTelephone
     *
     * @return string 
     */
    public function getBillingTelephone()
    {
        return $this->billingTelephone;
    }

    /**
     * Set vatNumber
     *
     * @param string $vatNumber
     * @return CompanyBillingDetails
     */
    public function setVatNumber($vatNumber)
    {
        $this->vatNumber = $vatNumber;
    
        return $this;
    }

    /**
     * Get vatNumber
     *
     * @return string 
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * Set vatRate
     *
     * @param string $vatRate
     * @return CompanyBillingDetails
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;
    
        return $this;
    }

    /**
     * Get vatRate
     *
     * @return string 
     */
    public function getVatRate()
    {
        return $this->vatRate;
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
     * @return CompanyBillingDetails
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
