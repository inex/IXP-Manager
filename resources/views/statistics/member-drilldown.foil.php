<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>


<?php $this->section( 'title' ) ?>
<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
    <a href="<?= route( 'customer@list' )?>">Customers</a>
    <li>
        <a href="<?= route( 'customer@list' )?>" >
            <?= $t->c->getFormattedName() ?>
        </a>
    </li>
<?php else: ?>
    IXP Interface Statistics :: <?= $t->cust->getFormattedName() ?>
<?php endif; ?>
<?php $this->append() ?>

<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Statistics Drilldown (<?= $t->category ?>)
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

                <?php if( $t->type == null ): ?>
                    <?php $formRoute =  route( "statistics@memberDrilldown", [ "id" => $t->c->getId() ] ) ?>
                <?php else: ?>
                    <?php $formRoute =  route( "statistics@memberDrilldown", [ "id" => $t->c->getId(), "type" => $t->type , "typeid" => $t->typeid ] ) ?>
                <?php endif; ?>

                <form class="navbar-form navbar-left form-inline"  action="<?= $formRoute ?>" method="get">
                    <div class="form-group">
                        <label for="category">Type:</label>
                        <select id="category" name="category" onchange="this.form.submit()" class="form-control">
                            <?php foreach( $t->categories as $cvalue => $cname ): ?>
                                <option value="<?= $cvalue ?>" <?php if( $t->category == $cvalue ): ?> selected <?php endif; ?> ><?= $cname ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

            </div>
        </nav>
    </div>

    <div class="col-sm-12">
        <h3>
            <?php if( $t->type == "aggregate" ): ?>
                Aggregate Statistics for All Peering Ports
            <?php elseif( $t->type == "lag" ): ?>
                LAG
                <small>
                    <?= $t->sname?>: <?= $t->spname ?>
                </small>
            <?php else: ?>
                Port:  <?= $t->sname?> / <?= $t->spname ?>

                <?php if( $t->resellerMode() && $t->c->isReseller() ): ?>
                <br /><small>
                    <?php if( $t->pi->getSwitchPort()->isTypePeering() ): ?>
                        Peering Port
                    <?php elseif( $pi->getSwitchPort()->isTypeFanout() ): ?>
                        Fanout Port for <a href="<?= route( 'customer@overview', [ 'id' => $pi->getRelatedInterface()->getVirtualInterface()->getCustomer()->getId() ] ) ?>">
                        <?= $pi->getRelatedInterface()->getVirtualInterface()->getCustomer()->getAbbreviatedName() ?>
                        </a>
                    <?php elseif( $t->pi->getSwitchPort()->isTypeReseller() ): ?>
                        Reseller Uplink Port
                    <?php endif; ?>
                </small>
                <?php endif; ?>

            <?php endif; ?>
        </h3>
    </div>

    <div class="row">
        <?php foreach( $t->periods as $pvalue => $pname ): ?>
            <div class="col-sm-6">
                <div class="well">
                    <h3><?= $pname ?> Graph</h3>
                    <p>
                        <?= $t->grapher->setCategory( $t->category )->setPeriod( $pvalue )->renderer()->boxLegacy() ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php $this->append() ?>
