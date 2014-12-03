#!/bin/sh

# ixpm-update-rs-info.sh
# Copyright (C) 2014 GRNET S.A.
# Written by Rowan Thorpe
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program. If not, see <http://www.gnu.org/licenses/>.

ROUTE_SERVERS='0 1 2'
RS_TARGETS='bird'
PEERING_LANS='100'
IP_VERSIONS='4 6'
MAIL_RECIPIENTS="noc@example-ixp.com"

APP_PATH="/opt/ixpmanager"
CONF_DIR="/etc/ixpmanager"
IXPM_CONF_FILE='/etc/ixpmanager.conf'
OUTPUT_DIR="/var/lib/ixpmanager/rsconfigs"
DEBUG=0
STDOUT=0
UPDATE_DB=1
USE_GIT=1

bork() {
    errorcode=$1
    shift
    printf 'ERROR: ixpm-update-rs-info.sh: %s. Failed with error code %d.\n' "$*" "$errorcode" >&2
    exit 1
}

quote_list() {
    for arg do
        printf "%s" "$arg" | sed -e "s/'/'\\\\''/g; s/^/'/; s/\$/', /"
    done | sed -e 's/, $//'
}

test 0 -eq `id -u` || bork $? 'not running as root'

# Getopts
while test 0 -ne $#; do
    case "$1" in
        --help|-h)
            cat <<EOF
Usage: ixpm-update-rs-info.sh [OPTIONS] [--]

DESCRIPTION

 Update IXP-Manager's prefix and ASN tables, and generate route-server configs, with
 git version-tracking and emailing on changes. Useful for running from cron for
 regular updates.

OPTIONS

 --help, -h      : this message
 --debug, -d     : spill info to stdout/stderr
 --stdout, -O    : output configs to stdout and don't generate files
 --no-update, -u : don't do the DB updates first, just generate configs
 --no-git, -g    : don't track changes in git

EOF
            exit 0
            ;;
        --debug|-d)
            DEBUG=1
            shift
            ;;
        --stdout|-O) # generate configs to stdout (AS/prefix-updates still update DB)
            STDOUT=1
            shift
            ;;
        --no-update|-u) # don't update ASes and prefixes, just generate configs
            UPDATE_DB=0
            shift
            ;;
        --no-git|-g)
            USE_GIT=0
            shift
            ;;
        --)
            shift
            break
            ;;
        -*)
            bork 1 "in getopts (option \"$1\")"
            ;;
        *)
            break
            ;;
    esac
done

# Set verbosity
if test 0 -eq $DEBUG; then
    verb=''
else
    verb="-v"
fi

# Update DB tables
if test 1 -eq $UPDATE_DB; then

    # Prefixes
    "${APP_PATH}/bin/ixptool.php" $verb -a 'irrdb-cli.update-prefix-db' || \
      bork $? 'irrdb-cli.update-prefix-db'

    # ASNs
    "${APP_PATH}/bin/ixptool.php" $verb -a 'irrdb-cli.update-asn-db' || \
      bork $? 'irrdb-cli.update-prefix-db'
fi

# Ensure configs dir exists
if test 0 -eq $STDOUT; then
    test -d "${OUTPUT_DIR}" || mkdir $verb -p "${OUTPUT_DIR}" || \
      bork $? "creating output dir \"$OUTPUT_DIR\""
fi

# Get database details from config file
ixpm_conf="$(cat "$IXPM_CONF_FILE")"
for field in dbase_type dbase_database dbase_username dbase_password dbase_hostname dbase_portname; do
    eval "${field}"'=`printf "%s" "$ixpm_conf" | sed -n -e "s/^[ \\t]*${field}[ \\t]*=[ \\t]*\\([^ \\t].*\\)\$/\1/; t PRINT; b; : PRINT; s/ \\+\$//; p; q"`'
done
case "$dbase_type" in
    mysql)
        # Create temp mysql defaults file
        trap 'test -z "$temp_defaults" || rm -f "$temp_defaults" 2>/dev/null' EXIT
        temp_defaults="`mktemp`" && chmod go= "$temp_defaults" || \
          bork $? 'creating temp defaults file'
        cat <<EOF >"$temp_defaults" || bork $? 'populating temp defaults file'
[mysql]
user=$dbase_username
password=$dbase_password
database=$dbase_database
`test -z "$dbase_hostname" || printf 'host=%s' "$dbase_hostname"`
`test -z "$dbase_portname" || printf 'port=%s' "$dbase_portname"`
skip-column-names
batch
EOF
        database_cmd="mysql --defaults-file=\"$temp_defaults\" | tr '\\n' ' ' | sed -e 's/\\t/|/g; s/ \$//'"
        ;;
    *)
        bork 1 "$dbase_type database type handling not yet implemented"
        ;;
esac

# Get peering lan ID mappings from the DB
peering_lan_maps="$(
    eval "printf 'select id, number from vlan where number in (%s);' \"\$(quote_list \$PEERING_LANS)\" | $database_cmd"
)" || bork $? "getting IDs for peering lans \"$PEERING_LANS\" from database \"$dbase_database\""

# Enter output dir
cd "${OUTPUT_DIR}" || bork $? 'entering the output directory'

# Initialise git if not already done
if test 0 -eq $STDOUT && test 1 -eq $USE_GIT && ! test -d '.git'; then
    git init >/dev/null 2>&1 || bork $? 'initialising git repo'
fi

# Generate Route Server configs
for vlan in $peering_lan_maps; do
    vlan_id=`printf '%s' "$vlan" | cut -d\| -f1`
    vlan_num=`printf '%s' "$vlan" | cut -d\| -f2`
    for rs in $ROUTE_SERVERS; do
        for target in $RS_TARGETS; do
            for ipv in $IP_VERSIONS; do

                # Generate file, or just stdout?
                if test 0 -eq $STDOUT; then
                    outfile="rs${rs}-vlan${vlan_num}-ipv${ipv}.conf"
                else
                    outfile="/dev/stdout"
                fi

                # Generate config
                "${APP_PATH}/bin/ixptool.php" $verb -a 'router-cli.gen-server-conf' \
                  -p vlanid=${vlan_id},target=${target},proto=${ipv} \
                  --config="${CONF_DIR}/rs${rs}-vlan${vlan_num}-ipv${ipv}.conf" \
                  >"$outfile" || \
                    bork $? "router-cli.gen-server-conf (rs:$rs, target:$target, vlan:$vlan, ipv:$ipv)"

                # Spacer for stdout
                if test 1 -eq $STDOUT; then
                    printf '\n======\n'
                fi

            done
        done
    done
done

# Commit and email changes
if test 0 -eq $STDOUT && test 1 -eq $USE_GIT && test -n "`git status --porcelain 2>/dev/null`"; then

    #TODO: capture stderr output for $DEBUG use below... do we care?

    # Commit
    now=$(date --rfc-3339=seconds 2>/dev/null) && \
      git add . >/dev/null 2>&1 && \
        git commit -m "Config-changes at $now" >/dev/null 2>&1 || \
          bork $? "git-committing config-changes"

    # Email
    if test 1 -eq `git rev-list --min-parents=0 'HEAD' 2>/dev/null | wc -l` || \
      test -n "`git diff --patch-with-raw 'HEAD^' 2>/dev/null | grep -v '^\([^+-]\|--- \|+++ \|[+-]# \+Generated: \)'`"; then
        hostname="`hostname`"
        {
            printf 'Route server config changes on $hostname, git-diff output:\n\n'
            # try "previous -> present commit", fallback to show whole log if only one commit
            git log -p --stat 'HEAD^..HEAD' 2>/dev/null || \
              git log -p --stat 2>/dev/null
        } | mail -a "From: IXP-Manager <root@example-ixp.com>" -s "Route server config changes on $hostname" $MAIL_RECIPIENTS >/dev/null 2>&1 || \
          bork $? 'emailing config-changes'
    fi

fi
