#!/bin/sh
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

PATH=/usr/local/bin:$PATH
export PATH

querytype="$1"
shift

query="$1"
shift

if [ "X"${querytype} = "Xprefixlist" ]; then
	echo "${query}" | \
		peval $* | \
		grep '^({' |\
		cut -d \{ -f2 | cut -d \} -f1 | \
		perl -ne '
			my @arr = split(/\s+/);
			print join(" ", @arr)."\n";
		'
		
elif [ "X"${querytype} = "Xasnlist" ]; then
	echo "${query}" | \
		peval -no-as $* | \
		grep '^((' |\
		cut -d \( -f3 | cut -d \) -f1 | \
		perl -ne '
			s/AS//g;
			my @arr = split(/\s+/);
			print join(", ", @arr)."\n";
		'
fi

exit $?
