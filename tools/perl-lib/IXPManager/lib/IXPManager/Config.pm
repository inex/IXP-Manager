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
# IXPManager::Config
#
# A Config interface

package IXPManager::Config;

# Be neutoric about syntax
use strict;

# These packages are part of the base perl system 
use Carp;

# These packages are from CPAN
use DBI;

# Data::Dumper is used solely for debugging
use Data::Dumper;

use vars qw(@ISA @EXPORT_OK @EXPORT $VERSION $AUTOLOAD);

@ISA = ("Config");

$VERSION = '0.24';

1;


##
## new
##  
sub new {
	use Config::General;

	my ($type) = shift if @_;
	my $class = ref($type) || $type || "IXPManager::Config";
	my %args = @_;
	my @tags = qw (configfile debug);

	my $self = {
		version		=> $VERSION,
		configfile	=> '/usr/local/etc/ixpmanager.conf',
		class		=> $class,
		debug		=> 0,
	};

	foreach my $tag (@tags) {
		$self->{$tag} = $args{$tag} if (defined $args{$tag});
	}

	my $confhdl = new Config::General (
		-ConfigFile     => $self->{configfile},
		-LowerCaseNames => 1,
	);

	my $conf = { $confhdl->getall };

	my $datasource = "DBI:$conf->{sql}->{dbase_type}:database=$conf->{sql}->{dbase_database}";
	if ($conf->{sql}->{dbase_hostname}) {
		$datasource .= ";host=$conf->{sql}->{dbase_hostname}";
	}
	if ($conf->{sql}->{dbase_portname}) {
		$datasource .= ";port=$conf->{sql}->{dbase_portname}";
	}

	my $attr = { RaiseError => 1, AutoCommit => 1 };

	$self->{db} = DBI->connect (
		$datasource,
		$conf->{sql}->{dbase_username},
		$conf->{sql}->{dbase_password},
		$attr
	);

	$self->{ixp} = $conf->{ixp};

	bless $self, $class;

	return $self;
}
