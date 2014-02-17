<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * OUI
 */
class OUI
{
    /**
     * @var string
     */
    private $oui;

    /**
     * @var string
     */
    private $organisation;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set oui
     *
     * @param string $oui
     * @return OUI
     */
    public function setOui($oui)
    {
        $this->oui = $oui;
    
        return $this;
    }

    /**
     * Get oui
     *
     * @return string 
     */
    public function getOui()
    {
        return $this->oui;
    }

    /**
     * Set organisation
     *
     * @param string $organisation
     * @return OUI
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    
        return $this;
    }

    /**
     * Get organisation
     *
     * @return string 
     */
    public function getOrganisation()
    {
        return $this->organisation;
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
}