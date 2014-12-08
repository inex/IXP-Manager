#! /usr/bin/env perl
#
# update-l2database.pl
#
# Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
# All Rights Reserved.
# 
# This file is part of IXP Manager.
# 
# IXP Manager is free software: you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the Free
# Software Foundation, version v2.0 of the License.
# 
# IXP Manager is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
# more details.
# 
# You should have received a copy of the GNU General Public License v2.0
# along with IXP Manager.  If not, see:
# 
# http://www.gnu.org/licenses/gpl-2.0.html
#
# Description:
#
# A script to poll all switches and update a database table of the known
# MAC addresses attached to each port.  The general approach here pulls
# the bridge info from SNMP, which encodes the mac address as part of the
# OID and maps it to the bridge index.  The bridge index is then mapped to
# the ifIndex which in turn is mapped to the text-format ifDescr.
#
# This is full of fail by the various switch vendors.  They all do things
# slightly differently.
# 
# Implementation Notes:
#
# Standard SNMP implementations: 
#
# Brocade TurboIron >= 4.2.00c:	Q-BRIDGE-MIB, BRIDGE-MIB
# Brocade FES-X6xx <= 5.4.00c:	BRIDGE-MIB
# Brocade FES-X6xx >= 5.4.00e:	Q-BRIDGE-MIB, BRIDGE-MIB
# Brocade NetIron > 5.1.00:	Q-BRIDGE-MIB, BRIDGE-MIB
# Dell FTOS S4810:		Q-BRIDGE-MIB. Uses separate Port-Channel interface.
# Extreme BD-8806, X series:	BRIDGE-MIB.  Requires dot1dTpFdbAddress support.
#
# Broken stuff which causes headwreck:
# Cisco Anything:		BRIDGE-MIB.  Per vlan support implemented with community@vlan,
#				argh.  Documented in Cisco Document ID 44800: "Using SNMP to Find a
#				Port Number from a MAC Address on a Catalyst Switch"
# Juniper EX Series:		no BRIDGE-MIB. partial Q-BRIDGE-MIB support.  Complete weirdness. 
#				Reference Juniper KB26533: "How to identify which MAC address
#				(non-default VLAN) is learnt from which interface via SNMP" and
#				Juniper KB20833 "How to find which MAC address (default VLAN) is
#				learnt from which interface via SNMP".  Requires jnxExVlanTag
#				support.

use strict;
use Net_SNMP_util;
use Getopt::Long;
use Data::Dumper;

use IXPManager::Config;
use IXPManager::Const;

my $ixpconfig = new IXPManager::Config;
my $dbh = $ixpconfig->{db};
my $debug = 0;
my $do_nothing = 0;
my $qbridge_support = 1;
my $vlan;
my $debug_output;

my ($query, $sth, $l2mapping);

GetOptions(
	'debug!'		=> \$debug,
	'do-nothing!'		=> \$do_nothing,
	'qbridge-support!'	=> \$qbridge_support,
	'vlan=i'		=> \$vlan,
);

$query = "SELECT s.name, s.snmppasswd, v.name AS vendor FROM switch s LEFT JOIN vendor v ON v.id = s.vendorid WHERE active AND switchtype = ?";

($sth = $dbh->prepare($query)) or die "$dbh->errstr\n";
$sth->execute(SWITCHTYPE_SWITCH) or die "$dbh->errstr\n";
my $switches = $sth->fetchall_hashref('name');

foreach my $switch (keys %{$switches}) {
	$l2mapping->{$switch} = trawl_switch_snmp($switches->{$switch}, $vlan);
}

if ($debug) {
	($debug_output = Dumper($l2mapping)) =~ s/^\$VAR[0-9]+ = /\$l2mapping = /;
	print STDERR $debug_output;
}

$query = "SELECT id, switchport, switchportid, spifname, switch, status, infrastructure FROM view_switch_details_by_custid";
($sth = $dbh->prepare($query)) or die "$dbh->errstr\n";
$sth->execute() or die "$dbh->errstr\n";
my $ports = $sth->fetchall_hashref( [qw (switch switchport)] );

if ($debug) {
	($debug_output = Dumper($ports)) =~ s/^\$VAR[0-9]+ = /\$ports = /;
	print STDERR $debug_output;
}

my ($insertsth);
$query = "INSERT INTO macaddress (id, firstseen, virtualinterfaceid, mac) VALUES (NULL, NOW(), ?, ?)";
($insertsth = $dbh->prepare($query)) or die "$dbh->errstr\n";

$do_nothing or $dbh->do('START TRANSACTION') or die $dbh->errstr;
$do_nothing or $dbh->do('DELETE FROM macaddress') or die $dbh->errstr;

foreach my $switch (keys %{$ports}) {
	$debug && print STDERR "\n";
	foreach my $port (keys %{$ports->{$switch}}) {
		foreach my $mac (@{$l2mapping->{$switch}->{$port}}) {
			$debug && print STDERR "INSERT: $mac -> $switch:$port\n";
			$do_nothing or $insertsth->execute($ports->{$switch}->{$port}->{id}, $mac) or die "$dbh->errstr\n";
#			$ports->{$switch}->{$port}->{mac} = $l2mapping->{$switch}->{$port};
		}
	}
}

$do_nothing or $dbh->do('COMMIT') or die $dbh->errstr;

exit;

# Source and comments(!) adapted from Net::SNMP::Mixin::Util::normalize_mac()
# (Artistic Licensed code) found at:
#  http://search.cpan.org/perldoc?Net%3A%3ASNMP%3A%3AMixin%3A%3AUtil
sub normalize_mac {
	my ($mac) = @_;
	return unless defined $mac;

	# translate this OCTET_STRING to hexadecimal, unless already translated
	if ( length $mac == 6 ) {
		$mac = unpack 'H*', $mac;
	}

	# to lower case
	my $norm_address = lc($mac);

	# remove '-' in bloody Microsoft format
	$norm_address =~ s/-//g;

	# remove '.' in bloody Cisco format
	$norm_address =~ s/\.//g;

	# remove '0x' in front of, we are already lower case
	$norm_address =~ s/^0x//;

	# we are already lower case
	my $hex_digit = qr/[a-f,0-9]/;

	# insert leading 0 in bloody Sun format
	$norm_address =~ s/\b($hex_digit)\b/0$1/g;

	# wrong format
	return unless $norm_address =~ m /^$hex_digit{12}$/;

	return $norm_address;
}

# oid2mac: converts from dotted decimal format to nonseparated hex

sub oid2mac {
	my ($mac) = @_;

	return join ("", map { sprintf ('%02x', $_) } split(/\./, $mac))
}

sub trawl_switch_snmp ($$) {
	my ($host_info, $vlan) = @_;
	my $host = $host_info->{name};
	my $snmpcommunity = $host_info->{snmppasswd};
	my $vendor = $host_info->{vendor};
	my ($dbridgehash, $qbridgehash, $macaddr, $junipermapping, $vlanmapping);
	my $oids = {
		'ifDescr'		=> '.1.3.6.1.2.1.2.2.1.2',
		'dot1dBasePortIfIndex'	=> '.1.3.6.1.2.1.17.1.4.1.2',
		'dot1qVlanFdbId'	=> '.1.3.6.1.2.1.17.7.1.4.2.1.3',
		'dot1qTpFdbPort'	=> '.1.3.6.1.2.1.17.7.1.2.2.1.2',
		'dot1dTpFdbPort'	=> '.1.3.6.1.2.1.17.4.3.1.2',
		'dot1dTpFdbAddress'	=> '.1.3.6.1.2.1.17.4.3.1.1',
		'jnxExVlanTag'		=> '.1.3.6.1.4.1.2636.3.40.1.5.1.5.1.5',
	};
	my $snmpcommunitybridge = $snmpcommunity;
	# Cisco wants us to query BRIGE-MIB OIDs with community@vlan.
	if ($vendor =~ /^cisco/i) {
		$snmpcommunitybridge = "$snmpcommunity\@$vlan";
	}
	$debug && print STDERR "DEBUG: $host: started query process\n";

	my $ifindex = snmpwalk2hash($host, $snmpcommunity, $oids->{ifDescr});
	if (!$ifindex) {
		print STDERR "WARNING: $host: cannot read ifDescr. Not processing $host further.\n";
		return;
	}
	my $interfaces = snmpwalk2hash($host, $snmpcommunitybridge, $oids->{dot1dBasePortIfIndex});
	if (!$interfaces) {
		print STDERR "WARNING: $host: cannot read dot1dBasePortIfIndex. Not processing $host further.\n";
		return;
	}

	$debug && print STDERR "DEBUG: $host: pre-emptively trying Juniper jnxExVlanTag to see if we're on a J-EX box (".$oids->{jnxExVlanTag}.")\n";
	$vlanmapping = snmpwalk2hash($host, $snmpcommunity, $oids->{jnxExVlanTag});
	# if jnxExVlanTag returns something, then this is a juniper and we need to
	# handle the interface mapping separately on these boxes
	if ($vlanmapping) {
		$junipermapping = 1;
		$debug && print STDERR "DEBUG: $host: looks like this is a Juniper EX\n";
	} else {
		$debug && print STDERR "DEBUG: $host: this isn't a Juniper EX\n";
	}

	# attempt to use Q-BRIDGE-MIB.
	if ($vlan && $qbridge_support) {
		$debug && print STDERR "DEBUG: $host: attempting to retrieve dot1qVlanFdbId mapping (".$oids->{dot1qVlanFdbId}.")\n";

		# FIXME: the .0 at the end of this URL cannot be discarded like this
		if (!$vlanmapping) {
			$vlanmapping = snmpwalk2hash($host, $snmpcommunity, $oids->{dot1qVlanFdbId}.".0", undef, undef);
		}

		# At this stage we should have a dot1qVlanFdbId mapping, but
		# some switches don't support it (e.g.  Dell F10-S4810), so
		# if it doesn't exist we'll attempt Q-BRIDGE-MIB with the
		# VLAN IDs instead of mapped IDs.

		my $vlanid;
		if ($vlanmapping) {	# if this fails too, Q-BRIDGE-MIB is out
			my $vlan2idx = {reverse %{$vlanmapping}};
			$vlanid = $vlan2idx->{$vlan};
			$debug && print STDERR "DEBUG: $host: got mapping index: $vlan maps to $vlanid\n";
		} else {
			$debug && print STDERR "DEBUG: $host: that didn't work either. attempting Q-BRIDGE-MIB with no fdb->ifIndex mapping\n";
			$vlanid = $vlan;
		}
		$debug && print STDERR "DEBUG: $host: attempting Q-BRIDGE-MIB ($oids->{dot1qTpFdbPort}.$vlanid)\n";
		$qbridgehash = snmpwalk2hash($host, $snmpcommunity, "$oids->{dot1qTpFdbPort}.$vlanid", \&oid2mac, undef);
		if ($debug) {
			if ($qbridgehash) {
				print STDERR "DEBUG: $host: Q-BRIDGE-MIB query successful\n";
			} else {
				print STDERR "DEBUG: $host: failed to retrieve Q-BRIDGE-MIB. falling back to BRIDGE-MIB\n";
			}
		}
	} else {
		$debug && $qbridge_support && print STDERR "DEBUG: $host: vlan not specified - falling back to BRIDGE-MIB for compatibility\n";
	}

	# special case: when the vlan is not specified, juniper EX boxes
	# return data on Q-BRIDGE-MIB rather than BRIDGE-MIB
	if (!$vlan && $junipermapping) {
		$debug && print STDERR "DEBUG: $host: attempting special Juniper EX Q-BRIDGE-MIB query for unspecified vlan\n";
		$qbridgehash = snmpwalk2hash($host, $snmpcommunity, $oids->{dot1qTpFdbPort}, \&oid2mac, undef);
		if ($debug) {
			if ($qbridgehash) {
				print STDERR "DEBUG: $host: Juniper EX Q-BRIDGE-MIB query successful\n";
			} else {
				print STDERR "DEBUG: $host: failed Juniper EX Q-BRIDGE-MIB retrieval\n";
			}
		}			
	}

	# if vlan wasn't specified or there's nothing coming in from the
	# Q-BRIDGE mib, then use rfc1493 BRIDGE-MIB.
	if (($vlan && !$qbridgehash) || (!$vlan && !$junipermapping)) {
		$debug && print STDERR "DEBUG: $host: attempting BRIDGE-MIB ($oids->{dot1dTpFdbPort})\n";
		$dbridgehash = snmpwalk2hash($host, $snmpcommunitybridge, $oids->{dot1dTpFdbPort});
		$dbridgehash && $debug && print STDERR "DEBUG: $host: BRIDGE-MIB query successful\n";
	}

	# if this isn't supported, then panic.  We could probably try
	# community@vlan syntax, but this should be good enough.
	if (!$qbridgehash && !$dbridgehash) {
		print STDERR "WARNING: $host: cannot read BRIDGE-MIB or Q-BRIDGE-MIB. Not processing $host further.\n";
		return;
	}

	my ($bridgehash, $maptable, $bridgehash2mac);
	if ($dbridgehash) {
		$bridgehash2mac = snmpwalk2hash($host, $snmpcommunitybridge, $oids->{dot1dTpFdbAddress}, undef, \&normalize_mac);
		$bridgehash = $dbridgehash;
		$maptable = $bridgehash2mac;
	} else {
		$bridgehash = $qbridgehash;
		$maptable = $qbridgehash;
	}
		
	foreach my $entry (keys %{$maptable}) {
		if (defined($ifindex->{$interfaces->{$bridgehash->{$entry}}})) {
			my $int = $ifindex->{$interfaces->{$bridgehash->{$entry}}};
			if ($junipermapping && $int =~ /\.\d+$/) {
				$int =~ s/(\.\d+)$//;
			}
			if ($dbridgehash) {
				$entry = $bridgehash2mac->{$entry};
			}
			push (@{$macaddr->{$int}}, $entry);
		}
	}

	return $macaddr;
}

sub snmpwalk2hash {
	my($host, $snmpcommunity, $queryoid, $keycallback, $valuecallback) = @_;
	my $returnhash;

	my $comm = $snmpcommunity.'@'.$host;

	my @resultarray = &snmpwalk($comm, $queryoid);

	foreach my $entry (@resultarray) {
		my ($returnoid, $descr) = split(':', $entry, 2);
		if ($keycallback) {
			$returnoid = &$keycallback($returnoid);
		}
		if ($valuecallback) {
			$descr = &$valuecallback($descr);
		}
		$returnhash->{$returnoid} = $descr;
	}

	return $returnhash;
}
