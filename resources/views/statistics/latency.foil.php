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

        Latency Graphs

        <div class="pull-right">
            <a class="btn btn-default" href="<?= route( 'statistics@member', [ 'id' => $t->c->getId() ] ) ?>">All Ports</a>
        </div>

    <?php endif; ?>

<?php $this->append() ?>


<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>

    <?php $this->section( 'page-header-postamble' ) ?>

    <li>
        Latency
    </li>

    <?php $this->append() ?>

<?php endif; ?>



<?php $this->section('content') ?>

<div class="row">

    <?= $t->alerts() ?>

    <div class="col-md-12">

        <nav class="navbar navbar-default">

            <div class="navbar-header">
                <a class="navbar-brand">Latency Graphs for <?= $t->vli->getVlan()->getName() ?> on <?= $t->ip ?></a>
            </div>

            <?php if( $t->vli->canGraphForLatency( IXP\Services\Grapher\Graph::PROTOCOL_IPV4 ) && $t->vli->canGraphForLatency( IXP\Services\Grapher\Graph::PROTOCOL_IPV6 ) ): ?>
                <form class="navbar-form navbar-left form-inline">
                    <div class="form-group">
                        <label for="select_protocol">Protocol:</label>
                        <select id="select_protocol" name="protocol" class="form-control">
                            <?php foreach( IXP\Services\Grapher\Graph::PROTOCOLS_REAL as $pvalue => $pname ): ?>
                                <?php if( $t->vli->canGraphForLatency( $pvalue ) ): ?>
                                    <option value="<?= $pvalue ?>" <?php if( $t->protocol == $pvalue ): ?> selected <?php endif; ?>  >
                                        <?= IXP\Services\Grapher\Graph::PROTOCOL_DESCS[ $pvalue ] ?> :: <?= $t->vli->getIPAddress( $pvalue )->getAddress() ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            <?php endif; ?>

        </nav>

    </div>

    <div class="col-md-12">
        <div class="alert alert-info">
            Latency graphs are a tool for monitoring network latency and is an invaluable asset when diagnosing some IXP issues.
            <b>While they should never be used as a tool for monitoring IXP latency or packet loss</b> (as routers de-prioritise ICMP requests
            and/or may not have a suitably powerful management plane), they can act as an extremely useful tool for identifying and diagnosing
            customer / member issues. What we really look for here is recent changes over time.
        </div>
    </div>

    <?php foreach( IXP\Services\Grapher\Graph\Latency::PERIODS_DESC as $scale => $name ): ?>

        <div class="col-md-12">

            <h4>Last <?= $name ?></h4>

            <img border="0" src="<?= $t->graph->setPeriod( $scale )->url() ?>" />
            <br><br><br>

        </div>

    <?php endforeach; ?>

</div>

<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>

<?php if( $t->vli->canGraphForLatency( IXP\Services\Grapher\Graph::PROTOCOL_IPV4 ) && $t->vli->canGraphForLatency( IXP\Services\Grapher\Graph::PROTOCOL_IPV6 ) ): ?>

<script>

    let base_route   = "<?= url( 'statistics/latency' ) . '/' . $t->vli->getId() ?>";
    let dd_protocol = $('#select_protocol');

    dd_protocol.change( function() {
        window.location = `${base_route}/${dd_protocol.val()}`;
    });

</script>

<?php endif; ?>

<?php $this->append() ?>
