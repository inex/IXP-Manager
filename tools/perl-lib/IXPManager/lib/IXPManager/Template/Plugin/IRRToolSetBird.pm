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
##
## This package provides a Template::Toolkit filter to allow per-customer
## IRRDB settings.
##

package IXPManager::Template::Plugin::IRRToolSetBird;

use File::Temp qw( tempfile );
use Template::Plugin::Filter;
use base qw( Template::Plugin::Filter );
use NetAddr::IP;
use Data::Dumper;
use IXPManager::Const;
use IXPManager::Config;

# We need this to be a dynamic filter
our $DYNAMIC = 1;

sub irrdbget {
	my ($self, $conf) = @_;
	my ($tmpfile, $prefixorasn, $returntext);

	print STDERR Dumper ($conf) if ($ixp->{ixp}->{debug});

	my $ixp = new IXPManager::Config;
	my $dbh = $ixp->{db};

	# If this is a RIPE query, we go directly to RIPE.  Otherwise we go
	# to RADB and set the source filter to be RIPE,XXXX, where XXXX is
	# the source name for the registry in question.  We leave this in
	# for compatibility with IRRToolSetDispatch which needs it for
	# resolution of aut-num: objects in the RIPE region.

	$prefixorasn = ($conf->{type} eq 'asnlist') ? 'asnlist' : 'prefixlist';

	my $irrdbhash = $dbh->selectall_hashref('SELECT id, host, protocol, source FROM irrdbconfig', 'id');

	# if the operator has not specified which IRRDB profile to use, then
	# choose the one with lowest ID
	if (!defined ($irrdbhash->{$conf->{irrdb}})) {
		my @keys = sort keys %{$irrdbhash};
		$conf->{irrdb} = $keys[0];
	}
	my $irrconfig = $irrdbhash->{$conf->{irrdb}};

	if ($irrconfig->{source} !~ /RIPE/) {
		$irrconfig->{source} = 'RIPE,'.$irrconfig->{source};
	}

	my $pipe = "$ixp->{ixp}->{rs_peval_bird} $prefixorasn '$conf->{filter}'".
			" -h $irrconfig->{host} -protocol $irrconfig->{protocol} -s $irrconfig->{source}";

	print STDERR "$pipe\n" if ($ixp->{ixp}->{debug});

	open (INPUTPIPE, "$pipe |");
	$returntext = <INPUTPIPE>;
	chomp($returntext);
	close (INPUTPIPE);

	my $exitval = $? >> 8;
	if ($exitval != 0) {
		die ("ABORT: \"$ixp->{ixp}->{rs_peval_bird}\" returned %d after exiting...\n", $exitval);
	}

	if (!$returntext) {
		$returntext = 'NOT ANY';
	}

	print STDERR "returntext: \"$returntext\"\n" if ($ixp->{ixp}->{debug});

	return $returntext;
}

1;
