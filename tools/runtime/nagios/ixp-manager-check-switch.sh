#! /usr/bin/env bash
#
# Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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
# Barry O'Donovan <barry.odonovan -at- inex.ie>

# Example Nagios check for switch status

# See: http://docs.ixpmanager.org/features/nagios/

# Parse arguments
APIKEY=""
DEBUG=0
ID=""
URL=""

usage() { echo "Usage: $0 -k <apikey> -i <switch-db-id> -u <ixp-manager-base-url> [-d]" 1>&2; exit 1; }

hash jq   2>/dev/null || { echo >&2 "I require jq but it's not installed.  Aborting."; exit 1; }
hash wget 2>/dev/null || { echo >&2 "I require wget but it's not installed.  Aborting."; exit 1; }

while getopts ":k:i:u:d" o; do
    case "${o}" in
        k)
            APIKEY=${OPTARG}
            ;;
        i)
            ID=${OPTARG}
            ;;
        u)
            URL=${OPTARG}
            ;;
        d)
            DEBUG=1
            ;;
        *)
            usage
            ;;
    esac
done
shift $((OPTIND-1))

if [ -z "${APIKEY}" ] || [ -z "${ID}" ]; then
    usage
fi


STATUS="$( wget -O - -q  "${URL}/api/v4/switch/${ID}/status?apikey=${APIKEY}" )"

if [ $? -ne 0 ] || [ -z "$STATUS" ]; then
    echo Could not query switch status via API
    exit 3
fi

if [ "X`echo $STATUS | jq .status`X" = "XtrueX" ]; then
    RETCODE=0;
else
    RETCODE=2;
fi

echo $STATUS | jq -r '.msgs[]' | while read line; do
    echo -n "${line} "
done

echo

exit $RETCODE

