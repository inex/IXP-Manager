password: {$options.rir.ripe_password}

as-set:         AS-SET-INEX-RS
descr:          ASNs connected to the Route Server system at INEX
descr:          INEX is an IXP located in Dublin, Ireland
admin-c:        INO7-RIPE
tech-c:         INO7-RIPE
notify:         ripe-notify@inex.ie
remarks:        INEX member ASNs are listed in AS-SET-INEX-CONNECTED
mnt-by:         INEX-NOC
{foreach $customers as $c}
{if $c->isRouteServerClient()}
members:        {$c->resolveAsMacro( 4, 'AS' )}
{/if}
{/foreach}
source:         RIPE
