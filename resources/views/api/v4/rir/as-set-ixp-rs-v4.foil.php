password: <?= config('ixp_api.rir.password') ?>


    as-set:         AS-SET-IXP-RS-V4
    descr:          ASNs connected to the Route Server system at IXP via IPv4
    admin-c:        XYZ-RIPE
    tech-c:         XYZ-RIPE
    notify:         ripe-notify@example.com
    remarks:        IXP member ASNs are listed in AS-SET-IXP-CONNECTED
    mnt-by:         IXP-NOC
<?php foreach( $t->customers as $c ): ?>
<?php if( $c->routeServerClient( 4 ) ): ?>
    members:        <?= $c->asMacro( 4, 'AS' ) ?>

<?php endif; ?>
<?php endforeach; ?>
    source:         RIPE
