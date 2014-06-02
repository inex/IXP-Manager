password: {$options.rir.ripe_password}

aut-num:        AS2128
as-name:        INEX
descr:          Internet Neutral Exchange Association Limited
{foreach $asns as $asn => $details}
{if $asn != 2128}
import:         from AS{$asn} accept {$details.asmacro} # {$details.name}
export:         to   AS{$asn} announce AS-INEXIE
{/if}
{/foreach}
org:            ORG-INEX1-RIPE
admin-c:        INO7-RIPE
tech-c:         INO7-RIPE
notify:         ripe-notify@inex.ie
mnt-by:         INEX-NOC
mnt-by:         RIPE-NCC-END-MNT
mnt-routes:     INEX-NOC
changed:        mnorris@hea.ie 19960601
changed:        ripe-dbm@ripe.net 19990701
changed:        mike.norris@heanet.ie 19990705
changed:        dave.wilson@heanet.ie 20020312
changed:        dave.wilson@heanet.ie 20020809
changed:        nick@iol.ie 20020826
changed:        nick-ripe@inex.ie 20050611
changed:        nick-ripe@inex.ie 20060512
changed:        ripe-admin@inex.ie 20071119
changed:        ripe-admin@inex.ie
status:         ASSIGNED
source:         RIPE
