password: {$options.rir.ripe_password}

as-set:         AS-SET-IXP-RS-V4
descr:          ASNs connected to the Route Server system at IXP via IPv4
admin-c:        XYZ-RIPE
tech-c:         XYZ-RIPE
notify:         ripe-notify@example.com
remarks:        IXP member ASNs are listed in AS-SET-IXP-CONNECTED
mnt-by:         IXP-NOC
{foreach $customers as $c}
{if $c->isRouteServerClient( 6 )}
members:        {$c->resolveAsMacro( 6, 'AS' )}
{/if}
{/foreach}
source:         RIPE
