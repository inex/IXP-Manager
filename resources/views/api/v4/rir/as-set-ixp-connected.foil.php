password: <?= config('ixp_api.rir.password') ?>


    as-set:         AS-SET-IXP-CONNECTED
    descr:          ASNs connected to the IXP
    admin-c:        XYZ-RIPE
    tech-c:         XYZ-RIPE
    notify:         ripe-notify@example.com
    remarks:        IXP member ASNs connected to the route servers are listed in AS-SET-IXP-CONNECTED
    mnt-by:         IXP-NOC
<?php foreach( $t->asns as $asn => $details ): ?>
    members:        <?= $details[ "asmacro" ] ?>

<?php endforeach; ?>
    source:         RIPE
