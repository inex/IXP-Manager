#!/bin/bash

# Example script used by INEX to rebuild all Bird route servers via API on demand.
#
# Barry O'Donovan <barry@opensolutions.ie>

# Edit as appropriate 

echo "Reconfiguring all bird instances:"

for vlanid in x y z; do
    echo -ne "VLAN ID ${vlanid}: "
    for proto in 4 6; do
        echo -ne "\tIPv${proto}: "
        /usr/local/sbin/api-reconfigure-example.sh -v $vlanid -p $proto -q
        if [[ $? -eq 0 ]]; then
            echo -ne "OK    "
        else
            echo -ne "ERROR "
        fi
    done
    echo
done

