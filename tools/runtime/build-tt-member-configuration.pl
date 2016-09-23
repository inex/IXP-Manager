#!/usr/bin/perl
#
# Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
# $Id: build-tt-member-configuration.pl,v 1.10 2010/02/03 13:45:43 nick Exp $
#

use warnings;
use strict;
use utf8;

use Getopt::Long;
use Template;
use Data::Dumper;
use IXPManager::Config;
use IXPManager::Const;

my $ixpconfig = new IXPManager::Config;
my $dbh = $ixpconfig->{db};
my $sth;

my $ttconfig = {
	ABSOLUTE	=> 1,
	RELATIVE	=> 1,
	PLUGIN_BASE	=> 'IXPManager::Template::Plugin',
	EVAL_PERL	=> 1,
};
my $tt = Template->new($ttconfig);

my ($output, $vlan, $protocol, $routeserver, $debug, $shortname, $query, $vars);

$debug = $ixpconfig->{ixp}->{debug};

GetOptions (	"vlan=i" => \$vlan,
		"protocol=s" => \$protocol,
		"routeserver=i" => \$routeserver,
		"debug" => \$debug,
		"shortname=s" => \$shortname,
);

$query = 'SET NAMES "utf8"';
if (!($sth = $dbh->prepare($query))) {
	die "$dbh->errstr\n";
}
if (!$sth->execute()) {
	die "$dbh->errstr\n";
}

$query = <<EOF;
	SELECT
		v.number AS vlan,
		v.name AS name,
		v.rcvrfname as vrfname,
		CONCAT('ipv', ni.protocol) AS protocol,
		UPPER(ni.network) AS network,
		ni.masklen,
		UPPER(ni.rs1address) AS rs1address,
		UPPER(ni.rs2address) AS rs2address
	FROM (
		networkinfo ni,
		vlan v
	)
	WHERE
		v.id = ni.vlanid
EOF

if (!($sth = $dbh->prepare($query))) {
	die "$dbh->errstr\n";
}
if (!$sth->execute()) {
	die "$dbh->errstr\n";
}

my $network;
while (my $rec = $sth->fetchrow_hashref) {
	$vars->{network}->{$rec->{protocol}}->{$rec->{vlan}} = $rec;
}

$query = <<EOF;
	SELECT
                cu.name,
                cu.autsys,
                cu.maxprefixes,
                cu.irrdb,
                IF(LENGTH(cu.peeringmacro) > 1, cu.peeringmacro, CONCAT('AS', cu.autsys)) AS peeringmacro,
		vli.vlan,
		IF(vli.ipv4enabled, UPPER(vli.ipv4address), NULL) AS ipv4address,
		IF(vli.ipv6enabled, UPPER(vli.ipv6address), NULL) AS ipv6address,
EOF
if ($protocol) {
	$query .= "\t\tIF(vli.ipv".$protocol."enabled, UPPER(vli.ipv".$protocol."address), NULL) AS address,\n";
}
$query .= <<EOF;
		vli.ipv4bgpmd5secret,
		vli.ipv6bgpmd5secret,
		vli.virtualinterfaceid,
		vli.rsclient,
		vli.irrdbfilter,
		vli.as112client,
		sw.speed,
		sw.switch,
		sw.switchport,
		sw.locationname
	FROM
		view_cust_current_active cu
	LEFT JOIN (view_vlaninterface_details_by_custid vli) ON (vli.custid = cu.id)
	LEFT JOIN (view_switch_details_by_custid sw) on (sw.virtualinterfaceid = vli.virtualinterfaceid)
	WHERE
	(	cu.type = ?
	OR	cu.type = ?
	OR	cu.type = ?
	)
	AND	cu.status = ?
	AND	sw.status = ?
EOF
if ($vlan) {
	$query .= 'AND vli.vlan = '.$dbh->quote($vlan);
}
if ($shortname) {
	$query .= 'AND cu.shortname = '.$dbh->quote($shortname);
}
$query .= <<EOF;
	GROUP BY
		virtualinterfaceid
	ORDER BY
		autsys
EOF

print STDERR Dumper ($query) if ($debug);

if (!($sth = $dbh->prepare($query))) {
	die "$dbh->errstr\n";
}
if (!$sth->execute(CUST_TYPE_FULL, CUST_TYPE_PROBONO, CUST_TYPE_INTERNAL, CUST_STATUS_NORMAL, PORTSTATUS_CONNECTED)) {
	die "$dbh->errstr\n";
}

$vars->{entries} = $sth->fetchall_hashref('virtualinterfaceid');

$query = <<EOF;
	SELECT
		cu.name,
		cu.autsys,
		cu.maxprefixes,
		cu.peeringemail,
		IF(LENGTH(cu.peeringmacro) > 1, cu.peeringmacro, CONCAT('AS', cu.autsys)) AS peeringmacro,
		COUNT(vli.virtualinterfaceid) AS numports,
		SUM(vli.ipv6enabled) AS ipv6enabled,
		SUM(vli.ipv4enabled) AS ipv4enabled,
		IF(SUM(vli.rsclient) >= 1, 1, 0) AS rsclient
	FROM
		view_cust_current_active cu
	LEFT JOIN (view_vlaninterface_details_by_custid vli) ON (vli.custid = cu.id)
	WHERE
	(	cu.type = ?
	OR	cu.type = ?
	OR	cu.type = ?
	)
	AND	cu.status = ?
EOF
if ($shortname) {
	$query .= 'AND cu.shortname = '.$dbh->quote($shortname);
}

$query .= <<EOF;
	GROUP BY
		name
	HAVING
		COUNT(vli.virtualinterfaceid) >= 1
	ORDER BY
		autsys
EOF

if (!($sth = $dbh->prepare($query))) {
	die "$dbh->errstr\n";
}
if (!$sth->execute(CUST_TYPE_FULL, CUST_TYPE_PROBONO, CUST_TYPE_INTERNAL, CUST_STATUS_NORMAL)) {
	die "$dbh->errstr\n";
}

$vars->{asnlist} = $sth->fetchall_hashref('autsys');

$vars->{ixp_rs_asn} = $ixpconfig->{ixp}->{rs_asn};
$vars->{ixp_rs_afilist} = ['ipv4', 'ipv6', 'vpnv4'];

my %hash = %{$vars->{entries}};
@{$vars->{sortedkeys}} = reverse sort { $hash{$b}->{autsys} <=> $hash{$a}->{autsys} } sort keys %hash;

if ($routeserver) {
	if ($protocol && $vlan) {
		$vars->{ixp_rs_address} = $vars->{network}->{'ipv'.$protocol}->{$vlan}->{'rs'.$routeserver.'address'};
		$vars->{ixp_rs_hostname} = 'rs'.$routeserver.'-vlan'.$vlan.'-ipv'.$protocol;
		$vars->{ixp_rs_routerid} = $vars->{network}->{'ipv4'}->{$vlan}->{'rs'.$routeserver.'address'};
	}
	$vars->{routeserver} = $routeserver;
}
if ($protocol) {
	$vars->{protocol} = 'ipv'.$protocol;
}

print STDERR Dumper ($vars) if ($debug);

foreach my $input (@ARGV) {
	$tt->process($input, $vars, \$output) || die $tt->error();
	print $output;
}
