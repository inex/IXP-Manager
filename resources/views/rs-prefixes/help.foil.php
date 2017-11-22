<br/>
<p>
    This <em>Route Server Prefix Analysis</em> tool allows you to examine what routes you
    are advertising to the <?= config( 'identity.orgname' ) ?> Route Servers.
    <br /><br />
</p>

<h4>Source of IRRDB Information</h4>

<p>
    <?= config( 'identity.name' ) ?> has set the source of IRRDB information for your organisation to:
    <?= $t->c->getIRRDB()->getSource() ?> <em>(<?= $t->c->getIRRDB()->getNotes() ?>)</em>.
</p>

<p>
    <?php if( $t->c->getPeeringmacro() ): ?>
        We are using your IPv4 AS-SET <code><?= $t->c->getPeeringmacro() ?></code>
        (and IPv6 AS-SET <code><?= $t->c->resolveAsMacro(6) ?></code>) when querying the IRRDB database(s).
    <?php else: ?>
        We have no AS-SET on record for you and as such, we are just querying the IRRDB database(s) using
        the single ASN <?= $t->c->getAutsys() ?>.
    <?php endif; ?>
</p>

<p>
    If this is incorrect or if there is a better source, please contact
    <a href="mailto:<?= config( 'identity.email' ) ?>"><?= config( 'identity.name' ) ?></a>
    to have this changed.
    <br /><br />
</p>


<h4>
    Advertised but Not Accepted
    <span class="badge badge-<?php if( $t->aggRoutes[ 'adv_nacc' ] > 0 ): ?>danger <?php else: ?>success<?php endif; ?>"><?= count( $t->aggRoutes[ 'adv_nacc' ] ) ?></span>
</h4>

<p>
    These are routes that you are advertising to the route servers <strong>but that the route servers are
        NOT accepting</strong> because you do not have an exact <code>route:</code> / <code>route6:</code>
    object in your IRRDB entries. NB: <?= config( 'identity.orgname' ) ?> filters these objects on prefix size so
    if you are deaggregating your annoucements, you must also create route objects for these deaggregated
    prefixes.
</p>

<p>
    A typical <code>route:</code> / <code>route6:</code> object for your organisation would be:
</p>

<div class="row">

    <div class="col-md-4 col-md-offset-1">

        <pre>
            route:          192.0.2.0/24
            descr:          <?= $t->c->getName() ?>

            origin:         AS<?= $t->c->getAutsys() ?>

            mnt-by:         YOURORG-MNT
        </pre>

    </div>

    <div class="col-md-4 col-md-offset-1">

        <pre>
            route6:         2001:DB8::/32
            descr:          <?= $t->c->getName() ?>

            origin:         AS<?= $t->c->getAutsys() ?>

            mnt-by:         YOURORG-MNT
        </pre>

    </div>

</div>

<br /><br />

<h4>
    Not Advertised but Acceptable
    <span class="badge badge-<?php if( count( $t->aggRoutes[ 'nadv_acc' ] ) > 0 ): ?>warning<?php else: ?>success<?php endif; ?>"><?= count( $t->aggRoutes[ 'nadv_acc' ] ) ?></span>
</h4>

<p>
    These are prefixes for which you have valid route objects but that you do not advertise to the route servers.
    <strong>Advertising these routes could increase your traffic over <?= config( 'identity.orgname' ) ?> and thus allow
        you to get greater value for money from your membership.</strong>
    <br /><br />
</p>

<h4>
    Advertised and Accepted
    <span class="badge badge-<?php if( count( $t->aggRoutes[ 'adv_acc' ] ) > 0 ): ?>success<?php else: ?>danger<?php endif; ?>"><?= count( $t->aggRoutes[ 'adv_acc' ] ) ?></span>
</h4>

<p>
    These are routes that you are advertising to the route servers and that the route servers are accepting as valid.
    As such, these routes are distributed to the other route server users.
    <br /><br />
</p>
