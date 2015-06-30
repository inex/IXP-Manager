#!/usr/bin/perl -w
#
# Copyright (C) 2009-2014 Internet Neutral Exchange Association Limited.
# All Rights Reserved.
# 
# This file is part of IXP Manager.
# 
# IXP Manager is free software: you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the Free
# Software Foundation, version 2.0 of the License.
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

# Script used to populate the IXP Manager database for the route server
# prefix analysis tool. 
#
# See: https://github.com/inex/IXP-Manager/wiki/Route-Server-Prefix-Analysis-Tool
#
# You need to install and configure the IXP Manager perl libraries for this script
# to work. See:
#
# https://github.com/inex/IXP-Manager/wiki/Installation-08-Setting-Up-Your-IXP#perl-libraries
#
#
# NB: Ensure you set $vlanid, $conffile and $sockfile in the protocols loop
#     below (search for XXX-SET-ME)


use strict;

use Data::Dumper;
use NetAddr::IP qw(:lower Compact);

use IXPManager::Config;
use IXPManager::Const;

my $devmode = 1;
my $debug = 1;
my $do_nothing = 0;

# XXX-SET-ME
my $vlanid = "1";

my $ixpconfig = new IXPManager::Config;
my $dbh = $ixpconfig->{db};
my ($query, $sth);

# We need to build an autnum -> custid mapping
$query = "SELECT autsys, id FROM view_cust_current_active WHERE type IN (?, ?, ?)";
if (!($sth = $dbh->prepare($query))) {
	die "$dbh->errstr\n";
}
if (!$sth->execute(CUST_TYPE_FULL, CUST_TYPE_PROBONO, CUST_TYPE_INTERNAL)) {
	die "$dbh->errstr\n";
}
                
my $autsys = $sth->fetchall_hashref('autsys');

# Prepare some statements for flinging crap into the table
$query = "INSERT into rs_prefixes (timestamp, custid, prefix, protocol, irrdb, rs_origin) VALUES (NOW(), ?, ?, ?, ?, ?)";
my $insertsth;
if (!($insertsth = $dbh->prepare($query))) {
	die "$dbh->errstr\n";
}

$query = "UPDATE rs_prefixes SET timestamp=NOW(), irrdb=?, rs_origin=? WHERE id=?";
my $updatesth;
if (!($updatesth = $dbh->prepare($query))) {
	die "$dbh->errstr\n";
}

# Finally, pull a complete copy of the database so that we don't end up with huge numbers of inserts every run 
$query = "SELECT cu.autsys, dp.id, dp.prefix, dp.protocol, dp.irrdb, dp.rs_origin FROM (cust cu, rs_prefixes dp) WHERE cu.id = dp.custid";
if (!($sth = $dbh->prepare($query))) {
	die "$dbh->errstr\n";
}
if (!$sth->execute()) {
	die "$dbh->errstr\n";
}

my $origin;

# fetchall_hashref() is ~ 2x cpu efficient as this loop, but it takes ~5
# times the amount of res memory.  We don't want to memory starve a route
# server => use the loop instead

while (my $rec = $sth->fetchrow_hashref) {
	my $p = \%{$origin->{$rec->{protocol}}->{$rec->{autsys}}->{$rec->{prefix}}};
	$p->{id} = $rec->{id};
	$p->{protocol} = $rec->{protocol};
	$p->{irrdb} = $rec->{irrdb};
	$p->{rs_origin} = $rec->{rs_origin};
}

foreach my $protocol (qw(4 6)) {
	# XXX-SET-ME
	my $conffile = "/etc/bird/bird-vlanid".$vlanid."-ipv".$protocol.".conf";
	my $sockfile = "/var/run/bird/bird-vlanid".$vlanid."-ipv".$protocol.".ctl";

	open (INPUT, $conffile);
	my ($asn, $address, $prefixes);

	while (<INPUT>) {
		if (/^\s*allnet\s*=\s*\[\s*([a-fA-F0-9\.:,\s\/]+)\s*\]/) {
			$prefixes = $1;
			$prefixes =~ s/^\s+|\s+$//g;
			next;
		} elsif (/^\s*neighbor\s+([a-fA-F0-9\.:]+)\s+as\s+(\d+)/) {
			$address = $1; $asn = $2;
		} else {
			next;
		}

		my @pfxlist = split(/\s*,\s*/, $prefixes);
		foreach my $prefix (@pfxlist) {
			my $ip = new NetAddr::IP::Lite $prefix;
			next unless $ip;
			my $p = \%{$origin->{$protocol}->{$asn}->{$ip->short."/".$ip->masklen}};
			$p->{refresh_irrdb} = 1;
			if (!defined ($p->{irrdb}) || (defined ($p->{irrdb}) && $p->{irrdb} != 1)) {
				$p->{irrdb} = 1;
				$p->{changed} = 1;
			}
		}
		$asn = undef; $address = undef; $prefixes = '';
	}
	close (INPUT);

	my (@asnlist);
	my (%vliidhash);
	
	open (INPUT, '/usr/sbin/birdc -s '.$sockfile.' show protocols |');
	while (<INPUT>) {
		next unless (/^pb_(\d+)_as(\d+)\s+.*\s+up\s+.*\s+Established/);
		push (@asnlist, $2);
		$vliidhash{ $2 } = $1;
		print "BIRDC: Found established session for $2 (with vliid $1)\n" if( $devmode );
		
	}
	close (INPUT);

	$do_nothing or $dbh->do('START TRANSACTION') or die $dbh->errstr;
                                   
	foreach my $asn (@asnlist) {
		my $cmd = '/usr/sbin/birdc -s '.$sockfile.' show route table t_' . $vliidhash{ $asn } . '_as'.$asn.' protocol pb_' . $vliidhash{ $asn } . '_as'.$asn;
		print "BIRD: $cmd\n" if ($debug);
		open (INPUT, $cmd.' |');
		while (<INPUT>) {
			# 195.189.221.0/24   via 193.242.111.17 on vlan10 [pb_as2110 May17] * (100) [AS22711i]
			next unless (/^([A-Fa-f0-9:\.]+\/\d+)\s+via\s+.*\[AS(\d+)\S*\]/);
			my $ip = new NetAddr::IP::Lite $1;
			my $asorigin = $2;
			next unless $ip;
			my $p = \%{$origin->{$protocol}->{$asn}->{$ip->short."/".$ip->masklen}};
			$p->{refresh_rs} = 1;
			if (!defined ($p->{rs_origin}) || (defined ($p->{rs_origin}) && $p->{rs_origin} != $asorigin)) {
				$p->{rs_origin} = $asorigin;
				$p->{changed} = 1;
			}
		}	
		close (INPUT);

		foreach my $prefix (keys %{$origin->{$protocol}->{$asn}}) {
			my $p = \%{$origin->{$protocol}->{$asn}->{$prefix}};

			if ($p->{changed}) {
				if (!defined ($p->{id})) {			# new entry
					my $irrdb = defined ($p->{irrdb}) ? 1 : 0;
					my $rs_origin = defined ($p->{rs_origin}) ? $p->{rs_origin} : "";
					print "INSERT: peer-as: $asn, prefix: $prefix, origin-as: $rs_origin, irrdb: $irrdb\n" if ($debug);
					$do_nothing or $insertsth->execute($autsys->{$asn}->{id}, $prefix, $protocol, $irrdb, $p->{rs_origin}) or die $dbh->errstr;
				} else {					# updated entry
					my $irrdb = defined ($p->{irrdb}) ? 1 : 0;
					my $rs_origin = defined ($p->{rs_origin}) ? $p->{rs_origin} : "";
					print "UPDATE: peer-as: $asn, prefix: $prefix, origin-as: $rs_origin, irrdb: $irrdb\n" if ($debug);
					$do_nothing or $updatesth->execute($irrdb, $p->{rs_origin}, $p->{id}) or die $dbh->errstr;
				}
			} elsif ($p->{id}
					&& !defined($p->{refresh_irrdb})
					&& !defined($p->{refresh_rs})) {	# stale entry
				my $irrdb = defined ($p->{irrdb}) ? 1 : 0;
				my $rs_origin = defined ($p->{rs_origin}) ? $p->{rs_origin} : "";
				print "DELETE: peer-as: $asn, prefix: $prefix, origin-as: $rs_origin, irrdb: $irrdb\n" if ($debug);
				$do_nothing or $dbh->do(q{DELETE FROM rs_prefixes WHERE id = ?}, undef, $p->{id}) or die $dbh->errstr;
			}
		}
	}

	$do_nothing or $dbh->do('COMMIT') or die $dbh->errstr;
}
