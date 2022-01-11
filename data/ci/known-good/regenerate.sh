#! /bin/bash

KEY="Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC9"
URL="http://127.0.0.1:8080/apiv1"
WGET="/usr/bin/wget"
BIRD="/usr/sbin/bird"

for skin in none inex; do

    cp ../htaccess-${skin}-skin ../../../public/.htaccess
    SKIN="${skin}-"
        
    for proto in 4 6; do
        for vlanid in 1 2; do
    
            cp ../configs/ci-rs1-conf-vlanid${vlanid}-ipv${proto}.conf ../../../application/configs

            ${WGET} -q    \
                ${URL}/router/server-conf/key/${KEY}/target/bird/vlanid/${vlanid}/proto/${proto}/config/ci-rs1-conf-vlanid${vlanid}-ipv${proto} \
                -O ${skin}-ci-rs1-vlanid${vlanid}-ipv${proto}.conf

            rm ../../../application/configs/ci-rs1-conf-vlanid${vlanid}-ipv${proto}.conf
            
            if [[ $proto -eq 6 ]]; then
                BIRDCMD="${BIRD}6"
            else 
                BIRDCMD="${BIRD}"
            fi
        
            $BIRDCMD -p -c ${skin}-ci-rs1-vlanid${vlanid}-ipv${proto}.conf
        
            if [[ $? -ne 0 ]]; then
                echo ERROR: Config check failed for: ${skin}-ci-rs1-vlanid${vlanid}-ipv${proto}.conf
            fi
        done
    done

done
