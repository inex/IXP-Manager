<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * Controller: MAC Address CLI Actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MacAddressCliController extends IXP_Controller_CliAction
{
/*
    public function updateDatabaseAction()
    {
        $switches = $this->getD2R( '\\Entities\\Switcher' )->getAndCache( true, \Entities\Switcher::TYPE_SWITCH );

        foreach( $switches as $switch )
        {
            if( $sw = new \OSS_SNMP\SNMP( $switch->getHostname(), $switch->getSnmppasswd() ) )
            {
                $descrs = $sw->useIface()->descriptions();

    my @interface2ifindex = &snmpwalk($host, ".1.3.6.1.2.1.17.1.4.1.2");
    my @bridgehash2ifindex = &snmpwalk($host, ".1.3.6.1.2.1.17.4.3.1.2");
    my @bridgehash2mac = &snmpwalk($host, ".1.3.6.1.2.1.17.4.3.1.1");

    my ($ifindex, $interfaces, $bridgehash, $macaddr);

    # ifindex2descr - oid -> 1001 - descr -> X460-48x Port 1
    foreach my $entry (@ifindex2descr) {
        my ($oid, $descr) = split(':', $entry, 2);
        $ifindex->{$oid} = $descr;
    }

    # interface2ifindex - oid -> 7 - descr -> 1007
    foreach my $entry (@interface2ifindex) {
        my ($oid, $descr) = split(':', $entry, 2);
        $interfaces->{$oid} = $descr;
    }

    # '136.67.225.163.42.128' => '49'
    foreach my $entry (@bridgehash2ifindex) {
        my ($oid, $descr) = split(':', $entry, 2);
        $bridgehash->{$oid} = $descr;
    }

    # 136.67.225.163.42.128 - 0x8843e1a32a80
    foreach my $entry (@bridgehash2mac) {
        my ($oid, $descr) = split(':', $entry, 2);

        if (defined($ifindex->{ $interfaces->{ $bridgehash->{$oid} } })) {
            push (@{$macaddr->{$ifindex->{ $interfaces->{ $bridgehash->{$oid} } } }}, lc(substr($descr,2)));
        }
    }

    return $macaddr;

            }
            else
            {
                echo "ERR: Could not get SNMP session with {$switch->getHostname()}\n";
            }
        }
    }*/
}

