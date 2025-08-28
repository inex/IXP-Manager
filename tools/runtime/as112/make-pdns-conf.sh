#! /bin/bash


# Resolve the absolute path of the script's directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
EXPECTED_DIR="tools/runtime/as112"

if [[ "$SCRIPT_DIR" != *"$EXPECTED_DIR" ]]; then
  echo "Error: Script must be located in $EXPECTED_DIR"
  exit 1
fi

cat <<END_HEADER

==== PowerDNS configuration builder

This simple script will gather the powerdns configuration files and the
zone files into a tar.bz2 which is ready for production deployment on an
as112 server.

Any existing powerdns.tar.bz2 in this directory will be overwrittem.

END_HEADER

read -p "Do you want to continue? (y/n): " confirm

if [[ "$confirm" != "y" && "$confirm" != "Y" ]]; then
  echo "Aborted."
  exit 1
fi

mkdir -p tmp/powerdns/zones
cp powerdns/{named.conf,pdns.conf} tmp/powerdns/
cp zones/* tmp/powerdns/zones/
cd tmp/
tar jcf ../powerdns.tar.bz2 .
cd ..
rm -rf tmp/

echo -e "\nDONE\n"

