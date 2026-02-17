<?php if( $t->forJson ): ?>
IXPM-OBJECT:    aut-num
IXPM-KEY:       AS66500<?php else: ?>
password:       <?= config('ixp_api.rir.password') ?>
<?php endif; ?>

aut-num:        AS66500
as-name:        FOOBAR
descr:          Some City Internet Exchange Association Limited
<?php foreach( $t->asns as $asn => $details ): ?>
<?php if( $asn !== 2128 ): ?>
import:         from AS<?= $asn ?> accept <?= $details[ "asmacro" ] ?> # <?= $details[ "name" ] ?>

export:         to   AS<?= $asn ?> announce AS-MYIXPASSET
<?php endif; ?>
<?php endforeach; ?>
org:            ORG-BT1-TEST
admin-c:        BA1-TEST
tech-c:         BA1-TEST
notify:         ripe-notify@example.com
mnt-by:         BARRYTEST-MNT
mnt-by:         RIPE-NCC-END-MNT
source:         TEST-NONAUTH
