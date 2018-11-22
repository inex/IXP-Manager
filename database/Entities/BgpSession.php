<?php

namespace Entities;

use D2EM;

/**
 * BgpSession
 */
class BgpSession
{
    /**
     * @var integer
     */
    private $srcipaddressid;

    /**
     * @var integer
     */
    private $protocol;

    /**
     * @var integer
     */
    private $dstipaddressid;

    /**
     * @var integer
     */
    private $packetcount = '0';

    /**
     * @var \DateTime
     */
    private $last_seen;

    /**
     * @var string
     */
    private $source;

    /**
     * @var integer
     */
    private $id;


    /**
     * Get the source IP address entity for the appropriate protocol
     *
     * @return IPv4Address|IPv6Address
     */
    public function getSrcIpAddress()
    {
        if( $this->protocol === 4 ) {
            return D2EM::getRepository( 'Entities\IPv4Address' )->find( $this->srcipaddressid );
        } else {
            return D2EM::getRepository( 'Entities\IPv6Address' )->find( $this->srcipaddressid );
        }
    }


}

