<?php

namespace Entities;

/**
 * CoreLink
 */
class CoreLink
{
    /**
     * @var boolean
     */
    private $bfd = '0';

    /**
     * @var boolean
     */
    private $enabled = '0';

    /**
     * @var string
     */
    private $ipv4_subnet;

    /**
     * @var string
     */
    private $ipv6_subnet;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\CoreInterface
     */
    private $coreInterfaceSideA;

    /**
     * @var \Entities\CoreInterface
     */
    private $coreInterfaceSideB;

    /**
     * @var \Entities\CoreBundle
     */
    private $coreBundle;


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
     * Get bfd
     *
     * @return boolean
     */
    public function getBFD()
    {
        return $this->bfd;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get IPv4 Subnet
     *
     * @return boolean
     */
    public function getIPv4Subnet()
    {
        return $this->ipv4_subnet;
    }

    /**
     * Get IPv6 Subnet
     *
     * @return boolean
     */
    public function getIPv46Subnet()
    {
        return $this->ipv6_subnet;
    }


    /**
     * Get CoreInterface side A
     *
     * @return \Entities\CoreInterface
     */
    public function getCoreInterfaceSideA()
    {
        return $this->coreInterfaceSideA;
    }

    /**
     * Get CoreInterface side B
     *
     * @return \Entities\CoreInterface
     */
    public function getCoreInterfaceSideB()
    {
        return $this->coreInterfaceSideB;
    }


    /**
     * Get CoreBundle
     *
     * @return \Entities\CoreBundle
     */
    public function getCoreBundle()
    {
        return $this->coreBundle;
    }



    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return CoreBundle
     */
    public function setEnabled( $enabled )
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Set BFD
     *
     * @param boolean $bfd
     *
     * @return CoreBundle
     */
    public function setBFD( $bfd )
    {
        $this->bfd = $bfd;
        return $this;
    }

    /**
     * Set IPv4 Subnet
     *
     * @param string $ipv4_subnet
     *
     * @return CoreBundle
     */
    public function setIPv4Subnet( $ipv4_subnet )
    {
        $this->ipv4_subnet = $ipv4_subnet;
        return $this;
    }

    /**
     * Set IPv6 Subnet
     *
     * @param string $ipv6_subnet
     *
     * @return CoreBundle
     */
    public function setIPv6Subnet( $ipv6_subnet )
    {
        $this->ipv6_subnet = $ipv6_subnet;
        return $this;
    }


    /**
     * Set CoreInterface side A
     *
     * @param \Entities\CoreInterface $coreInterfaceSideA
     *
     * @return CoreLink
     */
    public function setCoreInterfaceSideA( CoreInterface $coreInterfaceSideA = null)
    {
        $this->coreInterfaceSideA = $coreInterfaceSideA;
        return $this;
    }

    /**
     * Set CoreInterface side B
     *
     * @param \Entities\CoreInterface $coreInterfaceSideB
     *
     * @return CoreLink
     */
    public function setCoreInterfaceSideB( CoreInterface $coreInterfaceSideB = null )
    {
        $this->coreInterfaceSideB = $coreInterfaceSideB;
        return $this;
    }

    /**
     * Set CoreBundle
     *
     * @param \Entities\CoreBundle $coreBundle
     *
     * @return CoreLink
     */
    public function setCoreBundle( CoreBundle $coreBundle = null )
    {
        $this->coreBundle = $coreBundle;
        return $this;
    }

}

