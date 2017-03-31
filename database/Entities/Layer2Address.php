<?php

namespace Entities;

use Carbon\Carbon;

/**
 * Layer2Address
 */
class Layer2Address {
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
     * Get mac formated with comma (xx:xx:xx:xx:xx:xx)
     *
     * @return string
     */
    public function getMacFormatedComma()
    {
        return wordwrap($this->mac, 2, ':',true);
    }

    /**
     * Get mac formated (xxxx.xxxx.xxxx)
     *
     * @return string
     */
    public function getMacFormatedDot()
    {
        return wordwrap($this->mac, 4, '.',true);
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
     * Get created
     *
     * @return \DateTime
     */
    public function getCreatedAtFormated()
    {
        return ($this->getCreatedAt() == null) ? $this->getCreatedAt() : $this->getCreatedAt()->format('Y-m-d');
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
     * Convert this object to an array
     *
     * @return array
     */
    public function toArray(){
        $a = [
            'id'                => $this->getId(),
            'mac'               => $this->getMac() ,
            'macFormatedComma'  => $this->getMacFormatedComma(),
            'macFormatedDot'    => $this->getMacFormatedDot(),
            'vliId'             => $this->getVlanInterface()->getId(),
            'createdAt'         => $this->getCreatedAt(),
            'firstSeenAt'       => $this->getFirstSeenAt(),
            'lastSeenAt'        => $this->getLastSeenAt()
        ];

        return $a;
    }

    /**
     * Get layer@address as JSON-compatibale array
     * @return array
     */
    public function jsonArray( ): array {
        $a = $this->toArray();

        $a['createdAt']     = $a['createdAt']       ? Carbon::instance( $a['createdAt']     )->toIso8601String() : null;
        $a['firstSeenAt']   = $a['firstSeenAt']     ? Carbon::instance( $a['firstSeenAt']   )->toIso8601String() : null;
        $a['lastSeenAt']    = $a['lastSeenAt']      ? Carbon::instance( $a['lastSeenAt']    )->toIso8601String() : null;

        return $a;
    }
}

