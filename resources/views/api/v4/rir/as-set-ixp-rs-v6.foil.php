<?php if( $t->forJson ): ?>
IXPM-OBJECT:    aut-num
IXPM-KEY:       AS66500<?php else: ?>
password:       <?= config('ixp_api.rir.password') ?>
<?php endif; ?>

as-set:         AS-SET-IXP-RS-V6
descr:          ASNs connected to the Route Server system at IXP via IPv6
admin-c:        XYZ-RIPE
tech-c:         XYZ-RIPE
notify:         ripe-notify@example.com
remarks:        IXP member ASNs are listed in AS-SET-IXP-CONNECTED
mnt-by:         IXP-NOC
<?php foreach( $t->customers as $c ): ?>
<?php if( $c->routeServerClient( 6 ) ): ?>
members:        <?= $c->asMacro( 6, 'AS' ) ?>

<?php endif; ?>
<?php endforeach; ?>
source:         RIPE