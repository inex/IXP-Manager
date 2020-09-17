<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <a href="<?= route( 'customer@overview', [ 'id' => $t->c->id ] )?>" >
            <?= $t->c->getFormattedName() ?>
        </a>
    <?php else: ?>
        IXP Port Graphs :: <?= $t->c->getFormattedName() ?>
    <?php endif; ?>

    / Statistics
    (
        <?= IXP\Services\Grapher\Graph::resolveCategory( $t->category ) ?>
        /
        <?= IXP\Services\Grapher\Graph::resolvePeriod( $t->period ) ?>
    )
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand">
                    Graph Options:
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex"  action="<?= route( "statistics@member", [ "id" => $t->c->id ] ) ?>" method="GET">
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="category" class="col-sm-4 col-lg-4">Type:</label>
                                    <select id="category" name="category" onchange="" class="form-control">
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $cvalue => $cname ): ?>
                                            <option value="<?= $cvalue ?>" <?php if( $t->category === $cvalue ): ?> selected <?php endif; ?> ><?= $cname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="period" class="col-sm-4 col-lg-6">Period:</label>
                                    <select id="period" name="period" onchange="" class="form-control" placeholder="Select State">
                                        <option></option>
                                        <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $pvalue => $pname ): ?>
                                            <option value="<?= $pvalue ?>" <?php if( $t->period === $pvalue ): ?> selected <?php endif; ?>  ><?= $pname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item float-right ml-3">
                                <input type="submit" class="btn btn-white" value="Show Graphs">
                                <?php if( config('grapher.backends.sflow.enabled') && $t->grapher()->canAccessAllCustomerP2pGraphs() ): ?>
                                    <a class="btn btn-white ml-2" href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->id ] ) ?>">
                                        <i class="fa fa-random"></i>&nbsp;&nbsp;P2P Graphs
                                    </a>&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php endif; ?>
                            </li>
                        </form>
                    </ul>
                </div>
            </nav>

            <div class="row">
                <div class="col-sm-12 col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex">
                            <h3 class="mr-auto">
                                Aggregate Peering Traffic
                                <?php if( $t->resellerMode() && $t->c->isReseller ): ?>
                                    <small><em>(Peering ports only)</em></small>
                                <?php endif; ?>
                            </h3>
                            <div class="btn-group btn-group-sm my-auto">
                                <?php if( config( 'grapher.backends.sflow.enabled' ) ): ?>
                                    <a class="btn btn-sm btn-white" href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->id ] ) ?>">
                                        <span class="fa fa-random"></span>
                                    </a>
                                <?php endif; ?>
                                <a class="btn btn-white" href="<?= route( "statistics@member-drilldown" , [ "typeid" => $t->c->id, "type" => "agg" ] ) ?>/?category=<?= $t->category ?>">
                                    <i class="fa fa-search-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?= $t->grapher->customer( $t->c )->setCategory( $t->category )->setPeriod( $t->period )->renderer()->boxLegacy() ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php foreach( $t->c->virtualinterfaces as $vi ): ?>
                <div class="card col-sm-12 mt-4 bg-light">
                    <div class="card-body row" >
                        <?php if( !$vi->physicalInterfaces()->exists() ) {
                                continue;
                            }

                            $pi = $vi->physicalInterfaces()->first();
                            $isLAG =  $vi->physicalInterfaces()->count() > 1;
                        ?>

                        <?php if( $isLAG ): ?>
                            <div class="col-sm-12 col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header d-flex">
                                        <h5 class="mr-auto">
                                            LAG on <?= $pi->switchPort->switcher->cabinet->location->name ?>
                                            / <?= $pi->switchPort->switcher->name ?>
                                        </h5>
                                        <?php if( $vi->isGraphable() ): ?>
                                            <div class="btn-group btn-group-sm my-auto">
                                                <?= $t->insert( 'statistics/snippets/latency-dropup', [ 'vi' => $vi ] ) ?>

                                                <?php if( config( 'grapher.backends.sflow.enabled' ) ): ?>
                                                    <a class="btn btn-white" href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->id ] )
                                                        . ( $vi->vlanInterfaces()->exists() ? '?svli=' . $vi->vlanInterfaces()->first()->id : '' )
                                                    ?>">
                                                        <span class="fa fa-random"></span>
                                                    </a>
                                                <?php endif; ?>

                                                <a class="btn btn-white" href="<?= route( "statistics@member-drilldown" , [ "type" => "vi", "typeid" => $vi->id  ] ) ?>/?category=<?= $t->category ?>" title="Drilldown">
                                                    <i class="fa fa-search-plus"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <?php if( $vi->isGraphable() ): ?>
                                            <?= $t->grapher->virtint( $vi )->setCategory( $t->category )->setPeriod( $t->period )->renderer()->boxLegacy() ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php foreach( $vi->physicalInterfaces as $idx => $pi ): ?>
                            <div class="col-sm-12 col-lg-6 mb-4 <?php if( $isLAG && $idx > 0 ): ?>offset-lg-6 <?php endif; ?>">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex">
                                            <div class="mr-auto">
                                                <h5>
                                                    <?php if( $isLAG ): ?>
                                                        <?= $pi->switchPort->switcher->name ?> ::
                                                        <?= $pi->switchPort->name ?> (<?=$pi->speed() ?>)
                                                    <?php else: ?>
                                                        <?= $pi->switchPort->switcher->cabinet->location->name ?>
                                                        / <?= $pi->switchPort->switcher->name ?> (<?=$pi->speed() ?>)
                                                    <?php endif; ?>
                                                </h5>
                                            </div>
                                            <?php if( $pi->statusIsConnectedOrQuarantine() ): ?>
                                                <div class="btn-group btn-group-sm my-auto">
                                                    <?php if( !$isLAG ): ?>
                                                        <?= $t->insert( 'statistics/snippets/latency-dropup', [ 'vi' => $vi ] ) ?>
                                                    <?php endif; ?>
                                                    <?php if( config( 'grapher.backends.sflow.enabled' ) ): ?>
                                                        <a class="btn btn-white btn-sm" href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->id ] )
                                                        . ( $vi->vlanInterfaces()->exists() ? '?svli=' . $vi->vlanInterfaces()->first()->id : '' )
                                                        ?>">
                                                            <span class="fa fa-random"></span>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a class="btn btn-white btn-sm" href="<?= route( "statistics@member-drilldown" , [ "type" => "pi", "typeid" => $pi->id  ] ) ?>/?category=<?= $t->category ?>">
                                                        <i class="fa fa-search-plus"></i>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <small>
                                            <?php if( !$isLAG ): ?>
                                                <?= $pi->switchPort->name ?>
                                            <?php endif; ?>

                                            <?php if( $t->resellerMode() && $t->c->isReseller ): ?>
                                                <br />
                                                <?php if( $pi->switchPort->isTypePeering() ): ?>
                                                Peering Port
                                                <?php elseif( $pi->switchPort->isTypeFanout() ): ?>
                                                    Fanout Port for <a href="<?= route( 'customer@overview', [ 'id' => $pi->getRelatedInterface()->virtualInterface->customer->id ] ) ?>">
                                                    <?= $pi->getRelatedInterface()->virtualInterface->customer->abbreviatedName ?>
                                                </a>
                                                <?php elseif( $pi->switchPort->isReseller() ): ?>
                                                    Reseller Uplink Port
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <?php if( $pi->statusIsConnectedOrQuarantine() ): ?>
                                            <?= $t->grapher->physint( $pi )->setCategory( $t->category )->setPeriod( $t->period )->renderer()->boxLegacy() ?>
                                        <?php else: ?>
                                            <?= $t->insert( 'customer/overview-tabs/ports/pi-status', [ 'pi' => $pi ] ) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; /* $vi->getPhysicalInterfaces() */ ?>
                    </div>
                </div>
            <?php endforeach; /* $t->c->getVirtualInterfaces() */ ?>
        </div>
    </div>
<?php $this->append() ?>