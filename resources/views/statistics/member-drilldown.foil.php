<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $isSuperUser = Auth::check() ? Auth::getUser()->isSuperUser() : false;
    $c = $t->c; /** @var $c \IXP\Models\Customer */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::check() && $isSuperUser ): ?>
        <a href="<?= route( 'customer@overview', [ 'cust' => $c->id ] ) ?>" >
            <?= $t->c->getFormattedName() ?>
        </a>
        /
        <a href="<?= route( 'statistics@member', [ 'cust' => $c->id ] ) ?>" >
            Port Graphs
        </a>
        /
        Statistics Drilldown (<?= $t->graph->resolveMyCategory() ?>)
    <?php else: ?>
        IXP Port Graphs :: <?= $c->getFormattedName() ?>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand" href="#">Graph Options:</a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse mr-auto" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex">
                            <li class="nav-item mr-2">
                                <div class="nav-link d-flex ">
                                    <label for="category" class="mr-2">Type:</label>
                                    <select id="category" name="category" onchange="this.form.submit()" class="form-control">
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $cvalue => $cname ): ?>
                                            <option value="<?= $cvalue ?>" <?= $t->graph->category() === $cvalue ? 'selected="selected"' : '' ?>><?= $cname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                            <a class="btn btn-white float-right" href="<?= route( 'statistics@member', [ 'cust' => $t->c->id ] ) ?>?category=<?= $t->graph->category() ?>">
                                All Ports
                            </a>
                        </form>
                    </ul>
                </div>
                <?php if( Auth::check() && $isSuperUser ): ?>
                    <button type="button" class="btn btn-white pull-right" data-toggle="modal" data-target="#grapher-backend-info-modal">
                        Backend Info
                    </button>
                <?php endif; ?>
            </nav>
            <h3>
                <?php switch( get_class( $t->graph ) ):
                    case IXP\Services\Grapher\Graph\Customer::class: ?>
                        Aggregate Statistics for All Peering Ports
                        <?php break;
                    case IXP\Services\Grapher\Graph\VirtualInterface::class: ?>
                        LAG
                        <?php if( $sp = $t->graph->virtualInterface()->switchPort() ): ?>
                            <small>
                                <?= $sp->switcher->name ?>
                                <?php
                                    $names = [];
                                    foreach( $t->graph->virtualInterface()->physicalInterfaces as $pi ){
                                        if( $sp = $pi->switchPort ){
                                            $names[] = $sp->name;
                                        }
                                    }
                                ?>

                                [<?= implode( ', ', $names ) ?>]
                            </small>
                        <?php endif;
                        break;
                    case IXP\Services\Grapher\Graph\PhysicalInterface::class: ?>
                        Port:  <?= $t->graph->physicalInterface()->switchport->switcher->name ?> / <?= $t->graph->physicalInterface()->switchport->name ?>

                        <?php if( $t->resellerMode() && $c->isReseller ): ?>
                            <br />
                            <small>
                                <?php if( $t->graph->physicalInterface()->switchport->typePeering() ): ?>
                                    Peering Port
                                <?php elseif( $t->graph->physicalInterface()->switchport->typeFanout() ): ?>
                                    Fanout Port for <a href="<?= route( 'customer@overview', [ 'cust' => $t->graph->physicalInterface()->relatedInterface()->virtualInterface->customer->id ] ) ?>">
                                        <?= $t->graph->physicalInterface()->relatedInterface()->virtualInterface->customer->abbreviatedName ?>
                                    </a>
                                <?php elseif( $t->graph->physicalInterface()->switchport->typeReseller() ): ?>
                                    Reseller Uplink Port
                                <?php endif; ?>
                            </small>
                        <?php endif;
                        break;
                endswitch; ?>
            </h3>

            <div class="row">
                <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $pvalue => $pname ): ?>
                    <div class="col-sm-12 col-lg-6 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h3><?= $pname ?> Graph</h3>
                            </div>
                            <div class="card-body">
                                <p>
                                    <?= $t->graph->setPeriod( $pvalue )->renderer()->boxLegacy() ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php if( Auth::check() && $isSuperUser ):?>
        <div class="modal" tabindex="-1" role="dialog" id="grapher-backend-info-modal">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Grapher Backend Information
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php foreach( $t->graph::PERIOD_DESCS as $pvalue => $pname ): ?>
                            <h6><?= $pname ?></h6>
                            <ul>
                                <li>
                                    Backend: <?= $t->graph->setPeriod( $pvalue )->backend()->name() ?> <code><?= get_class( $t->graph->backend() ) ?></code>
                                </li>
                                <li>
                                    Data File Path: <code><?= $t->graph->dataPath() ?></code>
                                </li>
                            </ul>
                        <?php endforeach; ?>
                        <pre><?= print_r( $t->graph->toc() ) ?></pre>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $this->append() ?>