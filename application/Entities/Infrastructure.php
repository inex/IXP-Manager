<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Infrastructure
 */
class Infrastructure
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $shortname;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\IXP
     */
    private $IXP;


    /**
     * Set name
     *
     * @param string $name
     * @return Infrastructure
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
     * @return Infrastructure
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set IXP
     *
     * @param \Entities\IXP $iXP
     * @return Infrastructure
     */
    public function setIXP(\Entities\IXP $iXP = null)
    {
        $this->IXP = $iXP;
    
        return $this;
    }

    /**
     * Get IXP
     *
     * @return \Entities\IXP 
     */
    public function getIXP()
    {
        return $this->IXP;
    }
}
