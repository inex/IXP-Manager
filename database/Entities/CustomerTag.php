<?php

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
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

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

}

