<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Entities;

use Countries;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var Customer
     */
    protected $Customer;
    
    public function __construct()
    {

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
     * Get billingCountry
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->country ? array_column( Countries::getList(), 'name', 'iso_3166_2')[ $this->getCountry() ] : null;
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
     * Set Customer
     *
     * @param Customer $customer
     * @return CompanyRegisteredDetail
     */
    public function setCustomer(Customer $customer)
    {
        $this->Customer[] = $customer;
        return $this;
    }

    /**
     * Get Customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }
}
