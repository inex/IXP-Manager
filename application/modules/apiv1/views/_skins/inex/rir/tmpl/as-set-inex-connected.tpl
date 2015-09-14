password: {$options.rir.ripe_password}

as-set:         AS-SET-INEX-CONNECTED
descr:          ASNs connected to INEX
descr:          INEX is an IXP located in Dublin, Ireland
admin-c:        INO7-RIPE
tech-c:         INO7-RIPE
notify:         ripe-notify@inex.ie
remarks:        INEX route server ASNs are listed in AS-SET-INEX-RS
mnt-by:         INEX-NOC
{foreach $asns as $asn => $details}
{if $asn != 43760}
members:        {$details.asmacro}
{/if}
{/foreach}
source:         RIPE
