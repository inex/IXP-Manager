<?php if( $t->forJson ): ?>
IXPM-OBJECT:    aut-num
IXPM-KEY:       AS66500<?php else: ?>
password:       <?= config('ixp_api.rir.password') ?>
<?php endif; ?>

as-set:         AS-SET-IXP-RS
descr:          ASNs connected to the Route Server system at IXP
admin-c:        XYZ-RIPE
tech-c:         XYZ-RIPE
notify:         ripe-notify@example.com
remarks:        IXP member ASNs are listed in AS-SET-IXP-CONNECTED
mnt-by:         IXP-NOC
<?php foreach( $t->customers as $c ): ?>
<?php if( $c->routeServerClient( ) ): ?>
members:        <?= $c->asMacro( 4, 'AS' ) ?>

<?php endif; ?>
<?php endforeach; ?>
source:         RIPE