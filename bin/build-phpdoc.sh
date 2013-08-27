#!/bin/sh

ROOT=`dirname $0`/..

/usr/local/bin/phpdoc -d $ROOT/application/models,$ROOT/library/IXP -t $ROOT/data/phpdoc/ -ti 'IXP Manager :: Auto Generated Documentation' \
    -dc 'IXP-Undefined' -dn 'IXP-Undefined' -s -o "HTML:frames:earthli"


