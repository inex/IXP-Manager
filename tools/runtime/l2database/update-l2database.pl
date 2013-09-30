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
# MAC addresses attached to each port.
#
# This is full of fail by the various switch vendors.  They all do things
# slightly differently.
# 
# Implementation Notes:
#
# Brocade TurboIron >= 4.2.00c:	Q-BRIDGE-MIB, BRIDGE-MIB
# Brocade FES-X6xx <= 5.4.00c:	BRIDGE-MIB
# Brocade FES-X6xx >= 5.4.00e:	Q-BRIDGE-MIB, BRIDGE-MIB
# Brocade NetIron > 5.1.00:	Q-BRIDGE-MIB, BRIDGE-MIB
# Dell FTOS S4810:		Q-BRIDGE-MIB. Uses separate Port-Channel interface.
# Extreme BD-8806, X series:	BRIDGE-MIB.  Requires dot1dTpFdbAddress support.
# Cisco Anything:		BRIDGE-MIB.  Per vlan support implemented with community@vlan hack, argh.
# Juniper EX4550:		no BRIDGE-MIB. partial Q-BRIDGE-MIB support.  Requires jnxExVlanTag support.

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

$query = "SELECT name, snmppasswd FROM switch WHERE active AND switchtype = ?";

($sth = $dbh->prepare($query)) or die "$dbh->errstr\n";
$sth->execute(SWITCHTYPE_SWITCH) or die "$dbh->errstr\n";
my $switches = $sth->fetchall_hashref('name');

foreach my $switch (keys %{$switches}) {
	$l2mapping->{$switch} = trawl_switch_snmp($switch, $switches->{$switch}->{snmppasswd}, $vlan);
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
	my ($host, $snmpcommunity, $vlan) = @_;
	my ($dbridgehash, $qbridgehash, $macaddr);

	$debug && print STDERR "DEBUG: processing $host\n";

	my $ifindex = snmpwalk2hash($host, $snmpcommunity, ".1.3.6.1.2.1.2.2.1.2") || die "cannot read ifDescr from $host";
	my $interfaces = snmpwalk2hash($host, $snmpcommunity, ".1.3.6.1.2.1.17.1.4.1.2") || die "cannot read dot1dBasePortIfIndex from $host";

	# attempt to use Q-BRIDGE-MIB.

	# The approach here is to check dot1qVlanFdbId first to see if this
	# exists to map vlan IDs to vlan numbers.  This doesn't work on
	# Juniper EX series boxes, so we need to check jnxExVlanTag on them.

	if ($vlan && $qbridge_support) {
		$debug && print STDERR "DEBUG: attempting to retrieve dot1qVlanFdbId mapping (.1.3.6.1.2.1.17.7.1.4.2.1.3) on $host\n";

		# FIXME: the .0 at the end of this URL cannot be discarded like this
		my $vlanmapping = snmpwalk2hash($host, $snmpcommunity, ".1.3.6.1.2.1.17.7.1.4.2.1.3.0", undef, undef);

		if (!$vlanmapping) { 	# then either Q-BRIDGE-MIB isn't supported, or else it's broken badly
			$debug && print STDERR "DEBUG: that didn't work. let's try Juniper EX jnxExVlanTag mapping instead (.1.3.6.1.4.1.2636.3.40.1.5.1.5.1.5) on $host\n";
			$vlanmapping = snmpwalk2hash($host, $snmpcommunity, ".1.3.6.1.4.1.2636.3.40.1.5.1.5.1.5");
		}

		# At this stage we should have a dot1qVlanFdbId mapping, but
		# some switches don't support it (e.g.  Dell F10-S4810), so
		# if it doesn't exist we'll attempt Q-BRIDGE-MIB with the
		# VLAN IDs instead of mapped IDs.

		my $vlanid;
		if ($vlanmapping) {	# if this fails too, Q-BRIDGE-MIB is out
			my $vlan2idx = {reverse %{$vlanmapping}};
			$vlanid = $vlan2idx->{$vlan};
			$debug && print STDERR "DEBUG: got mapping index: $vlan maps to $vlanid on $host\n";
		} else {
			$debug && print STDERR "DEBUG: that didn't work either. attempting Q-BRIDGE-MIB with no mapping on $host\n";
			$vlanid = $vlan;
		}
		$debug && print STDERR "DEBUG: attempting Q-BRIDGE-MIB (.1.3.6.1.2.1.17.7.1.2.2.1.2.$vlanid) on $host\n";
		$qbridgehash = snmpwalk2hash($host, $snmpcommunity, ".1.3.6.1.2.1.17.7.1.2.2.1.2.$vlanid", \&oid2mac, undef);
		$qbridgehash || $debug && print STDERR "DEBUG: failed to retrieve Q-BRIDGE-MIB on $host. falling back to BRIDGE-MIB\n";
	} else {
		$debug && $qbridge_support && print STDERR "DEBUG: vlan not specified - falling back to BRIDGE-MIB for compatibility\n";
	}

	# if vlan wasn't specified or there's nothing coming in from the
	# Q-BRIDGE mib, then use rfc1493 BRIDGE-MIB.
	if (($vlan && !$qbridgehash) || !$vlan) {
		$debug && print STDERR "DEBUG: attempting BRIDGE-MIB (.1.3.6.1.2.1.17.4.3.1.2) on $host\n";
		$dbridgehash = snmpwalk2hash($host, $snmpcommunity, ".1.3.6.1.2.1.17.4.3.1.2");
	}

	# if this isn't supported, then panic.  We could probably try
	# community@vlan syntax, but this should be good enough.
	if (!$qbridgehash && !$dbridgehash) {
		die "cannot read BRIDGE-MIB or Q-BRIDGE-MIB from switch $host\n";
	}

	if ($qbridgehash) {
		# '136.67.225.163.42.128' => '49'
		foreach my $entry (keys %{$qbridgehash}) {
			if (defined($ifindex->{$interfaces->{$qbridgehash->{$entry}}})) {
				push (@{$macaddr->{$ifindex->{$interfaces->{$qbridgehash->{$entry}}}}}, $entry);
			}
		}
	} else {
		my $bridgehash2mac = snmpwalk2hash($host, $snmpcommunity, ".1.3.6.1.2.1.17.4.3.1.1", undef, \&normalize_mac);

		foreach my $entry (keys %{$bridgehash2mac}) {
			if (defined($ifindex->{ $interfaces->{ $dbridgehash->{$entry} } })) {
				push (@{$macaddr->{$ifindex->{ $interfaces->{ $dbridgehash->{$entry} } } }}, $bridgehash2mac->{$entry});
			}
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
