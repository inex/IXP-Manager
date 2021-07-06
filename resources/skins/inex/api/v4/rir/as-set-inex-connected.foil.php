password: <?= config('ixp_api.rir.password') ?>


as-set:         AS-SET-INEX-CONNECTED
descr:          ASNs connected to INEX
descr:          INEX is an IXP located in Dublin, Ireland
admin-c:        INO7-RIPE
tech-c:         INO7-RIPE
notify:         ripe-notify@inex.ie
remarks:        INEX route server ASNs are listed in AS-SET-INEX-RS
mnt-by:         INEX-NOC
<?php foreach( $t->asns as $asn => $details ): ?>
<?php if( $asn !== 43760 ): ?>
members:        <?= $details[ "asmacro" ] ?>

<?php endif; ?>
<?php endforeach; ?>
source:         RIPE
