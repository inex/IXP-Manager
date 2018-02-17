<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
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
                Port Graphs
            </a>
        </li>

    <?php else: ?>

        IXP Port Graphs :: <?= $t->cust->getFormattedName() ?>

    <?php endif; ?>

<?php $this->append() ?>


<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>

    <?php $this->section( 'page-header-postamble' ) ?>

        <li>
            Statistics Drilldown (<?= $t->graph->resolveMyCategory() ?>)
        </li>

    <?php $this->append() ?>

<?php endif; ?>



<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <div class="row col-sm-12">

        <nav class="navbar navbar-default">

            <div class="">
                <div class="navbar-header">
                    <a class="navbar-brand" href="http://ixp.test/statistics/members">Graph Options:</a>
                </div>

                <form class="navbar-form navbar-left form-inline" method="get">
                    <div class="form-group">
                        <label for="category">Type:</label>
                        <select id="category" name="category" onchange="this.form.submit()" class="form-control">
                            <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $cvalue => $cname ): ?>
                                <option value="<?= $cvalue ?>" <?= $t->graph->category() == $cvalue ? 'selected="selected"' : '' ?>><?= $cname ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

            </div>
        </nav>
    </div>

    <div class="col-sm-12">
        <h3>
            <?php switch( get_class( $t->graph ) ):

                case IXP\Services\Grapher\Graph\Customer::class: ?>

                    Aggregate Statistics for All Peering Ports

                    <?php break;

                case IXP\Services\Grapher\Graph\VirtualInterface::class: ?>

                    LAG
                    <?php if( $sp = $t->graph->virtualInterface()->getSwitchPort() ): ?>
                        <small>
                            <?= $sp->getSwitcher()->getName() ?>
                        </small>
                    <?php endif;

                    break;

                case IXP\Services\Grapher\Graph\PhysicalInterface::class: ?>

                    Port:  <?= $t->graph->physicalInterface()->getSwitchPort()->getSwitcher()->getName() ?> / <?= $t->graph->physicalInterface()->getSwitchPort()->getName() ?>

                    <?php if( $t->resellerMode() && $t->c->isReseller() ): ?>

                        <br />
                        <small>
                            <?php if( $t->graph->physicalInterface()->getSwitchPort()->isTypePeering() ): ?>
                                Peering Port
                            <?php elseif( $t->graph->physicalInterface()->getSwitchPort()->isTypeFanout() ): ?>
                                Fanout Port for <a href="<?= route( 'customer@overview', [ 'id' => $t->graph->physicalInterface()->getRelatedInterface()->getVirtualInterface()->getCustomer()->getId() ] ) ?>">
                                    <?= $t->graph->physicalInterface()->getRelatedInterface()->getVirtualInterface()->getCustomer()->getAbbreviatedName() ?>
                                </a>
                            <?php elseif( $t->graph->physicalInterface()->getSwitchPort()->isTypeReseller() ): ?>
                                Reseller Uplink Port
                            <?php endif; ?>
                        </small>

                    <?php endif;

                    break;

                endswitch;
            ?>

        </h3>
        <br>
    </div>

    <div class="row">

        <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $pvalue => $pname ): ?>

            <div class="col-sm-6">
                <div class="well">
                    <h3><?= $pname ?> Graph</h3>
                    <p>
                        <?= $t->graph->setPeriod( $pvalue )->renderer()->boxLegacy() ?>
                    </p>
                </div>
            </div>

        <?php endforeach; ?>

    </div>

<?php $this->append() ?>
