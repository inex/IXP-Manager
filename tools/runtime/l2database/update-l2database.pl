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

use strict;
use Net_SNMP_util;
use Data::Dumper;

use IXPManager::Config;
use IXPManager::Const;

my $ixpconfig = new IXPManager::Config;
my $dbh = $ixpconfig->{db};
my $debug = 0;
my $do_nothing = 0;

my ($query, $sth, $l2mapping);

$query = "SELECT name, snmppasswd FROM switch WHERE active AND switchtype = ?";

($sth = $dbh->prepare($query)) or die "$dbh->errstr\n";
$sth->execute(SWITCHTYPE_SWITCH) or die "$dbh->errstr\n";
my $switches = $sth->fetchall_hashref('name');

foreach my $switch (keys %{$switches}) {
	$l2mapping->{$switch} = trawl_switch_snmp($switch, $switches->{$switch}->{snmppasswd});
}

$debug && print STDERR Dumper ($l2mapping);

$query = "SELECT id, switchport, switchportid, spifname, switch, status, infrastructure FROM view_switch_details_by_custid";
($sth = $dbh->prepare($query)) or die "$dbh->errstr\n";
$sth->execute() or die "$dbh->errstr\n";
my $ports = $sth->fetchall_hashref( [qw (switch switchport)] );

my ($insertsth);
$query = "INSERT INTO macaddress (id, firstseen, virtualinterfaceid, mac) VALUES (NULL, NOW(), ?, ?)";
($insertsth = $dbh->prepare($query)) or die "$dbh->errstr\n";

$dbh->do('START TRANSACTION') or die $dbh->errstr;
$dbh->do('DELETE FROM macaddress') or die $dbh->errstr;

foreach my $switch (keys %{$ports}) {
	foreach my $port (keys %{$ports->{$switch}}) {
		foreach my $mac (@{$l2mapping->{$switch}->{$port}}) {
			$debug && printf STDERR "INSERT: $mac -> $switch:$port\n";
			$do_nothing or $insertsth->execute($ports->{$switch}->{$port}->{id}, $mac) or die "$dbh->errstr\n";
#		$ports->{$switch}->{$port}->{mac} = $l2mapping->{$switch}->{$port};
		}
	}
}

$dbh->do('COMMIT') or die $dbh->errstr;

exit;

sub trawl_switch_snmp ($$) {
	my($host, $snmpcommunity) = @_;
	
	$host = $snmpcommunity.'@'.$host;
	
	my @ifindex2descr = &snmpwalk($host, ".1.3.6.1.2.1.2.2.1.2");
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
