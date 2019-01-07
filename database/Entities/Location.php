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
 * Entities\Location
 */
class Location
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $shortname
     */
    protected $shortname;

    /**
     * @var string $tag
     */
    protected $tag;

    /**
     * @var string $address
     */
    protected $address;

    /**
     * @var string $nocphone
     */
    protected $nocphone;

    /**
     * @var string $nocfax
     */
    protected $nocfax;

    /**
     * @var string $nocemail
     */
    protected $nocemail;

    /**
     * @var string $officephone
     */
    protected $officephone;

    /**
     * @var string $officefax
     */
    protected $officefax;

    /**
     * @var string $officeemail
     */
    protected $officeemail;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $Cabinets;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Cabinets = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Location
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
     * @return Location
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
     * Set tag
     *
     * @param string $tag
     * @return Location
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Location
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set nocphone
     *
     * @param string $nocphone
     * @return Location
     */
    public function setNocphone($nocphone)
    {
        $this->nocphone = $nocphone;
    
        return $this;
    }

    /**
     * Get nocphone
     *
     * @return string 
     */
    public function getNocphone()
    {
        return $this->nocphone;
    }

    /**
     * Set nocfax
     *
     * @param string $nocfax
     * @return Location
     */
    public function setNocfax($nocfax)
    {
        $this->nocfax = $nocfax;
    
        return $this;
    }

    /**
     * Get nocfax
     *
     * @return string 
     */
    public function getNocfax()
    {
        return $this->nocfax;
    }

    /**
     * Set nocemail
     *
     * @param string $nocemail
     * @return Location
     */
    public function setNocemail($nocemail)
    {
        $this->nocemail = $nocemail;
    
        return $this;
    }

    /**
     * Get nocemail
     *
     * @return string 
     */
    public function getNocemail()
    {
        return $this->nocemail;
    }

    /**
     * Set officephone
     *
     * @param string $officephone
     * @return Location
     */
    public function setOfficephone($officephone)
    {
        $this->officephone = $officephone;
    
        return $this;
    }

    /**
     * Get officephone
     *
     * @return string 
     */
    public function getOfficephone()
    {
        return $this->officephone;
    }

    /**
     * Set officefax
     *
     * @param string $officefax
     * @return Location
     */
    public function setOfficefax($officefax)
    {
        $this->officefax = $officefax;
    
        return $this;
    }

    /**
     * Get officefax
     *
     * @return string 
     */
    public function getOfficefax()
    {
        return $this->officefax;
    }

    /**
     * Set officeemail
     *
     * @param string $officeemail
     * @return Location
     */
    public function setOfficeemail($officeemail)
    {
        $this->officeemail = $officeemail;
    
        return $this;
    }

    /**
     * Get officeemail
     *
     * @return string 
     */
    public function getOfficeemail()
    {
        return $this->officeemail;
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
     * Add Cabinets
     *
     * @param Entities\Cabinet $cabinets
     * @return Location
     */
    public function addCabinet( \Entities\Cabinet $cabinets)
    {
        $this->Cabinets[] = $cabinets;
    
        return $this;
    }

    /**
     * Remove Cabinets
     *
     * @param Entities\Cabinet $cabinets
     */
    public function removeCabinet(\Entities\Cabinet $cabinets)
    {
        $this->Cabinets->removeElement($cabinets);
    }

    /**
     * Get Cabinets
     *
     * @return Doctrine\Common\Collections\Collection|\Doctrine\Common\Collections\Collection|array
     */
    public function getCabinets()
    {
        return $this->Cabinets;
    }
    /**
     * @var string $notes
     */
    protected $notes;


    /**
     * Set notes
     *
     * @param string $notes
     * @return Location
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    
        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }
    /**
     * @var integer
     */
    private $pdb_facility_id;


    /**
     * Set pdb_facility_id
     *
     * @param integer $pdbFacilityId
     * @return Location
     */
    public function setPdbFacilityId($pdbFacilityId)
    {
        $this->pdb_facility_id = $pdbFacilityId;

        return $this;
    }

    /**
     * Get pdb_facility_id
     *
     * @return integer 
     */
    public function getPdbFacilityId()
    {
        return $this->pdb_facility_id;
    }
}
