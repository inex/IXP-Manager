<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Statistics / Graphs
    <?php if( $t->graph ): ?>

        <?php if( !(Auth::check() && Auth::getUser()->isSuperUser() ) ): ?>
            <small>
        <?php endif; ?>

        (
        <?= $t->infra ? 'MRTG: '  . $t->infra->name : '' ?>
        <?= $t->vlan  ? 'SFlow: ' . $t->vlan->name  : '' ?>
        /
        <?= $t->graph->resolveCategory( $t->graph->category() ) ?>
        /
        <?= $t->graph->resolvePeriod( $t->graph->period() ) ?>
        <?php if( $t->graph->protocol() !== IXP\Services\Grapher\Graph::PROTOCOL_ALL ): ?>
            /
            <?= $t->graph->resolveProtocol( $t->graph->protocol() ) ?>
        <?php endif; ?>
        )

        <?php if( !(Auth::check() && Auth::getUser()->isSuperUser() ) ): ?>
            </small>
        <?php endif; ?>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?php if( in_array( 'mrtg', config( 'grapher.backend' ), true ) ): ?>
                <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                    <a class="navbar-brand" href="<?= route('statistics@members') ?>">
                        MRTG:
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav">
                            <form class="navbar-form navbar-left form-inline d-block d-lg-flex" method="post" action="<?= route('statistics@members' ) ?>">
                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="selectInfra" class="col-sm-4 col-lg-6">Infrastructure:</label>
                                        <select id="selectInfra" class="form-control" name="infra">
                                            <option>All</option>
                                            <?php foreach( $t->infras as  $i ): ?>
                                                <option value="<?= $i[ 'id' ] ?>" <?= $t->infra && $t->infra->id === $i[ 'id' ] ? 'selected="selected"' : '' ?>><?= $i[ 'name' ] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="selectCategory" class="col-sm-4 col-lg-6">Category:</label>
                                        <select id="selectCategory" class="form-control" name="category">
                                            <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $c => $d ): ?>
                                                <option value="<?= $c ?>" <?= $t->r->category === $c ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="selectPeriod" class="col-sm-4 col-lg-6">Period:</label>
                                        <select id="selectPeriod" class="form-control" name="period">
                                            <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $p => $d ): ?>
                                                <option value="<?= $p ?>" <?= $t->r->period === $p ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <li class="nav-item ml-3">
                                    <input class="btn btn-white float-right" type="submit" name="submit" value="Show Graphs" />
                                </li>
                            </form>
                        </ul>
                    </div>
                </nav>
            <?php endif; ?>

            <?php if( in_array( 'sflow', config( 'grapher.backend' ), true ) ): ?>
                <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                    <a class="navbar-brand" href="<?= route('statistics@members') ?>">
                        SFlow:
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown2" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavDropdown2">
                        <ul class="navbar-nav">
                            <form class="navbar-form navbar-left form-inline d-block d-lg-flex"  action="<?= route('statistics@members' ) ?>" method="post">
                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="selectVlan" class="col-sm-4 col-lg-3">VLAN:</label>
                                        <select id="selectVlan" class="form-control" name="vlan">
                                            <option>All</option>
                                            <?php foreach( $t->vlans as $i ): ?>
                                                <option value="<?= $i[ 'id' ] ?>" <?= $t->vlan && $t->vlan->id === $i[ 'id' ] ? 'selected="selected"' : '' ?>><?= $i[ 'name' ] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="selectVlan" class="col-sm-4 col-lg-6">Protocol:</label>
                                        <select id="selectVlan" class="form-control" name="protocol">
                                            <option>All</option>
                                            <?php foreach( \IXP\Services\Grapher\Graph::PROTOCOL_REAL_DESCS as $p => $n ): ?>
                                                <option value="<?= $p ?>" <?= $t->r->protocol === $p ? 'selected="selected"' : '' ?>><?= $n ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="selectCategory2" class="col-sm-4 col-lg-6">Category:</label>
                                        <select id="selectCategory2" class="form-control" name="category">
                                            <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $c => $d ): ?>
                                                <option value="<?= $c ?>" <?= $t->r->category === $c ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="selectPeriod2" class="col-sm-4 col-lg-6">Period:</label>
                                        <select id="selectPeriod2" class="form-control" name="period">
                                            <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $p => $d ): ?>
                                                <option value="<?= $p ?>" <?= $t->r->period === $p ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <li class="nav-item ml-3">
                                  <input class="btn btn-white float-right" type="submit" name="submit" value="Show Graphs" />
                                </li>
                            </form>
                        </ul>
                    </div>
                </nav>
            <?php endif; ?>

            <?php if( !sizeof( array_intersect( ['mrtg', 'sflow'], config( 'grapher.backend' ) ) ) ): ?>

                <div id="infra_reg_banner" class="tw-bg-blue-100 tw-border-l-4 tw-border-blue-500 tw-text-blue-700 p-4 alert-dismissible mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center"><i class="fa fa-info-circle fa-2x "></i></div>
                        <div class="col-sm-12">
                            You must have either or both the MRTG and/or the sflow grapher backend configured for this functionality.
                        </div>
                    </div>
                </div>

            <?php endif; ?>


    <?php if( !$t->graph ): ?>
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            <?php if( !$t->infra && !$t->vlan  ): ?>
                                Select parameters above and click <em>Show Graphs</em>.
                            <?php else: ?>
                                No graphs found for the requested parameters.
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach( $t->graphs as $graph ): ?>
                        <div id="graph-row" class="col-sm-12 col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header d-flex">
                                    <div class="mr-auto">
                                        <b class="align-middle">
                                            <?= $graph->customer()->getFormattedName() ?>
                                        </b>
                                    </div>
                                    <div class="btn-group btn-group-sm my-auto" role="group">
                                        <?php if( config('grapher.backends.sflow.enabled') && isset( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS[$graph->category()] ) && $t->grapher()->canAccessAllCustomerP2pGraphs() ): ?>
                                            <a class="btn btn-white" href="<?= route('statistics@p2p', [ 'cust' => $graph->customer()->id ] ) . "?category={$graph->category()}&period={$graph->period()}" ?>">
                                                <span class="fa fa-random"></span>
                                            </a>
                                        <?php endif; ?>
                                        <?php if( $t->grapher()->canAccessAllCustomerGraphs() ): ?>
                                            <a class="btn btn-white" href="<?= route( 'statistics@member', [ $graph->customer()->id ] ) ?>">
                                                <span class="fa fa-search-plus"></span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php $graph->authorise() ?>
                                    <?= $graph->renderer()->boxLegacy() ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>