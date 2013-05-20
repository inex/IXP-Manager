<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatabaseVersion
 */
class DatabaseVersion
{
    /**
     * @var integer
     */
    private $version;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set version
     *
     * @param integer $version
     * @return DatabaseVersion
     */
    public function setVersion($version)
    {
        $this->version = $version;
    
        return $this;
    }

    /**
     * Get version
     *
     * @return integer 
     */
    public function getVersion()
    {
        return $this->version;
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
