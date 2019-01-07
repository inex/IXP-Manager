#!/bin/sh

# Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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
# FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License v2.0
# along with IXP Manager.  If not, see:
#
# http://www.gnu.org/licenses/gpl-2.0.html

ROOT=`dirname $0`/..

/usr/local/bin/phpdoc -d $ROOT/application/models,$ROOT/library/IXP -t $ROOT/data/phpdoc/ -ti 'IXP Manager :: Auto Generated Documentation' \
    -dc 'IXP-Undefined' -dn 'IXP-Undefined' -s -o "HTML:frames:earthli"


