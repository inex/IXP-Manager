# /bin/bash

ROOT=$(dirname $0)/..

/usr/local/bin/phpdoc -d $ROOT/application/models,$ROOT/library/INEX -t $ROOT/data/phpdoc/ -ti 'INEX IXP Manager :: Auto Generated Documentation' \
    -dc 'IXP-Undefiend' -dn 'IXP-Undefined' -s -o "HTML:frames:earthli"


