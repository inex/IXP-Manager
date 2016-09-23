# IXPManager::Utils
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

package IXPManager::Utils;

# Be neutoric about syntax
use strict;

# These packages are part of the base perl system 
use Carp;

# Data::Dumper is used solely for debugging
use Data::Dumper;

# Pull in other IXPManager stuff
use IXPManager::Const;

use vars qw(@ISA @EXPORT_OK @EXPORT $VERSION $AUTOLOAD);

our @EXPORT = qw( switchporttosnmpidentifier );

1;

sub switchporttosnmpidentifier {
	my ($shortport, $vendorid) = @_;

	if ($vendorid == VENDORID_CISCO) {
		$shortport =~ s/tengigabitethernet/Te/gi;
		$shortport =~ s/gigabitethernet/Gi/gi;
		$shortport =~ s/fastethernet/Fa/gi;
	} elsif ($vendorid == VENDORID_BROCADE)  {
		$shortport =~ s/10gigabitethernet/ethernet/gi;
		$shortport =~ s/gigabitethernet/ethernet/gi;
		$shortport =~ s/Management/management/gi;
	}

	return $shortport;
}

sub switchportifnametosnmpidentifier {
       my ($ifname) = @_;

       # escape special characters in ifName as per
       # http://oss.oetiker.ch/mrtg/doc/mrtg-reference.en.html - "Interface by Name" section

       $ifname =~ s/:/\\:/g; 
       $ifname =~ s/&/\\&/g;   
       $ifname =~ s/@/\\@/g;  
       $ifname =~ s/\ /\\\ /g; 
       
       return $ifname;
}
