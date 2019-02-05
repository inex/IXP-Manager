<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>

    Statistics / Graphs




    <?php if( $t->graph ): ?>

        <?php if( !(Auth::check() && Auth::user()->isSuperUser() ) ): ?>
            <small>
        <?php endif; ?>

        (
        <?= $t->infra ? 'MRTG: '  . $t->infra->getName() : '' ?>
        <?= $t->vlan  ? 'SFlow: ' . $t->vlan->getName()  : '' ?>
        /
        <?= $t->graph->resolveCategory( $t->graph->category() ) ?>
        /
        <?= $t->graph->resolvePeriod( $t->graph->period() ) ?>
        <?php if( $t->graph->protocol() != IXP\Services\Grapher\Graph::PROTOCOL_ALL ): ?>
            /
            <?= $t->graph->resolveProtocol( $t->graph->protocol() ) ?>
        <?php endif; ?>
        )

        <?php if( !(Auth::check() && Auth::user()->isSuperUser() ) ): ?>
            </small>
        <?php endif; ?>

    <?php endif; ?>


<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <div class="row">

        <div class="col-sm-12">

            <?php if( in_array( 'mrtg', config('grapher.backend' ) ) ): ?>

                <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">

                    <a class="navbar-brand" href="<?= route('statistics/members') ?>">
                        MRTG:
                    </a>

                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav">
                            <form class="navbar-form navbar-left form-inline" method="post" action="<?= route('statistics/members' ) ?>">
                                <li class="nav-item mr-2">
                                    <div class="nav-link d-flex ">
                                        <label for="selectInfra" class="mr-2">Infrastructure:</label>
                                        <select id="selectInfra" class="form-control" name="infra">
                                            <option>All</option>
                                            <?php foreach( $t->infras as $id => $i ): ?>
                                                <option value="<?= $id ?>" <?= $t->infra && $t->infra->getId() == $id ? 'selected="selected"' : '' ?>><?= $i ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <li class="nav-item mr-2">
                                    <div class="nav-link d-flex ">
                                        <label for="selectCategory" class="mr-2">Category:</label>
                                        <select id="selectCategory" class="form-control" name="category">
                                            <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $c => $d ): ?>
                                                <option value="<?= $c ?>" <?= $t->r->category == $c ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <li class="nav-item mr-2">
                                    <div class="nav-link d-flex ">
                                        <label for="selectPeriod" class="mr-2">Period:</label>
                                        <select id="selectPeriod" class="form-control" name="period">
                                            <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $p => $d ): ?>
                                                <option value="<?= $p ?>" <?= $t->r->period == $p ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">

                                <li class="nav-item">
                                    <input class="btn btn-outline-secondary" type="submit" name="submit" value="Show Graphs" />
                                </li>

                            </form>

                        </ul>
                    </div>
                </nav>

            <?php endif; ?>

            <?php if( in_array( 'sflow', config('grapher.backend' ) ) ): ?>

                <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                    <a class="navbar-brand" href="<?= route('statistics/members') ?>">
                        SFlow:
                    </a>
                    <div class="navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav">

                            <form class="navbar-form navbar-left form-inline"  action="<?= route('statistics/members' ) ?>" method="post">
                                <li class="nav-item mr-2">
                                    <div class="nav-link d-flex ">
                                        <label for="selectVlan" class="mr-2">VLAN:</label>
                                        <select id="selectVlan" class="form-control" name="vlan">
                                            <option>All</option>
                                            <?php foreach( $t->vlans as $id => $i ): ?>
                                                <option value="<?= $id ?>" <?= $t->vlan && $t->vlan->getId() == $id ? 'selected="selected"' : '' ?>><?= $i ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <li class="nav-item mr-2">
                                    <div class="nav-link d-flex ">
                                        <label for="selectVlan" class="mr-2">Protocol:</label>
                                        <select id="selectVlan" class="form-control" name="protocol">
                                            <option>All</option>
                                            <?php foreach( \IXP\Services\Grapher\Graph::PROTOCOL_REAL_DESCS as $p => $n ): ?>
                                                <option value="<?= $p ?>" <?= $t->r->protocol == $p ? 'selected="selected"' : '' ?>><?= $n ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <li class="nav-item mr-2">
                                    <div class="nav-link d-flex ">
                                        <label for="selectCategory2" class="mr-2">Category:</label>
                                        <select id="selectCategory2" class="form-control" name="category">
                                            <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $c => $d ): ?>
                                                <option value="<?= $c ?>" <?= $t->r->category == $c ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <li class="nav-item mr-2">
                                    <div class="nav-link d-flex ">
                                        <label for="selectPeriod2" class="mr-2">Period:</label>
                                        <select id="selectPeriod2" class="form-control" name="period">
                                            <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $p => $d ): ?>
                                                <option value="<?= $p ?>" <?= $t->r->period == $p ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>

                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input class="btn btn-outline-secondary" type="submit" name="submit" value="Show Graphs" />

                            </form>
                        </ul>

                    </div>
                </nav>

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
                <hr>
                <div class="row">
                    <?php foreach( $t->graphs as $graph ): ?>

                        <div id="graph-row" class="col-xs-12 col-sm-12 col-md-6 col-lg-4 mb-4">

                            <div class="card">
                                <div class="card-header d-flex">
                                    <div class="mr-auto">
                                        <b class="align-middle">
                                            <?= $graph->customer()->getFormattedName() ?>
                                        </b>

                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">

                                        <?php if( config('grapher.backends.sflow.enabled') && isset( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS[$graph->category()] ) && $t->grapher()->canAccessAllCustomerP2pGraphs() ): ?>
                                            <a class="btn btn-outline-secondary" href="<?= route('statistics@p2p', [ 'cid' => $graph->customer()->getId() ] ) . "?category={$graph->category()}&period={$graph->period()}" ?>">
                                                <span class="fa fa-random"></span>
                                            </a>
                                        <?php endif; ?>

                                        <?php if( $t->grapher()->canAccessAllCustomerGraphs() ): ?>
                                            <a class="btn btn-outline-secondary" href="<?= route( 'statistics@member', [ $graph->customer()->getId() ] ) ?>">
                                                <span class="fa fa-search-plus"></span>
                                            </a>
                                        <?php endif; ?>

                                    </div>

                                </div>
                                <div class="card-bosy">

                                    <p>
                                        <br />
                                        <?php $graph->authorise() ?>
                                        <?= $graph->renderer()->boxLegacy() ?>
                                    </p>
                                </div>
                            </div>

                        </div>

                    <?php endforeach; ?>
                </div>


            <?php endif; ?>


        </div>

    </div>


<?php $this->append() ?>