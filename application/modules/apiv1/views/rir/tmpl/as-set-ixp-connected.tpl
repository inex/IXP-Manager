password: {$options.rir.ripe_password}

as-set:         AS-SET-IXP-CONNECTED
descr:          ASNs connected to the IXP
admin-c:        XYZ-RIPE
tech-c:         XYZ-RIPE
notify:         ripe-notify@example.com
remarks:        IXP member ASNs connected to the route servers are listed in AS-SET-IXP-RS
mnt-by:         IXP-NOC
{foreach $asns as $asn => $details}
members:        {$details.asmacro}
{/foreach}
source:         RIPE
