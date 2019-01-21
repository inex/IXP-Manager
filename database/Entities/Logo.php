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
     * Logo
     */
    class Logo
    {
    /**
     * @var string
     */
    private $original_name;

    /**
     * @var string
     */
    private $stored_name;

    /**
     * @var string
     */
    private $uploaded_by;

    /**
     * @var \DateTime
     */
    private $uploaded_at;

    /**
     * @var integer
     */
    private $width;

    /**
     * @var integer
     */
    private $height;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\Customer
     */
    private $customer;

    /**
     * @var string
     */
    private $type;

    /**
     * Tyoe for display on public website
     */
    const TYPE_WWW80 = 'WWW80';


    /**
     * Get originalName
     *
     * @return string
     */
    public function getOriginalName(){
        return $this->original_name;
    }

    /**
     * Get storedName
     *
     * @return string
     */
    public function getStoredName(){
        return $this->stored_name;
    }

    /**
     * Get uploadedBy
     *
     * @return string
     */
    public function getUploadedBy(){
        return $this->uploaded_by;
    }

    /**
     * Get uploadedAt
     *
     * @return \DateTime
     */
    public function getUploadedAt(){
        return $this->uploaded_at;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth(){
        return $this->width;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight(){
        return $this->height;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Get customer
     *
     * @return \Entities\Customer
     */
    public function getCustomer(){
        return $this->customer;
    }


    /**
     * Creates a hierarchy directory structure to shard image storage
     *
     * @return string the/sharded/path/filename
     */
    public function getShardedPath() {
        return substr($this->getStoredName(), 0, 1) . '/' . substr($this->getStoredName(), 1, 1) . '/' . $this->getStoredName();
    }

    /**
     * Get the full path of the a logo
     *
     * @return string the/full/path/filename
     */
    public function getFullPath() {
        return public_path().'/logos/' . $this->getShardedPath();
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType(){
        return $this->type;
    }

    /**
     * Set originalName
     *
     * @param string $originalName
     *
     * @return Logo
     */
    public function setOriginalName( $originalName ){
        $this->original_name = $originalName;
        return $this;
    }

    /**
     * Set storedName
     *
     * @param string $storedName
     *
     * @return Logo
     */
    public function setStoredName( $storedName ){
        $this->stored_name = $storedName;
        return $this;
    }

    /**
     * Set uploadedBy
     *
     * @param string $uploadedBy
     *
     * @return Logo
     */
    public function setUploadedBy( $uploadedBy ){
        $this->uploaded_by = $uploadedBy;
        return $this;
    }

    /**
     * Set uploadedAt
     *
     * @param \DateTime $uploadedAt
     *
     * @return Logo
     */
    public function setUploadedAt( $uploadedAt ){
        $this->uploaded_at = $uploadedAt;

        return $this;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return Logo
     */
    public function setWidth( $width ){
    $this->width = $width;

    return $this;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return Logo
     */
    public function setHeight( $height ){
        $this->height = $height;
        return $this;
    }

    /**
     * Set customer
     *
     * @param \Entities\Customer $customer
     *
     * @return Logo
     */
    public function setCustomer( \Entities\Customer $customer = null ){
        $this->customer = $customer;
        return $this;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Logo
     */
    public function setType( $type ){
        $this->type = $type;

        return $this;
    }
}
