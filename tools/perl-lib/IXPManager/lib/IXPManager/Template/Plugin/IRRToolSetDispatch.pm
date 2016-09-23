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
##
## This package dispatches the text through the RtConfig command with the
## correct command-line parameters to ensure that we can implement
## per-customer IRRDB settings.
##

package IXPManager::Template::Plugin::IRRToolSetDispatch;

use File::Temp qw( tempfile );
use Template::Plugin::Filter;
use base qw( Template::Plugin::Filter );
use NetAddr::IP;
use Data::Dumper;
use IXPManager::Const;
use IXPManager::DBI;

# We need this to be a dynamic filter
our $DYNAMIC = 1;

sub filter {
	my ($self, $text, $args, $conf) = @_;
	my ($tmpfile, $host, $sourcelist, $protocol);
	my $returntext = '';

	# We need to pipe data into RtConfig and expect data back from it. 
	# However, perl does not support open "| $pipeprog |", as we'd like. 
	# This can be worked around using IPC::Open2, but there are a bunch
	# of issues associated with this, including trap / error handling
	# and also wait().  Basically, it's too low level a command for
	# perl.  It's simpler to pipe the output of RtConfig to a temporary
	# file and then read that file in.  Less messing around with low
	# level components means more reliability.

	(undef, $tmpfile) = tempfile();

	# If this is a RIPE query, we go directly to RIPE.  Otherwise we go
	# to RADB and set the source filter to be RIPE,XXXX, where XXXX is
	# the source name for the registry in question.

	my $dbh = new IXPManager::DBI;
	my $irrdbhash = $dbh->selectall_hashref('SELECT id, host, protocol, source FROM irrdbconfig', 'id');
	my $irrconfig = $irrdbhash->{$conf->{irrdb}};

	# in order to pick up the definition for AS43760, we need to prefix everything with RIPE,<blah>
	if ($irrconfig->{source} !~ /RIPE/) {
		$irrconfig->{source} = 'RIPE,'.$irrconfig->{source};
	}
		
	my $pipe = "/usr/local/bin/rtconfig -cisco_use_prefix_lists ".
			" -h $irrconfig->{host} -protocol $irrconfig->{protocol} -s $irrconfig->{source} > $tmpfile";

	print STDERR "$pipe\n$text\n";

	open (PIPE, "| $pipe");
	print PIPE $text;
	close (PIPE);

	my $retval = $? >> 8;
	if ($retval) {
		unlink ($tmpfile);
		die "aborting: \"$pipe\" returned $retval.\n";
	}
	
	open (INPUT, $tmpfile);
	while (<INPUT>) {
		if (0) {
		# lines of form:
		# neighbor 193.242.111.6 route-map inex-rsclient-as112-ipv4-export in
		} elsif (/neighbor\s+(\S+)\s+route-map\s+(\S+)-export\s+in\s*$/) {
			$returntext .= " neighbor $1 route-map $2-export export\n";
		} elsif (/^ipv6 access-list\s+(\S+)\s+(permit|deny)\s+(\S+)/) {
			$returntext .= "ipv6 access-list $1 $2 $3";
			if ($2 eq 'permit') {
				$returntext .= " exact-match";
			}
			$returntext .= "\n";
		} elsif (/address-family (\S+).unicast/) {
			$returntext .= " address-family $1\n";
		} else {
			$returntext .= $_;
		}
	}

	close (INPUT);
	unlink ($tmpfile);

	return $returntext;
}

1;
