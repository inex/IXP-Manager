<?php

namespace Entities;

/**
 * CoreInterface
 */
class CoreInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\PhysicalInterface
     */
    private $physicalInterface;

    /**
     * @var \Entities\CoreLink
     */
    private $coreLink;

    /**
     * @var \Entities\CoreLink
     */
    private $coreLink2;


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
     * Get CoreBundle
     *
     * @return \Entities\PhysicalInterface
     */
    public function getPhysicalInterface()
    {
        return $this->physicalInterface;
    }

    /**
     * Get Core Link A
     *
     * @return \Entities\CoreLink
     */
    public function getCoreLinkA()
    {
        return $this->coreLink;
    }

    /**
     * Get CoreLink B
     *
     * @return \Entities\CoreLink
     */
    public function getCoreLinkB()
    {
        return $this->coreLink2;
    }

    /**
     * Check which side has a core link linked
     *
     * @return \Entities\CoreLink
     */
    public function getCoreLink()
    {
        if( $this->getCoreLinkA() ){
            return $this->getCoreLinkA();
        } else {
            return $this->getCoreLinkB();
        }
    }

    /**
     * Set Physical Interface
     *
     * @param \Entities\PhysicalInterface $physicalInterface
     *
     * @return CoreInterface
     */
    public function setPhysicalInterface( PhysicalInterface $physicalInterface = null )
    {
        $this->physicalInterface = $physicalInterface;
        return $this;
    }

    /**
     * Set Core Link A
     *
     * @param \Entities\CoreLink $coreLinkA
     *
     * @return CoreInterface
     */
    public function setCoreLinkA( CoreLink $coreLinkA = null )
    {
        $this->coreLink = $coreLinkA;
        return $this;
    }

    /**
     * Set Core Link B
     *
     * @param \Entities\CoreLink $coreLinkB
     *
     * @return CoreInterface
     */
    public function setCoreLinkB( CoreLink $coreLinkB = null )
    {
        $this->coreLink2 = $coreLinkB;
        return $this;
    }

}

