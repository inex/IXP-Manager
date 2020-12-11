password: <?= config('ixp_api.rir.password') ?>


    aut-num:        AS66500
    as-name:        FOOBAR
    descr:          Some City Internet Exchange Association Limited
<?php foreach( $t->asns as $asn => $details ): ?>
<?php if( $asn !== 2128 ): ?>
    import:         from AS<?= $asn ?> accept <?= $details[ "asmacro" ] ?> # <?= $details[ "name" ] ?>

    export:         to   AS<?= $asn ?> announce AS-MYIXPASSET
<?php endif; ?>
<?php endforeach; ?>
    org:            ORG-FOOBAR-RIPE
    admin-c:        FOOBAR-RIPE
    tech-c:         FOOBAR-RIPE
    notify:         ripe-notify@example.com
    mnt-by:         FOOBAR-IXP-NOC
    mnt-by:         RIPE-NCC-END-MNT
    mnt-routes:     FOOBAR-IXP-NOC
    source:         RIPE