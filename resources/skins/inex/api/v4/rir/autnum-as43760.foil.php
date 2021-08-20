password: <?= config('ixp_api.rir.password') ?>


aut-num:        AS43760
as-name:        INEX-RS
descr:          Internet Neutral Exchange Association Company Limited By Guarantee
remarks:        ------------------------------------------------------------------
remarks:
remarks:        INEX Route Server Routing Policy:
remarks:
remarks:        Large Communities Support - inex:action:rsclient
remarks:        prevent announcement of a prefix to a peer    43760:0:peer-as
remarks:        announce a route to a certain peer            43760:1:peer-as
remarks:        prevent announcement of a prefix to all peers 43760:0:0
remarks:        announce a route to all peers                 43760:1:0
remarks:
remarks:        Standard Communities Support:
remarks:        prevent announcement of a prefix to a peer    0:peer-as
remarks:        announce a route to a certain peer            43760:peer-as
remarks:        prevent announcement of a prefix to all peers 0:43760
remarks:        announce a route to all peers                 43760:43760
remarks:
remarks:        Notes:
remarks:        - large communities are evaluated before standard bgp
remarks:          communities.
remarks:        - we use a per-client RIB
remarks:        - local-preference is not modified in our RIBs
remarks:        - AS43760 is stripped from the AS path sent to clients
remarks:        - MEDs and next-hop are not modified
remarks:        - communities are stripped from all announcements to
remarks:          clients
remarks:        - we filter inbound routing prefixes based on IRR
remarks:          information pulled from whois.ripe.net.  Please check
remarks:          your public routing policy before complaining that
remarks:          we're ignoring your prefixes.  This particularly
remarks:          applies to IPv6 prefixes.
remarks:        - standard community 43760:43760 is really just a NOP
remarks:        - large community 43760:1:0 is not a NOP
remarks:
remarks:        -------------------------------------------------------
org:            ORG-INEA1-RIPE
admin-c:        INO7-RIPE
tech-c:         INO7-RIPE
notify:         ripe-notify@inex.ie
mnt-by:         RIPE-NCC-END-MNT
mnt-by:         INEX-NOC
<?php foreach( $t->rsclients[ "clients" ] as $asn => $cdetails ): ?>
<?php $cust = $t->customers[ $cdetails[ "id" ] ] ?>
<?php foreach( $cdetails[ "vlans" ] as $vlanid => $vli ): ?>
<?php foreach( $vli as $vliid => $interface ): ?>
<?php foreach( $t->protocols as $proto ): ?>
<?php if( !isset( $interface[ $proto ] ) ): ?>
<?php continue; ?>
<?php endif; ?>
<?php foreach( $t->rsclients[ "vlans" ][ $vlanid ][ "servers" ][ $proto ] as $serverip ): ?>
<?php if( $proto == 4 ): ?>
import:         from AS<?= $cust->autsys ?> <?= $interface[ $proto ] ?> at <?= $serverip ?>

                accept <?= $cust->asMacro( $proto, 'AS' ) ?>  # <?= $cust->name ?>

export:         to AS<?= $cust->autsys ?> <?= $interface[ $proto ] ?> at <?= $serverip ?>

                announce AS-SET-INEX-RS
<?php else: ?>
mp-import:      afi ipv6.unicast
                from AS<?= $cust->autsys?> <?= $interface[ $proto ] ?> at <?= $serverip ?>

                accept <?= $cust->asMacro( $proto, 'AS' ) ?>  # <?= $cust->name ?>

mp-export:      afi ipv6.unicast
                to AS<?= $cust->autsys ?> <?= $interface[ $proto ] ?> at <?= $serverip ?>

                announce AS-SET-INEX-RS
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
status:         ASSIGNED
source:         RIPE
