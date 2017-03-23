<?php

namespace Entities;

/**
 * Layer2Address
 */
class Layer2Address
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $mac;

    /**
     * @var \DateTime
     */
    private $firstseen;

    /**
     * @var \DateTime
     */
    private $lastseen;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \Entities\VlanInterface
     */
    private $vlanInterface;


    /**
     * Set mac
     *
     * @param string $mac
     *
     * @return Layer2Address
     */
    public function setMac( $mac )
    {
        $this->mac = $mac;
        return $this;
    }

    /**
     * Set firstseen
     *
     * @param \DateTime $firstSeenAt
     *
     * @return Layer2Address
     */
    public function setFirstSeenAt( $firstSeenAt )
    {
        $this->firstseen = $firstSeenAt;
        return $this;
    }

    /**
     * Set lastseen
     *
     * @param \DateTime $lastSeenAt
     *
     * @return Layer2Address
     */
    public function setLastSeenAt( $lastSeenAt )
    {
        $this->lastseen = $lastSeenAt;
        return $this;
    }

    /**
     * Set created
     *
     * @param \DateTime $createdAt
     *
     * @return Layer2Address
     */
    public function setCreatedAt( $createdAt )
    {
        $this->created = $createdAt;
        return $this;
    }

    /**
     * Set vlanInterface
     *
     * @param \Entities\VlanInterface $vlanInterface
     *
     * @return Layer2Address
     */
    public function setVlanInterface(\Entities\VlanInterface $vlanInterface = null)
    {
        $this->vlanInterface = $vlanInterface;
        return $this;
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
     * Get mac
     *
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * Get firstseen
     *
     * @return \DateTime
     */
    public function getFirstSeenAt()
    {
        return $this->firstseen;
    }

    /**
     * Get lastseen
     *
     * @return \DateTime
     */
    public function getLastSeenAt()
    {
        return $this->lastseen;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created;
    }

    /**
     * Get vlanInterface
     *
     * @return \Entities\VlanInterface
     */
    public function getVlanInterface()
    {
        return $this->vlanInterface;
    }

}

