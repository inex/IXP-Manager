<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );

    // helpers:
    /** @var Entities\VlanInterface $srcVli */
    $srcVli = $t->srcVli;
    /** @var Entities\VlanInterface $dstVli */
    $dstVli = $t->dstVli;
?>

<?php $this->section( 'page-header-preamble' ) ?>

    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>




            <a href="<?= route( 'customer@overview', [ 'id' => $t->c->getId() ] ) ?>" >
                <?= $t->c->getFormattedName() ?>
            </a>

            /

            <a href="<?= route( 'statistics@member', [ 'id' => $t->c->getId() ] ) ?>" >
                Statistics
            </a>

            /

            <a href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->getId() ] ) ?>" >
                Peer to Peer Graphs
            </a>

            /

            <a href="<?= route( 'statistics@member', [ 'id' => $t->c->getId() ] ) ?>" >
                Traffic Exchanged with
            </a>
            <a href="<?= route( 'statistics@p2p', [ 'cid' => $dstVli->getVirtualInterface()->getCustomer()->getId() ] )
                . '?svli='     . $dstVli->getId()
                . '&dvli='     . $srcVli->getId()
                . '&category=' . $t->category
                . '&period='   . $t->period
                . '&protocol=' . $t->protocol
            ?>">
                <?= $dstVli->getVirtualInterface()->getCustomer()->getFormattedName() ?>
            </a>


    <?php else: ?>

        Peer to Peer Graphs :: <?= $t->c->getFormattedName() ?>



    <?php endif; ?>

<?php $this->append() ?>

<?php if( Auth::check() && !Auth::user()->isSuperUser() ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>

            <a class="btn btn-white btn-sm" href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->getId() ] ) ?>">P2P Overview</a>

    <?php $this->append() ?>
<?php endif; ?>

<?php $this->section('content') ?>

<div class="row">

    <div class="col-md-12">

        <?= $t->alerts() ?>

        <h3>
            Traffic exchanged between <?= $srcVli->getVirtualInterface()->getCustomer()->getAbbreviatedName() ?> (<?= $srcVli->getIPAddress( $t->protocol ) ? $srcVli->getIPAddress( $t->protocol )->getAddress() : 'No IP' ?>)
            &amp; <?= $dstVli->getVirtualInterface()->getCustomer()->getAbbreviatedName() ?> (<?= $dstVli->getIPAddress( $t->protocol ) ? $dstVli->getIPAddress( $t->protocol )->getAddress() : 'No IP' ?>)
        </h3>

    </div>

</div>

<div class="row">

    <div class="col-md-12">

            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm mt-4">

                <a class="navbar-brand">P2P Graphs</a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">

                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex" action="<?= route( 'statistics@p2p', [ 'cid' => $this->c->getId() ] ) ?>" method="post">

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="select_network" class="col-sm-4 col-lg-3">Interface:</label>
                                    <select id="select_network" name="svli" class="form-control">
                                        <?php foreach( $t->srcVlis as $id => $vli ): ?>
                                            <option value="<?= $id ?>" <?php if( $t->srcVli->getId() == $vli->getId() ): ?> selected <?php endif; ?>  >
                                                <?= $vli->getVlan()->getName() ?>
                                                    :: <?= $vli->getIPAddress( $t->protocol ) ? $vli->getIPAddress( $t->protocol )->getAddress() : 'No IP - VLI ID: ' . $vli->getId() ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="select_category" class="col-sm-4 col-lg-6">Category:</label>
                                    <select id="select_category" name="category" class="form-control">
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                                            <option value="<?= $cvalue ?>" <?php if( $t->category == $cvalue ): ?> selected <?php endif; ?>  >
                                                <?= $cname ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="select_protocol" class="col-sm-4 col-lg-6">Protocol:</label>
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
                            </li>

                            <input type="hidden" name="dvli" value="<?= $dstVli->getId() ?>">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <input class="btn btn-white float-right" type="submit" name="submit" value="Submit" />

                        </form>
                    </ul>
                </div>

            </nav>

    </div>

</div>


<div class="row">

    <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $pid => $pname ): ?>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 mt-4">

            <div class="card">
                <div class="card-header">

                    <h3><?= $pname ?></h3>
                </div>
                <div class="card-body">
                    <p>
                        <?php
                            $t->graph->setDestinationVlanInterface( $dstVli, false )->setType('png')->setPeriod( $pid );
                            $t->graph->authorise();
                        ?>
                        <img class="img-responsive" src="<?= $t->graph->url() ?>">
                    </p>

                </div>

            </div>
        </div>

    <?php endforeach; ?>

</div>


<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'statistics/js/p2p' ); ?>
<?php $this->append() ?>
