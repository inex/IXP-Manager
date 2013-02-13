# /bin/bash

ROOT=$(dirname $0)/..

/usr/local/bin/phpdoc -d $ROOT/application/models,$ROOT/library/IXP -t $ROOT/data/phpdoc/ -ti 'IXP Manager :: Auto Generated Documentation' \
    -dc 'IXP-Undefiend' -dn 'IXP-Undefined' -s -o "HTML:frames:earthli"


