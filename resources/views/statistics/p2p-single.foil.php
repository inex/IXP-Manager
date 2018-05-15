<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );

    // helpers:
    /** @var Entities\VlanInterface $srcVli */
    $srcVli = $t->srcVli;
    /** @var Entities\VlanInterface $dstVli */
    $dstVli = $t->dstVli;
?>

<?php $this->section( 'title' ) ?>

<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>

    <a href="<?= route( 'customer@list' )?>">Customers</a>

    <li>
        <a href="<?= route( 'customer@overview', [ 'id' => $t->c->getId() ] ) ?>" >
            <?= $t->c->getFormattedName() ?>
        </a>
    </li>

    <li>
        <a href="<?= route( 'statistics@member', [ 'id' => $t->c->getId() ] ) ?>" >
            Statistics
        </a>
    </li>

    <li>
        <a href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->getId() ] ) ?>" >
            Peer to Peer Graphs
        </a>
    </li>

    <li>
        <a href="<?= route( 'statistics@member', [ 'id' => $t->c->getId() ] ) ?>" >
            Traffic Exchanged with
            <a href="<?= route( 'statistics@p2p', [ 'cid' => $dstVli->getVirtualInterface()->getCustomer()->getId() ] )
                . '?svli='     . $dstVli->getId()
                . '&dvli='     . $srcVli->getId()
                . '&category=' . $t->category
                . '&period='   . $t->period
                . '&protocol=' . $t->protocol
            ?>">
                <?= $dstVli->getVirtualInterface()->getCustomer()->getFormattedName() ?>
        </a>
    </li>

<?php else: ?>

    Peer to Peer Graphs :: <?= $t->c->getFormattedName() ?>

    <div class="pull-right">
        <a class="btn btn-default" href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->getId() ] ) ?>">P2P Overview</a>
    </div>

<?php endif; ?>

<?php $this->append() ?>



<?php $this->section('content') ?>

<div class="row">

    <div class="col-md-12">

        <?= $t->alerts() ?>

        <h3>
            Traffic exchanged between <?= $srcVli->getVirtualInterface()->getCustomer()->getFormattedName() ?> &amp; <?= $dstVli->getVirtualInterface()->getCustomer()->getFormattedName() ?>
        </h3>

    </div>

</div>

<div class="row">

    <div class="col-md-12">

        <nav class="navbar navbar-default">

            <div class="navbar-header">
                <a class="navbar-brand">P2P Graphs</a>
            </div>

            <form class="navbar-form navbar-left form-inline" action="<?= route( 'statistics@p2p', [ 'cid' => $this->c->getId() ] ) ?>" method="post">

                <div class="form-group">
                    <label for="select_network">Interface:</label>
                    <select id="select_network" name="svli" class="form-control">
                        <?php foreach( $t->srcVlis as $id => $vli ): ?>
                            <option value="<?= $id ?>" <?php if( $t->srcVli->getId() == $vli->getId() ): ?> selected <?php endif; ?>  >
                                <?= $vli->getVlan()->getName() ?>
                                    :: <?= $vli->getIPAddress( $t->protocol ) ? $vli->getIPAddress( $t->protocol )->getAddress() : 'No IP - VLI ID: ' . $vli->getId() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="select_category">Category:</label>
                    <select id="select_category" name="category" class="form-control">
                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                            <option value="<?= $cvalue ?>" <?php if( $t->category == $cvalue ): ?> selected <?php endif; ?>  >
                                <?= $cname ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="select_protocol">Protocol:</label>
                    <select id="select_protocol" name="protocol" class="form-control">
                        <?php foreach( IXP\Services\Grapher\Graph::PROTOCOL_REAL_DESCS as $pvalue => $pname ): ?>
                            <?php if( $srcVli->isIPEnabled( $pvalue ) ): ?>
                                <option value="<?= $pvalue ?>" <?php if( $t->protocol == $pvalue ): ?> selected <?php endif; ?>  >
                                    <?= $pname ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="dvli" value="<?= $dstVli->getId() ?>">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <input class="btn btn-primary" type="submit" name="submit" value="Submit" />

            </form>

        </nav>

    </div>

</div>


<div class="row">

    <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $pid => $pname ): ?>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">

            <div class="well">

                <h3><?= $pname ?></h3>

                <p>
                    <br />
                    <?php
                        $t->graph->setDestinationVlanInterface( $dstVli, false )->setType('png')->setPeriod( $pid );
                        $t->graph->authorise();
                    ?>
                    <img class="img-responsive" src="<?= $t->graph->url() ?>">
                </p>

            </div>

        </div>

    <?php endforeach; ?>

</div>


<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>
<?= $t->insert( 'statistics/js/p2p' ); ?>
<?php $this->append() ?>
