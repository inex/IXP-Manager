<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 *
 * Auto-generated Doctrine ORM File
 *
 * @category ORM
 * @package IXP_ORM_Models
 * @copyright Copyright 2008 - 2010 Internet Neutral Exchange Association Limited <info (at) inex.ie>
 * @author Barry O'Donovan <barryo (at) inex.ie>
 */
class BgpsessiondataTable extends Doctrine_Table
{

    /**
     * Get all BGP peers for all peers (or a specific ASN)
     * @param int $vlan The VLAN tag of the LAN to query
     * @param int $protocol The IP protocol to query
     * @param int|null $asn Optional ASN to query peers for
     * @return array Array of peerings.
     */
    public static function getPeers( $vlan, $protocol, $asn = null )
    {
        $q = Doctrine_Query::create()
                ->select( 'bs.*,
                        vlis.virtualinterfaceid, vis.custid,
                        cs.shortname, cs.name, cs.autsys,
                        vlid.virtualinterfaceid, vid.custid,
                        cd.shortname, cd.name, cd.autsys,
                        COUNT( bs.packetcount ) AS packetcount' ) //, MAX( bs.timestamp ) AS ts' )
                ->from( 'Bgpsessiondata bs' )
                ->leftJoin( 'bs.Src_Vlaninterface vlis' )
                ->leftJoin( 'vlis.Virtualinterface vis' )
                ->leftJoin( 'vis.Cust cs' )
                ->leftJoin( 'bs.Dst_Vlaninterface vlid' )
                ->leftJoin( 'vlid.Virtualinterface vid' )
                ->leftJoin( 'vid.Cust cd' );

        $q->andWhere( 'bs.vlan = ?', $vlan );
        $q->andWhere( 'bs.protocol = ?', $protocol );

        if( $asn !== null )
            $q->andWhere( 'cs.autsys = ?', $asn );

        $q->andWhere( 'packetcount >= 1' );

        $q->groupBy( 'bs.srcipaddressid, bs.dstipaddressid' );

        $peers = $q->execute( null, Doctrine::HYDRATE_ARRAY );

        $apeers = array();

        foreach( $peers as $p )
        {
            $s = $p['Src_Vlaninterface']['Virtualinterface']['Cust'];
            $d = $p['Dst_Vlaninterface']['Virtualinterface']['Cust'];

            if( !isset( $apeers[$s['autsys']] ) )
            {
                $apeers[$s['autsys']] = array();
                $apeers[$s['autsys']]['shortname'] = $s['shortname'];
                $apeers[$s['autsys']]['name']      = $s['name'];
                $apeers[$s['autsys']]['peers']     = array();
            }

            $apeers[$s['autsys']]['peers'][ $d['autsys'] ] = $d['autsys'];
        }

        ksort( $apeers, SORT_NUMERIC );

        foreach( $apeers as $asn => $p )
            ksort( $apeers[ $asn ][ 'peers' ], SORT_NUMERIC );

        return $apeers;
    }
}