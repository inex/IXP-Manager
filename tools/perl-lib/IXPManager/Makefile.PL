# $Id: Makefile.PL,v 1.2 2007/03/08 23:30:45 nick Exp $
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

require 5.002;
use ExtUtils::MakeMaker;

WriteMakefile(
	NAME		=> 'IXPManager',
	VERSION_FROM	=> 'lib/IXPManager/Config.pm',

	PREREQ_PM	=> {
		Config::General => 0,
		NetAddr::IP => 0,
		DBI => 0,
	},

	'dist'		=> {
		'COMPRESS' => 'gzip -9f',
		'SUFFIX' => 'gz'
	},
);
