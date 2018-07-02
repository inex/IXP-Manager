<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>


    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>

            Statistics
        </li>

        <li>
            Graphs

    <?php else: ?>

        Member Graphs

    <?php endif; ?>


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




<?php $this->section( 'page-header-postamble' ) ?>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <div class="row">

        <div class="col-sm-12">

            <?php if( in_array( 'mrtg', config('grapher.backend' ) ) ): ?>

                <nav class="navbar navbar-default">
                    <div class="container-fluid">

                        <div class="col-md-12">

                            <div class="navbar-header">
                                <a class="navbar-brand" href="<?= route('statistics/members') ?>">MRTG:</a>
                            </div>

                            <form class="navbar-form navbar-left action="<?= route('statistics/members' ) ?>" method="post">

                            <div class="form-group">
                                <label for="selectInfra">Infrastructure:</label>
                                <select id="selectInfra" class="form-control" name="infra">
                                    <option>All</option>
                                    <?php foreach( $t->infras as $id => $i ): ?>
                                        <option value="<?= $id ?>" <?= $t->infra && $t->infra->getId() == $id ? 'selected="selected"' : '' ?>><?= $i ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="selectCategory">Category:</label>
                                <select id="selectCategory" class="form-control" name="category">
                                    <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $c => $d ): ?>
                                        <option value="<?= $c ?>" <?= $t->r->category == $c ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="selectPeriod">Period:</label>
                                <select id="selectPeriod" class="form-control" name="period">
                                    <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $p => $d ): ?>
                                        <option value="<?= $p ?>" <?= $t->r->period == $p ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <input class="btn btn-default" type="submit" name="submit" value="Show Graphs" />

                            </form>

                        </div>
                    </div>
                </nav>

            <?php endif; ?>

            <?php if( in_array( 'sflow', config('grapher.backend' ) ) ): ?>

                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="col-md-12">
                            <div class="navbar-header">
                                <a class="navbar-brand" href="<?= route('statistics/members') ?>">SFlow:</a>
                            </div>

                            <form class="navbar-form navbar-left form-inline"  action="<?= route('statistics/members' ) ?>" method="post">

                                <div class="form-group">
                                    <label for="selectVlan">VLAN:</label>
                                    <select id="selectVlan" class="form-control" name="vlan">
                                        <option></option>
                                        <?php foreach( $t->vlans as $id => $i ): ?>
                                            <option value="<?= $id ?>" <?= $t->vlan && $t->vlan->getId() == $id ? 'selected="selected"' : '' ?>><?= $i ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="selectVlan">Protocol:</label>
                                    <select id="selectVlan" class="form-control" name="protocol">
                                        <option></option>
                                        <?php foreach( \IXP\Services\Grapher\Graph::PROTOCOL_REAL_DESCS as $p => $n ): ?>
                                            <option value="<?= $p ?>" <?= $t->r->protocol == $p ? 'selected="selected"' : '' ?>><?= $n ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="selectCategory2">Category:</label>
                                    <select id="selectCategory2" class="form-control" name="category">
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $c => $d ): ?>
                                            <option value="<?= $c ?>" <?= $t->r->category == $c ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="selectPeriod2">Period:</label>
                                    <select id="selectPeriod2" class="form-control" name="period">
                                        <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $p => $d ): ?>
                                            <option value="<?= $p ?>" <?= $t->r->period == $p ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input class="btn btn-default" type="submit" name="submit" value="Show Graphs" />

                            </form>
                        </div>



                    </div>
                </nav>

            <?php endif; ?>

            <?php if( !$t->graph ): ?>

                <?php if( !$t->infra && !$t->vlan  ): ?>

                    <div class="alert alert-info" role="alert">
                        Select parameters above and click <em>Show Graphs</em>.
                    </div>

                <?php else: ?>

                    <div class="alert alert-info" role="alert">
                        No graphs found for the requested parameters.
                    </div>

                <?php endif; ?>

            <?php else: ?>

                <?php foreach( $t->graphs as $graph ): ?>

                    <div id="graph-row" class="col-xs-12 col-sm-12 col-md-6 col-lg-4">

                        <div class="well">
                            <h4 style="vertical-align: middle">
                                <?= $graph->customer()->getFormattedName() ?>
                                <span class="pull-right">

                                    <div class="btn-group" role="group">

                                        <?php if( config('grapher.backends.sflow.enabled') && isset( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS[$graph->category()] ) && $t->grapher()->canAccessAllCustomerP2pGraphs() ): ?>
                                            <a class="btn btn-default btn-xs" href="<?= route('statistics@p2p', [ 'cid' => $graph->customer()->getId() ] ) . "?category={$graph->category()}&period={$graph->period()}" ?>">
                                                <span class="glyphicon glyphicon-random"></span>
                                            </a>
                                        <?php endif; ?>

                                        <?php if( $t->grapher()->canAccessAllCustomerGraphs() ): ?>
                                            <a class="btn btn-default btn-xs" href="<?= route( 'statistics@member', [ $graph->customer()->getId() ] ) ?>">
                                                <span class="glyphicon glyphicon-zoom-in"></span>
                                            </a>
                                        <?php endif; ?>

                                    </div>

                                </span>
                            </h4>

                            <p>
                                <br />
                                <?php $graph->authorise() ?>
                                <?= $graph->renderer()->boxLegacy() ?>
                            </p>
                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>


        </div>

    </div>


<?php $this->append() ?>