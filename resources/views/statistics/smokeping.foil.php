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
            Statistics
        </a>
    </li>

<?php else: ?>

    IXP Smokeping Graphs

    <div class="pull-right">
        <a class="btn btn-default" href="<?= route( 'statistics@member', [ 'id' => $t->c->getId() ] ) ?>">All Ports</a>
    </div>

<?php endif; ?>

<?php $this->append() ?>


<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>

    <?php $this->section( 'page-header-postamble' ) ?>

    <li>
        Smokeping
    </li>

    <?php $this->append() ?>

<?php endif; ?>



<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <div class="row col-sm-12">

        <nav class="navbar navbar-default">

            <div>

                <div class="navbar-header">
                    <a class="navbar-brand" href="">Smokeping Graphs for <?= $t->vli->getVirtualInterface()->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getInfrastructure()->getName() ?> on <?= $t->ip ?>:</a>
                </div>

                <form class="navbar-form navbar-left form-inline"  action="<?= route( "statistics@smokeping", [ "id" => $t->vli->getId() ] ) ?>" method="GET">
                    <div class="form-group">
                        <label for="protocol">Protocol:</label>
                        <select id="protocol" name="protocol" onchange="this.form.submit();" class="form-control" placeholder="Select Protocol">
                            <option></option>
                            <?php foreach( IXP\Services\Grapher\Graph::PROTOCOLS_REAL as $pvalue => $pname ): ?>
                                <option value="<?= $pvalue ?>" <?php if( $t->protocol == $pvalue ): ?> selected <?php endif; ?>  ><?= $pname ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

            </div>

        </nav>

    </div>

    <div class="row col-sm-12">
        <?php foreach( IXP\Services\Grapher\Graph\Latency::PERIODS as $scale => $name ): ?>

            <div class="col-sm-6">

                <h4>Last <?= $name ?></h4>

                <?= $t->grapher->setPeriod( $scale )->renderer()->boxLegacy() ?>
            </div>

        <?php endforeach; ?>

    </div>

<?php $this->append() ?>
