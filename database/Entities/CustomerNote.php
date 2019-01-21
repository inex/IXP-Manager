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
 * CustomerNote
 */
class CustomerNote
{
    /**
     * @var boolean
     */
    protected $private;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $note;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $updated;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Entities\Customer
     */
    protected $Customer;


    /**
     * Set private
     *
     * @param boolean $private
     * @return CustomerNote
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    
        return $this;
    }

    /**
     * Get private
     *
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return CustomerNote
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return CustomerNote
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNoteParsedown()
    {
        return @parseDown( $this->note );
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return CustomerNote
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
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
     * @param Customer $customer
     * @return CustomerNote
     */
    public function setCustomer( Customer $customer )
    {
        $this->Customer = $customer;
    
        return $this;
    }

    /**
     * Get Customer
     *
     * @return \Entities\Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }
    

    /**
     * Return the main fields of the note as an array
     * @return array
     */
    public function toArray(): array
    {
        return [
            'created'           => $this->getCreated(),
            'updated'           => $this->getUpdated(),
            'id'                => $this->getId(),
            'note'              => $this->getNote(),
            'noteParsedown'     => @parsedown( $this->getNote() ),
            'private'           => $this->getPrivate(),
            'title'             => $this->getTitle()
        ];
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return CustomerNote
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
