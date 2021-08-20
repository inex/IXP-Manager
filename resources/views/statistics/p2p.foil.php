<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $isSuperUser = Auth::check() ? Auth::getUser()->isSuperUser(): false;
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::check() && $isSuperUser ): ?>
        <a href="<?= route( 'customer@overview', [ 'cust' => $t->c->id ] ) ?>" >
            <?= $t->c->getFormattedName() ?>
        </a>
        /
        <a href="<?= route( 'statistics@member', [ 'cust' => $t->c->id ] ) ?>" >
            Statistics
        </a>
        /
        <a href="<?= route( 'statistics@member', [ 'cust' => $t->c->id ] ) ?>" >
            Peer to Peer Graphs
        </a>
        (<?= $t->srcVli->getIPAddress( $t->protocol )->address ?? 'No IP' ?>
            / <?= IXP\Services\Grapher\Graph::resolveCategory( $t->category ) ?>
            / <?= IXP\Services\Grapher\Graph::resolvePeriod( $t->period ) ?>
            / <?= IXP\Services\Grapher\Graph::resolveProtocol( $t->protocol ) ?>
        )

    <?php else: ?>
        Peer to Peer Graphs :: <?= $t->c->getFormattedName() ?>
    <?php endif; ?>
<?php $this->append() ?>

<?php if( Auth::check() && !$isSuperUser ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>
        <?php if( $t->grapher()->canAccessAllCustomerGraphs() ): ?>
            <a class="btn btn-white" href="<?= route( 'statistics@member', [ 'cust' => $t->c->id ] ) ?>">
                All Ports
            </a>
        <?php endif; ?>
    <?php $this->append() ?>
<?php endif; ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>

            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <div class="navbar-header">
                    <a class="navbar-brand">P2P Graphs</a>
                </div>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex" action="<?= route( 'statistics@p2p', [ 'cust' => $this->c->id ] ) ?>" method="post">
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="select_network" class="col-sm-4 col-lg-3">Interface:</label>
                                    <select id="select_network" name="svli" class="form-control">
                                        <?php foreach( $t->srcVlis as $vli ):
                                            /** @var $vli \IXP\Models\VlanInterface */?>
                                            <option value="<?= $vli->id ?>" <?php if( $t->srcVli->id === $vli->id ): ?> selected <?php endif; ?>  >
                                                <?= $vli->vlan->name ?>
                                                :: <?= $vli->getIPAddress( $t->protocol )->address ?? 'No IP - VLI ID: ' . $vli->id ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <?php if( $t->showGraphs ): ?>
                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="select_category" class="col-sm-4 col-lg-6">Category:</label>
                                        <select id="select_category" name="category" class="form-control">
                                            <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                                                <option value="<?= $cvalue ?>" <?php if( $t->category === $cvalue ): ?> selected <?php endif; ?>  >
                                                    <?= $cname ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="select_period" class="col-sm-4 col-lg-6">Period:</label>
                                        <select id="select_period" name="period" class="form-control">
                                            <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $pvalue => $pname ): ?>
                                                <option value="<?= $pvalue ?>" <?php if( $t->period === $pvalue ): ?> selected <?php endif; ?>  >
                                                    <?= $pname ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="select_protocol" class="col-sm-4 col-lg-6">Protocol:</label>
                                    <select id="select_protocol" name="protocol" class="form-control">
                                        <?php foreach( IXP\Services\Grapher\Graph::PROTOCOL_REAL_DESCS as $pvalue => $pname ): ?>
                                            <?php if( $t->srcVli->vlan->private || $t->srcVli->ipvxEnabled( $pvalue ) ): ?>
                                                <option value="<?= $pvalue ?>" <?php if( $t->protocol === $pvalue ): ?> selected <?php endif; ?>  >
                                                    <?= $pname ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <div class="float-right">
                                <input class="btn btn-white  mr-2" type="submit" name="submit" value="Submit" />

                                <?php if( $t->showGraphsOption ): ?>
                                    <input class="btn btn-white " type="submit" name="submit" value="<?= $t->showGraphs ? 'Hide' : 'Show' ?> Graphs" />
                                <?php endif; ?>
                            </div>
                        </form>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <?php
        $dstVlis = $t->dstVlis;
        foreach( $dstVlis as $id => $dvli ) {
            if( !$t->srcVli->vlan->private && !$dvli->ipvxEnabled( $t->protocol ) ) {
                unset( $dstVlis[ $id ] );
            }
        }

        $cnt = 0;
        $total = count( $dstVlis );
        $firstColComplete = false;
    ?>


    <?php if( !$t->showGraphs ): ?>
        <div class="row">
            <div class="col-md-6">
                <ul>
                    <?php
                        foreach( $dstVlis as $dvli ):
                    ?>
                        <li>
                            <a href="<?= route( 'statistics@p2p', [ 'cust' => $t->c->id ] )
                                . '?svli='     . $t->srcVli->id
                                . '&dvli='     . $dvli->id
                                . '&category=' . $t->category
                                . '&period='   . $t->period
                                . '&protocol=' . $t->protocol
                            ?>">
                                <?= $dvli->virtualInterface->customer->getFormattedName() ?>
                            </a>
                        </li>

                        <?php $cnt++; ?>
                        <?php if( !$firstColComplete && $cnt > ( $total / 2 ) ): ?>
                            </ul>
                            </div>
                            <div class="col-md-6">
                            <ul>
                            <?php $firstColComplete = true; ?>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    <?php else: /* if( !$t->showGraphs ) */ ?>
        <div class="row">
            <?php foreach( $dstVlis as $dvli ): ?>
                <div class="col-md-12 col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>
                                <?= $dvli->virtualInterface->customer->getFormattedName() ?> :: <?= $dvli->getIPAddress( $t->protocol ) ? $dvli->getIPAddress( $t->protocol )->address : 'No IP' ?>
                            </h4>
                        </div>
                        <div class="card-body">
                            <a href="<?= route( 'statistics@p2p', [ 'cust' => $t->c->id ] )
                                . '?svli='     . $t->srcVli->id
                                . '&dvli='     . $dvli->id
                                . '&category=' . $t->category
                                . '&period='   . $t->period
                                . '&protocol=' . $t->protocol
                            ?>">
                                <img class="img-fluid" src="<?= $t->graph->setDestinationVlanInterface( $dvli, false )->setType('png')->url() ?>">
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'statistics/js/p2p' ); ?>
<?php $this->append() ?>