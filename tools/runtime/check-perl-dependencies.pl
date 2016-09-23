#!/usr/bin/perl -w
#
# check-perl-dependencies.pl
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
# This script checks to ensure that all IXP Manager perl dependencies are
# installed and prints out some suggested installation commands if they are
# not.

use strict;
use Data::Dumper;

# FIXME:
# http://stackoverflow.com/questions/16927024/perl-5-20-and-the-fate-of-smart-matching-and-given-when
no if $] >= 5.018, warnings => "experimental::smartmatch";

my @dependencies = (
	# this ludicrousfail is for redhat and derivatives
	{ name => 'Data::Dumper',	pkgng => '',			apt => '',				redhat => 'perl-Data-Dumper' },
	{ name => 'Time::HiRes',	pkgng => '',			apt => '',				redhat => 'perl-Time-HiRes' },
	{ name => 'Module::Build',	pkgng => '',			apt => '',				redhat => '' },
	{ name => 'Crypt::DES',		pkgng => '',			apt => '',				redhat => 'perl-Crypt-DES' },
	{ name => 'Crypt::Rijndael',	pkgng => '',			apt => '',				redhat => '' },
	{ name => 'Digest::SHA1',	pkgng => '',			apt => '',				redhat => 'perl-Digest-SHA1' },

	# operating systems which work: 
	{ name => 'Config::General',	pkgng => 'p5-Config-General',	apt => 'libconfig-general-perl',	redhat => '' },
	{ name => 'DBD::mysql',		pkgng => 'p5-DBD-mysql',	apt => 'libdbd-mysql-perl',		redhat => 'perl-DBD-MySQL' },
	{ name => 'DBI',		pkgng => 'p5-DBI',		apt => 'libdbi-perl',			redhat => 'libdbi-dbd-mysql' },
	{ name => 'Daemon::Control',	pkgng => 'p5-Daemon-Control',	apt => 'libdaemon-control-perl',	redhat => '' },
	{ name => 'NetAddr::IP',	pkgng => 'p5-NetAddr-IP',	apt => 'libnetaddr-ip-perl',		redhat => 'perl-NetAddr-IP' },
	{ name => 'NetPacket::TCP',	pkgng => 'p5-NetPacket-TCP',	apt => 'libnetpacket-perl',		redhat => '' },
	{ name => 'Net::SNMP',		pkgng => 'p5-Net-SNMP',		apt => 'libnet-snmp-perl',		redhat => '' },
	{ name => 'Net_SNMP_util',	pkgng => 'mrtg',		apt => 'mrtg',				redhat => '' },
	{ name => 'RRDs',		pkgng => 'rrdtool',		apt => 'librrds-perl',			redhat => 'rrdtool-perl' },
	{ name => 'Template',		pkgng => 'p5-Template-Toolkit',	apt => 'libtemplate-perl',		redhat => 'perl-Template-Toolkit' },

);

my ($pkginstaller, $deptype);
if ($^O eq 'freebsd') { $pkginstaller = 'pkg install'; $deptype = 'pkgng'; }
elsif ($^O eq 'darwin')	{ $pkginstaller = 'port install'; $deptype = 'pkgng'; }
elsif ($^O eq 'linux') {
	if (-f '/etc/redhat-release') {
		$pkginstaller = 'yum install'; $deptype = 'redhat';
	} else {
		my $distro = lc(`lsb_release -i -s`);
		chomp ($distro);
		if ($distro =~ /ubuntu|debian/) {
			$pkginstaller = 'apt-get install'; $deptype = 'apt';
		} else {
			warn "unknown linux distribution o/s.\n";
		}
	}
} else {
	warn "unsupported o/s.\n";
}

print "Checking IXP Manager Perl dependencies:\n\n";

my (@pkglist, @cpanlist);
foreach my $pkg (@dependencies) {
	eval "use $pkg->{name}";
	if ($@) {
		print "\t".$pkg->{name}." - not installed\n";
		if ($deptype) {
			if ($pkg->{$deptype}) {
				push (@pkglist, $pkg->{$deptype});
			} else {
				push (@cpanlist, $pkg->{name});
			}
		}
	}
}

if ($#pkglist > -1 || $#cpanlist > -1) {
	print "\nSuggested installation command:\n\n";
	print "\t$pkginstaller ".join (' ', @pkglist)."\n\n" if ($#pkglist > -1);
	print "\tcpan ".join (' ', @cpanlist)."\n\n" if ($#cpanlist > -1);
} else {
	print "All IXP Manager dependencies installed\n";
}
