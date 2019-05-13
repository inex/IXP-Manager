<p>
    This <em>Route Server Prefix Filtering Analysis</em> tool allows one to examine what routes a
    network is advertising to the <?= config( 'identity.orgname' ) ?> Route Servers.
</p>

<h4>Source of IRRDB Information</h4>

<p>
    <?= config( 'identity.name' ) ?> has set the source of IRRDB information for this network to:
    <?= $t->c->getIRRDB()->getSource() ?> <em>(<?= $t->c->getIRRDB()->getNotes() ?>)</em>.
</p>

<p>
    <?php if( $t->c->getPeeringmacro() ): ?>
        We are using the IPv4 AS-SET <code><?= $t->c->getPeeringmacro() ?></code>
        (and IPv6 AS-SET <code><?= $t->c->resolveAsMacro(6) ?></code>) when querying the IRRDB database(s).
    <?php else: ?>
        We have no AS-SET on record for his network and as such, we are just querying the IRRDB database(s) using
        the single ASN <?= $t->c->getAutsys() ?>.
    <?php endif; ?>
</p>

<p>
    If this is incorrect or if there is a better source, please
    <a href="<?= route( 'public-content', 'support' ) ?>">contact us</a>
    to have this changed.
</p>


<h4>
    Advertised but Not Accepted
    <span class="badge badge-<?php if( $t->aggRoutes[ 'adv_nacc' ] > 0 ): ?>danger <?php else: ?>success<?php endif; ?>"><?= count( $t->aggRoutes[ 'adv_nacc' ] ) ?></span>
</h4>

<p>
    These are routes that are being advertised to the route servers <b>but that the route servers are
    NOT accepting</b> because there is no exact <code>route:</code> / <code>route6:</code>
    object in the network's IRRDB entries. NB: <?= config( 'identity.orgname' ) ?> filters these objects on prefix size so
    if this network is deaggregating their annoucements, they must also create route objects for these deaggregated
    prefixes.
</p>

<p>
    A typical <code>route:</code> / <code>route6:</code> object for this organisation would be:
</p>


<div>

    <pre>
        route:          192.0.2.0/24
        descr:          <?= $t->c->getName() ?>

        origin:         AS<?= $t->c->getAutsys() ?>

        mnt-by:         YOURORG-MNT
    </pre>

</div>

<div>

    <pre>
        route6:         2001:DB8::/32
        descr:          <?= $t->c->getName() ?>

        origin:         AS<?= $t->c->getAutsys() ?>

        mnt-by:         YOURORG-MNT
    </pre>

</div>


<h4 class="mt-2">
    Not Advertised but Acceptable
    <span class="badge badge-<?php if( count( $t->aggRoutes[ 'nadv_acc' ] ) > 0 ): ?>warning<?php else: ?>success<?php endif; ?>"><?= count( $t->aggRoutes[ 'nadv_acc' ] ) ?></span>
</h4>

<p>
    These are prefixes for which this network has valid route objects but does not advertise the them to the route servers.
    This may be intentional or accidental.
</p>

<h4 class="mt-2">
    Advertised and Accepted
    <span class="badge badge-<?php if( count( $t->aggRoutes[ 'adv_acc' ] ) > 0 ): ?>success<?php else: ?>danger<?php endif; ?>"><?= count( $t->aggRoutes[ 'adv_acc' ] ) ?></span>
</h4>

<p>
    These are routes that are being advertised to the route servers and that the route servers are accepting as valid.
    As such, these routes are distributed to the other route server users.
</p>
