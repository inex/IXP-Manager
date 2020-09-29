<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Entities;

use D2EM;

/**
 * BgpSession
 */
class BgpSession
{
    private $created_at;
    private $updated_at;
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

