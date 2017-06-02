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
    private $ipv4Subnet;

    /**
     * @var string
     */
    private $ipv6Subnet;

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


}

