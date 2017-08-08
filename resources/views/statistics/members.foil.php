<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <li>
        Statistics
    </li>

    <li>
        Graphs

        <?php if( $t->graph ): ?>
            (
                <?= $t->graph->resolveCategory( $t->graph->category() ) ?>
                /
                <?= $t->graph->resolvePeriod( $t->graph->period() ) ?>
                <?php if( $t->graph->protocol() != IXP\Services\Grapher\Graph::PROTOCOL_ALL ): ?>
                    /
                    <?= $t->graph->resolveProtocol( $t->graph->protocol() ) ?>
                <?php endif; ?>
            )
        <?php endif; ?>
    </li>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <?php if( in_array( 'mrtg', config('grapher.backend' ) ) ): ?>

        <nav class="navbar navbar-default">
            <div class="container-fluid">

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
                <input class="btn btn-default" type="submit" name="submit" value="Change" />

                </form>

            </div>
        </nav>

    <?php endif; ?>

    <?php if( in_array( 'sflow', config('grapher.backend' ) ) ): ?>

        <nav class="navbar navbar-default">
            <div class="container-fluid">

                <div class="navbar-header">
                    <a class="navbar-brand" href="<?= route('statistics/members') ?>">SFlow:</a>
                </div>

                <form class="navbar-form navbar-left action="<?= route('statistics/members' ) ?>" method="post">

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
                <input class="btn btn-default" type="submit" name="submit" value="Change" />

                </form>

            </div>
        </nav>

    <?php endif; ?>

    <div class="row-fluid">

    <?php if( !$t->graph ): ?>

        <div class="alert alert-info" role="alert">
            No graphs found for the requested parameters.
        </div>

    <?php else: ?>

        <?php foreach( $t->graphs as $graph ): ?>

            <div id="graph-row" class="col-xs-12 col-sm-12 col-md-6 col-lg-4">

                <div class="well">
                    <h4 style="vertical-align: middle">
                        <?= $graph->customer()->getFormattedName() ?>
                        <?php if( config('grapher.backends.sflow.enabled') && isset( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS[$graph->category()] ) ): ?>
                            <span class="pull-right">
                                <a class="btn btn-default btn-sm" href="<?= url('') . '/statistics/p2p/shortname/' . $graph->customer()->getShortname() . '/category/' . $graph->category() . '/period/' . $graph->period() ?>">
                                    <span class="glyphicon glyphicon-random"></span>
                                </a>
                            </span>
                        <?php endif; ?>
                    </h4>

                    <p>
                        <br />
                        <?= $graph->renderer()->boxLegacy() ?>
                    </p>
                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<script>
<?= $t->insert( 'statistics/js/members.foil.js' ); ?>
</script>
<?php $this->append() ?>