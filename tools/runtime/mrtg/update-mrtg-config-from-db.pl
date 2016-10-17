#!/usr/bin/perl
#
# update-mrtg-config-from-db.pl
#
# Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
# A script to pull an IXP configuration from the IXP Manager back-end
# database and write out a complete mrtg configuration for all members,
# including pkts, bytes, errors and discards.
#
# Optional parameters:
#
# --configdir <dir>	The destination directory of the MRTG configuration files
# --templatename <file>	An MRTG template which is inserted into the top of
#			each mrtg config file
# --datadir <dir>	The destination directory for the MRTG data files
# --owner <username>	The unix owner for the MRTG data files
# --ixpname <name>	Pretty name for the IXP
# --ixpmaxbits <num>	the maximum amount of traffic that the IXP is
# 			expected to pass (bits/sec)
# --debug		Output piles of debugging information

use strict;
use warnings;

use Getopt::Long;
use File::Copy;
use IXPManager::Config;
use IXPManager::Const;
use IXPManager::Utils;

use Data::Dumper;	# debugging only

my $mrtgconfigdir = "/opt/local/etc/mrtg";
my $mrtgconfigtemplate = "$mrtgconfigdir/mrtg.cfg.template";
my $mrtgdatadir = "/home/mrtg";
my $mrtgsubdir = "members";
my $dirowner = "mrtg";
my $ixpfullname = "INEX";
my $ixpmaxbits = 200*1000*1000*1000;	# 200 Gbit/sec
my $debug = 0;

GetOptions(
	'configdir=s'		=> \$mrtgconfigdir,
	'datadir=s'		=> \$mrtgdatadir,
	'debug'			=> \$debug,
	'ixpmaxbits=i'		=> \$ixpmaxbits,
	'ixpname=s'		=> \$ixpfullname,
	'owner=s'		=> \$dirowner,
	'templatename=s'	=> \$mrtgconfigtemplate,
);

my $mrtgconfigfile = "$mrtgconfigdir/mrtg.cfg";
my $tmpmrtgconfigfile = "$mrtgconfigfile.$$";

my ($lans);

my $traffictypes = {
	bits	 => {
		in	=> 'ifHCInOctets',
		out	=> 'ifHCOutOctets',
		maxbytes => $ixpmaxbits/8,
		options => 'growright,bits',
		name	=> 'Bits',
	},
	pkts	 => {
		in	=> 'ifHCInUcastPkts',
		out	=> 'ifHCOutUcastPkts',
		maxbytes => $ixpmaxbits/8,
		options => 'growright',
		name	=> 'Packets',
	},
};	

my ($grouplist, %opts);
my ($errstr, $sth, $query);

# Get uid / gid of directory owner
my ($uid, $gid);
($uid, $gid) = (getpwnam($dirowner))[2,3];

# Open handle to sql db
my $ixpconfig = new IXPManager::Config;
my $dbh = $ixpconfig->{db};

open (OUTPUT, ">$tmpmrtgconfigfile");

# Slurp in the configuration template
open (INPUT, "$mrtgconfigtemplate");
my @template = <INPUT>;
print OUTPUT @template;
close (INPUT);

# Get a list of all the switch IDs on each infrastructure and insert into cache
$query = 'SELECT
		id,
		name,
		infrastructure
	FROM switch
	WHERE
		active > 0
	AND	infrastructure IS NOT NULL
	AND	switchtype = ?
';

$sth = $dbh->prepare($query) || die "$dbh->errstr\n";
$sth->execute(SWITCHTYPE_SWITCH) || die "$dbh->errstr\n";

while (my $rec = $sth->fetchrow_hashref) {
	$debug && print STDERR ("INFO: calculating infrastructure for id: $rec->{id}, infrastructure: $rec->{infrastructure}, name: $rec->{name}\n");
	push (@{$lans->{$rec->{infrastructure}}->{switchids}}, $rec->{id});
}

# Slurp in generic switch info from database
$query = 'SELECT
		sw.id		AS switchid,
		sw.vendorid,
		sw.name		AS switchname,
		sw.snmppasswd,
		sp.ifName	AS switchport
	FROM
		switch sw,
		switchport sp
	WHERE
		sw.id = sp.switchid
	AND	sp.type = ?
	AND	sw.id = ?
	ORDER BY
		switchname,
		switchport
';

$sth = $dbh->prepare($query) || die "$dbh->errstr\n";

# slurp everything in from SQL
foreach my $infra (keys %{$lans}) {
	foreach my $switchid (@{$lans->{$infra}->{switchids}}) {
		$sth->execute(SWITCHPORT_TYPE_PEERING, $switchid) || die "$dbh->errstr\n";
		while (my $rec = $sth->fetchrow_hashref) {
			$debug && print STDERR ("INFO: adding switchport: $rec->{switchname}, port: $rec->{switchport}\n");
			$lans->{$infra}->{$rec->{switchid}}->{name} = $rec->{switchname};
			$lans->{$infra}->{$rec->{switchid}}->{snmppasswd} = $rec->{snmppasswd};
			$lans->{$infra}->{$rec->{switchid}}->{vendorid} = $rec->{vendorid};
			push (@{$lans->{$infra}->{$rec->{switchid}}->{ports}}, $rec->{switchport});
		}
	}
}

my ($target, $alltargets);

# build per-target configuration stuff
foreach my $infra (keys %{$lans}) {
	foreach my $traffictype (keys %{$traffictypes}) {
		foreach my $switchid (@{$lans->{$infra}->{switchids}}) {
			foreach my $switchport (@{$lans->{$infra}->{$switchid}->{ports}}) {
				my $spidentifier = IXPManager::Utils::switchportifnametosnmpidentifier($switchport);
				$debug && print STDERR ("INFO: per-infra aggregate pushed $spidentifier (\"$switchport\") to infra $infra\n");
				my $mrtgobj =	$traffictypes->{$traffictype}->{in}.'#'.$spidentifier.
						'&'.
						$traffictypes->{$traffictype}->{out}.'#'.$spidentifier.
						':'.
						$lans->{$infra}->{$switchid}->{snmppasswd}.
						'@'.
						$lans->{$infra}->{$switchid}->{name}.
						':::::2';
				
				$debug && print STDERR ("INFO: per-infra aggregate pushed $spidentifier (\"$switchport\") to infra $infra\n");
				push (@{$target->{'ixp_peering-network'.$infra.'-'.$traffictype}}, $mrtgobj);
			}
		}

		my $mrtglabel = 'ixp_peering-network'.$infra.'-'.$traffictype;
		push (@{$alltargets->{$traffictype}}, @{$target->{$mrtglabel}});
		my $localtarget = join (' + ', @{$target->{$mrtglabel}});
		print OUTPUT <<EOF;
# LAN$infra $traffictypes->{$traffictype}->{name} traffic
Target[$mrtglabel]:   $localtarget
MaxBytes[$mrtglabel]: $traffictypes->{$traffictype}->{maxbytes}
Title[$mrtglabel]:    $ixpfullname $traffictypes->{$traffictype}->{name} / second traffic on Infrastructure $infra
Options[$mrtglabel]:  $traffictypes->{$traffictype}->{options}
YLegend[$mrtglabel]:  $traffictypes->{$traffictype}->{name} / Second

EOF
	}
}

# build aggregates
foreach my $traffictype (keys %{$traffictypes}) {
		my $mrtglabel = 'ixp_peering-aggregate-'.$traffictype;
		my $localtarget = join (' + ', @{$alltargets->{$traffictype}});
		print OUTPUT <<EOF;
# Aggregate $traffictypes->{$traffictype}->{name} on entire exchange
Target[$mrtglabel]:   $localtarget
MaxBytes[$mrtglabel]: $traffictypes->{$traffictype}->{maxbytes}
Title[$mrtglabel]:    $ixpfullname Aggregate Traffic - $traffictype
Options[$mrtglabel]:  $traffictypes->{$traffictype}->{options}
YLegend[$mrtglabel]:  $traffictypes->{$traffictype}->{name} / Second
		
EOF
}

# build per-switch aggregates
$query = 'SELECT
		sw.id		AS switchid,
		sw.vendorid,
		sw.name		AS switchname,
		sw.snmppasswd,
		sp.ifName	AS switchport
	FROM
		switch sw,
		switchport sp
	WHERE
		sw.id = sp.switchid
	AND	sw.active = 1
	ORDER BY
		switchname,
		switchport
';

$sth = $dbh->prepare($query) || die "$dbh->errstr\n";
$sth->execute() || die "$dbh->errstr\n";

my $switchports;
# slurp everything in from SQL
while (my $rec = $sth->fetchrow_hashref) {
	my $spidentifier = IXPManager::Utils::switchportifnametosnmpidentifier($rec->{switchport});
	foreach my $traffictype (keys %{$traffictypes}) {
		my $mrtgobj =	$traffictypes->{$traffictype}->{in}.'#'.$spidentifier.
				'&'.
				$traffictypes->{$traffictype}->{out}.'#'.$spidentifier.
				':'.
				$rec->{snmppasswd}.
				'@'.
				$rec->{switchname}.
				':::::2';
		push (@{$switchports->{$rec->{switchname}}->{$traffictype}}, $mrtgobj);
	}
}

# build switch aggregates
foreach my $traffictype (keys %{$traffictypes}) {
	foreach my $switch (sort keys %{$switchports}) {
		my $mrtglabel = 'switch-aggregate-'.$switch.'-'.$traffictype;
		my $switchtarget = join (' + ', @{$switchports->{$switch}->{$traffictype}});
		print OUTPUT <<EOF;
# Switch traffic on $switch / $traffictype
Target[$mrtglabel]:   $switchtarget
MaxBytes[$mrtglabel]: $traffictypes->{$traffictype}->{maxbytes}
Title[$mrtglabel]:    $ixpfullname Switch Traffic on $switch
Options[$mrtglabel]:  $traffictypes->{$traffictype}->{options}
YLegend[$mrtglabel]:  $traffictypes->{$traffictype}->{name} / Second
Directory[$mrtglabel]: switches

EOF
	}
}

$query = 'SELECT
		cu.id,
		cu.name,
		cu.shortname,
		sd.switch,
		sd.spifname,
		sd.vendorid,
		sd.snmppasswd,
		pi.speed,
		pi.monitorindex
	FROM (
		view_cust_current_active cu,
		physicalinterface pi,
		virtualinterface vi )
	LEFT JOIN view_switch_details_by_custid sd ON pi.switchportid = sd.switchportid
	WHERE
		cu.id = vi.custid
	AND	pi.virtualinterfaceid = vi.id
	AND	cu.status = ?
	AND	cu.type != ? 
	ORDER BY
		shortname
';

$sth = $dbh->prepare($query) || die "$dbh->errstr\n";
$sth->execute(CUST_STATUS_NORMAL, CUST_TYPE_ASSOCIATE) || die "$dbh->errstr\n";

while (my $rec = $sth->fetchrow_hashref) {
	my $membername = $rec->{name};
	my $shortname = $rec->{shortname};
	my $switch = $rec->{switch};
	my $switchport = $rec->{spifname};
	my $speed = $rec->{speed};
	my $index = $rec->{monitorindex};

	my $tag = $shortname."-".$index;
	my $speedbytes = $speed * 1000000 / 8;
	my $speedpkts  = int ($speedbytes / 64);
	my $speeddisc  = int ($speedbytes / 10);

	my $shortport = IXPManager::Utils::switchportifnametosnmpidentifier($switchport);

	$rec->{shortport} = $shortport;

	my $mrtgport = "#$shortport:".$rec->{snmppasswd}."\@$switch\:::::2";
	$rec->{mrtgport} = $mrtgport;

	# Create aggregate for customer
	my $gl = [];
	if (defined ($grouplist->{$shortname})) {
		$gl = $grouplist->{$shortname};
	}
	push (@{$gl}, $rec);
	$grouplist->{$shortname} = $gl;

	$debug && print STDERR ("INFO: added per-customer entries: $membername, $switch:$switchport, $speed Mbps\n");
	# print out mrtg single configuration entry
	print OUTPUT <<EOF;
# $membername - $tag - bits in/out
Target[$shortname-$index-bits]: $mrtgport
MaxBytes[$shortname-$index-bits]: $speedbytes
Directory[$shortname-$index-bits]: $mrtgsubdir/$shortname
Title[$shortname-$index-bits]: $membername -- $switchport -- $switch -- bits in/out

# $membername - $tag - packets in/out
Target[$shortname-$index-pkts]: ifInUcastPkts#$shortport&ifOutUcastPkts#$shortport:$rec->{snmppasswd}\@$switch\:::::2
MaxBytes[$shortname-$index-pkts]: $speedpkts
Directory[$shortname-$index-pkts]: $mrtgsubdir/$shortname
Options[$shortname-$index-pkts]: growright
YLegend[$shortname-$index-pkts]: Packets/Second
Title[$shortname-$index-pkts]: $membername -- $switchport -- $switch -- packets in/out

# $membername - $tag - errors in/out
Target[$shortname-$index-errs]: ifInErrors#$shortport&ifOutErrors#$shortport:$rec->{snmppasswd}\@$switch\:::::2
MaxBytes[$shortname-$index-errs]: $speedpkts
Directory[$shortname-$index-errs]: $mrtgsubdir/$shortname
Options[$shortname-$index-errs]: growright
YLegend[$shortname-$index-errs]: Errors/Second
Title[$shortname-$index-errs]: $membername -- $switchport -- $switch -- Errors in/out

# $membername - $tag - discards in/out
Target[$shortname-$index-discs]: ifInDiscards#$shortport&ifOutDiscards#$shortport:$rec->{snmppasswd}\@$switch\:::::2
MaxBytes[$shortname-$index-discs]: $speeddisc
Directory[$shortname-$index-discs]: $mrtgsubdir/$shortname
Options[$shortname-$index-discs]: growright
YLegend[$shortname-$index-discs]: Discards/Second
Title[$shortname-$index-discs]: $membername -- $switchport -- $switch -- Discards in/out

EOF
}

foreach my $shortname (sort keys (%{$grouplist})) {
	my ($totalspeed, $speedbytes, $speedpkts, $speeddisc, $name, $id);
	my (@bitsioports, @pktsioports, @errsioports, @discsports);

	foreach my $entry (@{$grouplist->{$shortname}}) {
		my ($switch, $port, $speed);

		(undef, undef, $name, $switch, $port, $speed) = split(/\|/, $entry);

		$totalspeed += $entry->{speed};
		$speedbytes = $totalspeed * 1000000 / 8;
		$speedpkts  = int ($speedbytes / 64);
		$speeddisc  = int ($speedbytes / 10);

		$name = $entry->{name};
		$id = $entry->{id};
		$switch = $entry->{switch};
		my $snmppasswd = $entry->{snmppasswd};
		my $shortport = $entry->{shortport};

		push (@bitsioports, "#$shortport:$snmppasswd\@$switch\:::::2");
		push (@pktsioports, "ifInUcastPkts#$shortport&ifOutUcastPkts#$shortport:$snmppasswd\@$switch\:::::2");
		push (@errsioports, "ifInErrors#$shortport&ifOutErrors#$shortport:$snmppasswd\@$switch\:::::2");
		push (@discsports,  "ifInDiscards#$shortport&ifOutDiscards#$shortport:$snmppasswd\@$switch\:::::2");
	}

	my $bitsioport = join (' + ', @bitsioports);
	my $pktsioport = join (' + ', @pktsioports);
	my $errsioport = join (' + ', @errsioports);
	my $discsport = join (' + ', @discsports);

	print OUTPUT <<EOF;
# $name - $shortname - bits in/out
Target[$shortname-aggregate-bits]: $bitsioport
MaxBytes[$shortname-aggregate-bits]: $speedbytes
Directory[$shortname-aggregate-bits]: $mrtgsubdir/$shortname
Title[$shortname-aggregate-bits]: $name -- aggregate bits in/out

# $name - aggregate - packets in/out
Target[$shortname-aggregate-pkts]: $pktsioport
MaxBytes[$shortname-aggregate-pkts]: $speedpkts
Directory[$shortname-aggregate-pkts]: $mrtgsubdir/$shortname
Options[$shortname-aggregate-pkts]: growright
YLegend[$shortname-aggregate-pkts]: Packets/Second
Title[$shortname-aggregate-pkts]: $name -- aggregate -- packets in/out

# $name - aggregate - errors in/out
Target[$shortname-aggregate-errs]: $errsioport
MaxBytes[$shortname-aggregate-errs]: $speedpkts
Directory[$shortname-aggregate-errs]: $mrtgsubdir/$shortname
Options[$shortname-aggregate-errs]: growright
YLegend[$shortname-aggregate-errs]: Errors/Second
Title[$shortname-aggregate-errs]: $name -- aggregate -- Errors in/out

# $name - aggregate - discards in/out
Target[$shortname-aggregate-discs]: $discsport
MaxBytes[$shortname-aggregate-discs]: $speeddisc
Directory[$shortname-aggregate-discs]: $mrtgsubdir/$shortname
Options[$shortname-aggregate-discs]: growright
YLegend[$shortname-aggregate-discs]: Discards/Second
Title[$shortname-aggregate-discs]: $name -- aggregate -- Discards in/out


EOF
	-d "$mrtgdatadir/$mrtgsubdir/$shortname" 
		or mkdir ("$mrtgdatadir/$mrtgsubdir/$shortname", 0755)
		or die "ERROR: mkdir(\"$mrtgdatadir/$mrtgsubdir/$shortname\") failed\n";
		
	chown $uid, $gid, "$mrtgdatadir/$mrtgsubdir/$shortname";
}

close (OUTPUT);
move ($tmpmrtgconfigfile, $mrtgconfigfile) or die "ERROR: move(\"$tmpmrtgconfigfile\", \"$mrtgconfigfile\") failed\n";
