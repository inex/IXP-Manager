password: <?= config('ixp_api.rir.password') ?>


as-set:         AS-SET-INEX-RS
descr:          ASNs connected to the Route Server system at INEX
descr:          INEX is an IXP located in Dublin, Ireland
admin-c:        INO7-RIPE
tech-c:         INO7-RIPE
notify:         ripe-notify@inex.ie
remarks:        INEX member ASNs are listed in AS-SET-INEX-CONNECTED
mnt-by:         INEX-NOC
<?php foreach( $t->customers as $c ): ?>
<?php if( $c->routeServerClient( ) ): ?>
members:        <?= $c->asMacro( 4, 'AS' ) ?>

<?php endif; ?>
<?php endforeach; ?>
source:         RIPE
