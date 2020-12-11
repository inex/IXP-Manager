password: <?= config('ixp_api.rir.password') ?>


aut-num:        AS2128
as-name:        INEX
descr:          Internet Neutral Exchange Association Company Limited By Guarantee
<?php foreach( $t->asns as $asn => $details ): ?>
<?php if( $asn !== 2128 ): ?>
import:         from AS<?= $asn ?> accept <?= $details[ "asmacro" ] ?> # <?= $details[ "name" ] ?>

export:         to   AS<?= $asn ?> announce AS-INEXIE
<?php endif; ?>
<?php endforeach; ?>
org:            ORG-INEA1-RIPE
admin-c:        INO7-RIPE
tech-c:         INO7-RIPE
notify:         ripe-notify@inex.ie
mnt-by:         INEX-NOC
mnt-by:         RIPE-NCC-END-MNT
status:         ASSIGNED
source:         RIPE
