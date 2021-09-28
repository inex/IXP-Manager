<?php /** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);
namespace IXP\Tasks\Switch;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\Switcher;

use IXP\Models\Vlan;
use OSS_SNMP\SNMP;


/**
 * TrawlFdb
 *
 * @author     Nick Hilliard   <nick@inex.ie>
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Switch
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class FdbTrawl
{

    # ifDescr
    const OID_IFDESCR  = '.1.3.6.1.2.1.2.2.1.2';

    # dot1dBasePortIfIndex
    const OID_DOT1DBASEPORTIFINDEX  = '.1.3.6.1.2.1.17.1.4.1.2';

    # dot1qVlanFdbId
    const OID_DOT1QVLANFDBID        = '.1.3.6.1.2.1.17.7.1.4.2.1.3';

    # dot1qTpFdbPort
    const OID_DOT1QTPFDBPORT        = '.1.3.6.1.2.1.17.7.1.2.2.1.2';

    # dot1dTpFdbPort
    const OID_DOT1DTPFDBPORT        = '.1.3.6.1.2.1.17.4.3.1.2';

    # dot1dTpFdbAddress
    const OID_DOT1DTPFDBADDRESS     = '.1.3.6.1.2.1.17.4.3.1.1';

    # jnxExVlanTag
    const OID_JNXEXVLANTAG          = '.1.3.6.1.4.1.2636.3.40.1.5.1.5.1.5';

    # jnxL2aldVlanTag
    const OID_JNXL2ALDVLANTAG       = '.1.3.6.1.4.1.2636.3.48.1.3.1.1.3';

    # jnxL2aldVlanFdbId
    const OID_JNXL2ALDVLANFDBID     = '.1.3.6.1.4.1.2636.3.48.1.3.1.1.5';


    /**
     * @param array $logs
     */
    public array $logs = [];

    private SNMP $snmp;

    public function __construct( public Switcher $sw, public Vlan $vlan )
    {
        // get an OSS_SNMP object for the switch
        $this->snmp = new SNMP( $sw->hostname, $sw->snmppasswd );
    }


    /**
     * Trawl the switch's FDB table.
     *
     * Imported from a PoC from Nick.
     *
     * @return array|void
     * @throws
     */
    public function trawl()
    {
        $oids = array (
            'dot1qVlanFdbId'        => '.1.3.6.1.2.1.17.7.1.4.2.1.3',
            'dot1qTpFdbPort'        => '.1.3.6.1.2.1.17.7.1.2.2.1.2',
            'dot1dTpFdbPort'        => '.1.3.6.1.2.1.17.4.3.1.2',
            'dot1dTpFdbAddress'     => '.1.3.6.1.2.1.17.4.3.1.1',
            'jnxExVlanTag'          => '.1.3.6.1.4.1.2636.3.40.1.5.1.5.1.5',
            'jnxL2aldVlanTag'       => '.1.3.6.1.4.1.2636.3.48.1.3.1.1.3',
            'jnxL2aldVlanFdbId'     => '.1.3.6.1.4.1.2636.3.48.1.3.1.1.5',
        );

        logger( "FDBTRAWL: {$this->sw->name}: started query process" );

        $sysdescr = $this->snmp->useSystem()->description();

        if (preg_match('/Cisco\s+(NX-OS|IOS)/', $sysdescr)) {
            logger()->warning( "FDBTRAWL: {$this->sw->name}: using community\@vlan hack to handle broken SNMP implementation" );
            $this->snmp->setCommunity( $this->snmp->getCommunity() . '@' . $this->vlan->number );
        }

        $ifindex = $this->snmp->useIface()->descriptions();

        if (!$ifindex) {
            logger()->warning( "FDBTRAWL: {$this->sw->name}: cannot read ifDescr. Not processing {$this->sw->name} further." );
            return;
        }

        $interfaces = $this->snmp->useBridge()->basePortIfIndexes();
        if (!$interfaces) {
            logger()->warning( "FDBTRAWL: {$this->sw->name}: cannot read dot1dBasePortIfIndex. Not processing {$this->sw->name} further." );
            return;
        }

        $vlanmapping = null;

        # if jnxExVlanTag returns something, then this is a juniper and we need to
        # handle the interface mapping separately on these boxes
        logger( "FDBTRAWL: {$this->sw->name}: pre-emptively trying Juniper jnxExVlanTag to see if we're on a J-EX box (" . $oids['jnxExVlanTag'] . ")" );
        $juniperexmapping = false;
        try { $vlanmapping = $this->snmp->walk1d($oids['jnxExVlanTag']); } catch( \Exception $e ) {}
        if ( $vlanmapping ) {
            $juniperexmapping = true;
            logger( "FDBTRAWL: {$this->sw->name}: looks like this is a Juniper EX" );
        } else {
            logger( "FDBTRAWL: {$this->sw->name}: this isn't a Juniper EX" );
        }

        if (!$vlanmapping) {
            # Juniper KB32532:
            #
            # We start out with two arrays, jnxL2aldVlanTag and jnxL2aldVlanFdbId.  We need to
            # end up with a mapping from the value of jnxL2aldVlanFdbId pointing to the
            # value of jnxL2aldVlanTag.
            #
            # jnxL2aldVlanTag.3 = 1
            # jnxL2aldVlanTag.4 = 10
            # jnxL2aldVlanTag.5 = 20
            # jnxL2aldVlanFdbId.3 = 196608
            # jnxL2aldVlanFdbId.4 = 262144
            # jnxL2aldVlanFdbId.5 = 327680
            #
            # This gets mapped to
            # array (
            #	196608 => 1,
            #	262144 => 10,
            #	327680 => 20
            # )
            $jnxL2aldvlantag = false;
            try { $jnxL2aldvlantag = $this->snmp->walk1d($oids['jnxL2aldVlanTag']); } catch( \Exception $e ) {}
            if ($jnxL2aldvlantag) {
                logger( "FDBTRAWL: {$this->sw->name}: looks like this is a Juniper EX running an ELS image" );
                $jnxL2aldvlanid = $this->snmp->walk1d($oids['jnxL2aldVlanFdbId']);

                foreach (array_keys($jnxL2aldvlantag) as $index) {
                    $vlanmapping[$jnxL2aldvlanid[$index]] = $jnxL2aldvlantag[$index];
                }

                if (!$vlanmapping) {
                    logger()->warning( "FDBTRAWL: {$this->sw->name}: Juniper ELS image detected but VLAN mapping retrieval failed. Not processing {$this->sw->name} further." );
                    return;
                }
            } else {
                logger( "FDBTRAWL: {$this->sw->name}: this isn't a Juniper running an ELS image" );
            }
        }

        # attempt to use Q-BRIDGE-MIB.
        logger( "FDBTRAWL: {$this->sw->name}: attempting to retrieve dot1qVlanFdbId mapping (".$oids['dot1qVlanFdbId'].")" );

        if (!$vlanmapping) {
            try { $vlanmapping = $this->snmp->walk1d($oids['dot1qVlanFdbId'].".0"); } catch( \Exception $e ) {}
        }

        # At this stage we should have a dot1qVlanFdbId mapping, but
        # some switches don't support it (e.g.  Dell F10-S4810), so
        # if it doesn't exist we'll attempt Q-BRIDGE-MIB with the
        # VLAN IDs instead of mapped IDs.

        if ($vlanmapping) {    # if this fails too, Q-BRIDGE-MIB is out
            $vlan2idx = $this->array_reverse ($vlanmapping);
            $vlanid = $vlan2idx[$vlan];
            if ($debug) { print "DEBUG: $host: got mapping index: $vlan maps to $vlanid\n"; }
        } else {
            if ($debug) { print "DEBUG: $host: that didn't work either. attempting Q-BRIDGE-MIB with no fdb->ifIndex mapping\n"; }
            $vlanid = $vlan;
        }

        if ($debug) { print "DEBUG: $host: attempting Q-BRIDGE-MIB (".$oids['dot1qTpFdbPort'].".$vlanid)\n"; }
        $qbridgehash = $this->snmpwalk2hash($oids['dot1qTpFdbPort'].".".$vlanid, [$this, 'oid2mac'], false, false);

        if ($qbridgehash) {
            if ($debug) { print "DEBUG: $host: Q-BRIDGE-MIB query successful\n"; }
        } else {
            if ($debug) { print "DEBUG: $host: dot1qTpFdbPort.$vlanid failed - attempting baseline dot1qTpFdbPort subtree walk in desperation\n"; }

            # some stacks (e.g.  Comware) don't support mib walk for
            # dot1qTpFdbPort.$vlanid, so we'll attempt dot1qTpFdbPort instead, then
            # filter out all the unwanted entries.  This is inefficient and unusual, so
            # it's the last option attempted.

            $qbridgehash = $this->snmpwalk2hash($oids['dot1qTpFdbPort'], [$this, 'oid2mac'], false, $vlanid);

            if ($qbridgehash) {
                if ($debug) { print "DEBUG: $host: Q-BRIDGE-MIB query ".($qbridgehash ? "successful" : "failed")."\n"; }
            }
            if ($debug) { print "DEBUG: $host: failed to retrieve Q-BRIDGE-MIB. falling back to BRIDGE-MIB\n"; }
        }

        # special case: when the vlan is not specified, juniper EX boxes
        # return data on Q-BRIDGE-MIB rather than BRIDGE-MIB
        if (!$vlan && $juniperexmapping) {
            if ($debug) { print "DEBUG: $host: attempting special Juniper EX Q-BRIDGE-MIB query for unspecified vlan\n"; }
            $qbridgehash = $this->snmpwalk2hash($oids['dot1qTpFdbPort'], [$this, 'oid2mac'], false);
            if ($debug) {
                if ($qbridgehash) {
                    print "DEBUG: $host: Juniper EX Q-BRIDGE-MIB query successful\n";
                } else {
                    print "DEBUG: $host: failed Juniper EX Q-BRIDGE-MIB retrieval\n";
                }
            }
        }

        # if vlan wasn't specified or there's nothing coming in from the
        # Q-BRIDGE mib, then use rfc1493 BRIDGE-MIB.
        if (($vlan && !$qbridgehash) || (!$vlan && !$juniperexmapping)) {
            if ($debug) { print "DEBUG: $host: attempting BRIDGE-MIB (".$oids['dot1dTpFdbPort'].")\n"; }
            $dbridgehash = $this->snmpwalk2hash($oids['dot1dTpFdbPort'], false, false, false);
            if ($debug and $dbridgehash) { print "DEBUG: $host: BRIDGE-MIB query successful\n"; }
        }

        # if this isn't supported, then panic.  We could probably try
        # community@vlan syntax, but this should be good enough.
        if (!$qbridgehash && !$dbridgehash) {
            print "WARNING: $host: cannot read BRIDGE-MIB or Q-BRIDGE-MIB. Not processing $host further.\n";
            return;
        }

        if ($dbridgehash) {
            $bridgehash2mac = $this->snmpwalk2hash($oids['dot1dTpFdbAddress'], false, [$this, 'normalize_mac'], false);
            $bridgehash = $dbridgehash;
            $maptable = $bridgehash2mac;
        } else {
            $bridgehash = $qbridgehash;
            $maptable = $qbridgehash;
        }

        foreach (array_keys($maptable) as $entry) {
            if (isset($ifindex[$interfaces[$bridgehash[$entry]]])) {
                $int = $ifindex[$interfaces[$bridgehash[$entry]]];
                if ($juniperexmapping && preg_match('/\.\d+$/', $int)) {
                    $int = preg_replace('/(\.\d+)$/','', $int);
                }
                if ($dbridgehash) {
                    $entry = $bridgehash2mac[$entry];
                }
                $macaddr[$int][] = $entry;
            }
        }

        return [];

    }

    /**
     * Gets the ifIndex to ifDescr array
     *
     * E.g.:
     *    [] => Array
     *        (
     *            [1] => 'Ethernet1',
     *            [2] => 'Ethernet1'
     *        )
     *
     * @return array associative array of ifIndexes pointing to the ifDescr entry
     */
    private function getIfDescr(): array
    {
        return $this->snmp->walk1d(self::OID_IFDESCR);
    }

    /**
     * Gets the ifIndex to dot1d baseport array
     *
     * E.g.:
     *    [] => Array
     *        (
     *            [1] => 1001,
     *            [2] => 1002
     *        )
     *
     * @return array Associate array of ifIndex pointing to their associated dot1dBasePort entry
     */
    public function getBasePortIfIndex(): array
    {
        return $this->snmp->walk1d(self::OID_DOT1DBASEPORTIFINDEX);
    }


    /**
     * Gets the vlanIndex to dot1d vlan tag array
     *
     * E.g.:
     *    [] => Array
     *        (
     *            [10] => 10,
     *            [30] => 30,
     *            [70] => 70
     *        )
     *
     * @return array Associate array of vlanIndex pointing to their associated dot1d vlan tag
     */
    public function getVlanMapping(): array
    {
        try {
            $foo = $this->snmp->walk1d(self::OID_DOT1QVLANFDBID.".0");
        } catch (\Exception $e) {
            $foo = [];
        }

        return $foo;
    }


    public function snmpwalk2hash ($queryoid, $keycallback, $valuecallback, $keyfilter)
    {

        $index = count(explode ('.', $queryoid));
        try {
            $resultarray = $this->snmp->subOidWalk($queryoid, $index, -1);
        } catch (\Exception $e) {
        }

        if (!$resultarray) {
            return false;
        }

        foreach (array_keys($resultarray) as $returnoid) {

            $descr = $resultarray[$returnoid];

            # ignore all returned OIDs except those starting with $keyfilter
            if ($keyfilter) {
                if (!preg_match('/^($keyfilter)\./', $returnoid)) {
                    $returnoid = preg_replace('/^($keyfilter)\./', '', $returnoid);
                    continue;
                }
            }

            if ($keycallback) {
                $returnoid = $keycallback($returnoid);
            }
            if ($valuecallback) {
                $descr = $valuecallback($descr);
            }

            $returnhash[$returnoid] = $descr;
        }

        return ($returnhash);
    }

}
