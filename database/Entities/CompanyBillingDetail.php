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
 * CompanyBillingDetail
 */
class CompanyBillingDetail
{
    
    const INVOICE_METHOD_EMAIL = 'EMAIL';
    const INVOICE_METHOD_POST  = 'POST';

    public static $INVOICE_METHODS = [
        self::INVOICE_METHOD_EMAIL => 'Email',
        self::INVOICE_METHOD_POST  => 'Post'
    ];

    const BILLING_FREQUENCY_MONTHLY    = 'MONTHLY';
    const BILLING_FREQUENCY_2MONTHLY   = '2MONTHLY';
    const BILLING_FREQUENCY_QUARTERLY  = 'QUARTERLY';
    const BILLING_FREQUENCY_HALFYEARLY = 'HALFYEARLY';
    const BILLING_FREQUENCY_ANNUALLY   = 'ANNUALLY';
    const BILLING_FREQUENCY_NOBILLING  = 'NOBILLING';
    
    public static $BILLING_FREQUENCIES = [
        self::BILLING_FREQUENCY_MONTHLY    => 'Monthly',
        self::BILLING_FREQUENCY_2MONTHLY   => 'Every 2 Months',
        self::BILLING_FREQUENCY_QUARTERLY  => 'Quarterly',
        self::BILLING_FREQUENCY_HALFYEARLY => 'Half-Yearly',
        self::BILLING_FREQUENCY_ANNUALLY   => 'Annually',
        self::BILLING_FREQUENCY_NOBILLING  => 'No Billing'
    ];

    /**
     * @var string
     */
    protected $billingContactName;

    /**
     * @var string
     */
    protected $billingAddress1;

    /**
     * @var string
     */
    protected $billingAddress2;

    /**
     * @var string
     */
    protected $billingTownCity;

    /**
     * @var string
     */
    protected $billingPostcode;

    /**
     * @var string
     */
    protected $billingCountry;

    /**
     * @var string
     */
    protected $billingEmail;

    /**
     * @var string
     */
    protected $billingTelephone;

    /**
     * @var string
     */
    protected $vatNumber;

    /**
     * @var string
     */
    protected $vatRate;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Customer
     */
    protected $Customer;

    /**
     * @var boolean
     */
    protected $purchaseOrderRequired;

    /**
     * @var string
     */
    protected $invoiceMethod;

    /**
     * @var string
     */
    protected $invoiceEmail;

    /**
     * @var string
     */
    protected $billingFrequency;
    
    public function __construct()
    {

    }

    /**
     * Set billingContactName
     *
     * @param string $billingContactName
     * @return CompanyBillingDetail
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
     * @return CompanyBillingDetail
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
     * @return CompanyBillingDetail
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
     * @return CompanyBillingDetail
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
     * @return CompanyBillingDetail
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
     * @return CompanyBillingDetail
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
     * Get billingCountry
     *
     * @return string
     */
    public function getBillingCountryName()
    {
        $index = null;
        foreach( Countries::getList() as $i => $value ){
            if( $value[ 'iso_3166_2' ] == $this->getBillingCountry() ){
                $index = $i;

            }
        }

        if( $index == null ){
            return null;
        } else{
            return Countries::getList()[ $index ][ 'name' ];
        }

    }

    /**
     * Set billingEmail
     *
     * @param string $billingEmail
     * @return CompanyBillingDetail
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
     * @return CompanyBillingDetail
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
     * @return CompanyBillingDetail
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
     * @return CompanyBillingDetail
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
     * Add Customer
     *
     * @param Customer $customer
     * @return CompanyBillingDetail
     */
    public function setCustomer( Customer $customer)
    {
        $this->Customer = $customer;
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

    /**
     * Set purchaseOrderRequired
     *
     * @param boolean $purchaseOrderRequired
     * @return CompanyBillingDetail
     */
    public function setPurchaseOrderRequired($purchaseOrderRequired)
    {
        $this->purchaseOrderRequired = $purchaseOrderRequired;
    
        return $this;
    }

    /**
     * Get purchaseOrderRequired
     *
     * @return boolean 
     */
    public function getPurchaseOrderRequired()
    {
        return $this->purchaseOrderRequired;
    }

    /**
     * Set invoiceMethod
     *
     * @param string $invoiceMethod
     * @return CompanyBillingDetail
     */
    public function setInvoiceMethod($invoiceMethod)
    {
        $this->invoiceMethod = $invoiceMethod;
    
        return $this;
    }

    /**
     * Get invoiceMethod
     *
     * @return string 
     */
    public function getInvoiceMethod()
    {
        return $this->invoiceMethod;
    }

    /**
     * Set invoiceEmail
     *
     * @param string $invoiceEmail
     * @return CompanyBillingDetail
     */
    public function setInvoiceEmail($invoiceEmail)
    {
        $this->invoiceEmail = $invoiceEmail;
    
        return $this;
    }

    /**
     * Get invoiceEmail
     *
     * @return string 
     */
    public function getInvoiceEmail()
    {
        return $this->invoiceEmail;
    }

    /**
     * Set billingFrequency
     *
     * @param string $billingFrequency
     * @return CompanyBillingDetail
     */
    public function setBillingFrequency($billingFrequency)
    {
        $this->billingFrequency = $billingFrequency;
    
        return $this;
    }

    /**
     * Get billingFrequency
     *
     * @return string 
     */
    public function getBillingFrequency()
    {
        return $this->billingFrequency;
    }
    /**
     * @var string
     */
    private $billingAddress3;


    /**
     * Set billingAddress3
     *
     * @param string $billingAddress3
     * @return CompanyBillingDetail
     */
    public function setBillingAddress3($billingAddress3)
    {
        $this->billingAddress3 = $billingAddress3;
    
        return $this;
    }

    /**
     * Get billingAddress3
     *
     * @return string 
     */
    public function getBillingAddress3()
    {
        return $this->billingAddress3;
    }


    public function getFrequencyAsNumMonths(): int
    {
        switch( $this->getBillingFrequency() ) {

            case self::BILLING_FREQUENCY_MONTHLY:
                return 1;

            case self::BILLING_FREQUENCY_2MONTHLY:
                return 2;

            case self::BILLING_FREQUENCY_QUARTERLY:
                return 3;

            case self::BILLING_FREQUENCY_HALFYEARLY:
                return 6;

            case self::BILLING_FREQUENCY_ANNUALLY:
                return 12;

            case self::BILLING_FREQUENCY_NOBILLING:
                return 0;

            default:
                die('huh? no such billing frequency');

        }
    }
}
