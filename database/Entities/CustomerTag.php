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

/**
 * CustomerTag
 */
class CustomerTag
{
    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $display_as;

    /**
     * @var string
     */
    private $description;

    /**
     * @var bool
     */
    private $internal_only;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $customers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->customers = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     * @return CustomerTag
     */
    public function setTag( string $tag ): CustomerTag
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayAs(): string
    {
        return $this->display_as;
    }

    /**
     * @param string $display_as
     * @return CustomerTag
     */
    public function setDisplayAs( string $display_as ): CustomerTag
    {
        $this->display_as = $display_as;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return CustomerTag
     */
    public function setDescription( $description ): CustomerTag
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInternalOnly(): bool
    {
        return $this->internal_only;
    }

    /**
     * @param bool $internal_only
     * @return CustomerTag
     */
    public function setInternalOnly( bool $internal_only ): CustomerTag
    {
        $this->internal_only = $internal_only;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created
     * @return CustomerTag
     */
    public function setCreated( \DateTime $created ): CustomerTag
    {
        $this->created_at = $created;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated(): \DateTime
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTime $updated
     * @return CustomerTag
     */
    public function setUpdated( \DateTime $updated ): CustomerTag
    {
        $this->updated_at = $updated;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Customer[]
     */
    public function getCustomers(): \Doctrine\Common\Collections\Collection
    {
        return $this->customers;
    }

    /**
     * Add customer
     *
     * @param Customer $customer
     * @return CustomerTag
     */
    public function addCustomer(Customer $customer): CustomerTag
    {
        $this->customers[] = $customer;

        return $this;
    }

    /**
     * Remove customer
     *
     * @param Customer $customer
     */
    public function removeCustomer(Customer $customer)
    {
        $this->customers->removeElement($customer);
    }




}

