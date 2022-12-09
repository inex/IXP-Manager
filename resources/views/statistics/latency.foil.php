<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $isSuperUser = Auth::check() ? Auth::getUser()->isSuperUser() : false;
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::check() && $isSuperUser ): ?>
        <a href="<?= route( 'customer@overview', [ 'cust' => $t->c->id ] ) ?>" >
            <?= $t->c->getFormattedName() ?>
        </a>
       /
        <a href="<?= route( 'statistics@member', [ 'cust' => $t->c->id ] ) ?>" >
            Statistics
        </a>
        /
        Latency Graphs

    <?php else: ?>
        Latency Graphs
    <?php endif; ?>
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( Auth::check() && !$isSuperUser ): ?>
        <a class="btn btn-white" href="<?= route( 'statistics@member', [ 'cust' => $t->c->id ] ) ?>">All Ports</a>
    <?php endif; ?>
<?php $this->append() ?>


<?php $this->section('content') ?>
    <div class="row">
        <?= $t->alerts() ?>
        <div class="col-md-12">
            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand">
                  Latency Graphs for <?= $t->vli->vlan->name ?> on <?= $t->ip ?>
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <?php if( $t->vli->canGraphForLatency( IXP\Services\Grapher\Graph::PROTOCOL_IPV4 ) && $t->vli->canGraphForLatency( IXP\Services\Grapher\Graph::PROTOCOL_IPV6 ) ): ?>
                            <form class="navbar-form navbar-left form-inline d-block d-lg-flex">
                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="select_protocol" class="col-sm-4 col-lg-4">Protocol:</label>
                                        <select id="select_protocol" name="protocol" class="form-control">
                                            <?php foreach( IXP\Services\Grapher\Graph::PROTOCOLS_REAL as $pvalue => $pname ): ?>
                                                <?php if( $t->vli->canGraphForLatency( $pvalue ) ): ?>
                                                    <option value="<?= $pvalue ?>" <?php if( $t->protocol === $pvalue ): ?> selected <?php endif; ?>  >
                                                        <?= IXP\Services\Grapher\Graph::PROTOCOL_DESCS[ $pvalue ] ?> :: <?= $t->vli->getIPAddress( $pvalue )->address ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>
                            </form>
                        <?php endif; ?>
                    </ul>
                </div>

                <?php if( Auth::check() && $isSuperUser ): ?>
                    <button type="button" class="btn btn-white pull-right" data-toggle="modal" data-target="#grapher-backend-info-modal">
                        Backend Info
                    </button>
                <?php endif; ?>
            </nav>
        </div>

        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-info-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">
                        Latency graphs are a tool for monitoring network latency and are an invaluable asset when diagnosing some IXP issues.
                        <b>While they should never be used as a tool for monitoring IXP latency or packet loss</b> (as routers de-prioritise ICMP requests
                        and/or may not have a suitably powerful management plane), they can act as an extremely useful tool for identifying and diagnosing
                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> issues. What we really look for here is recent changes over time.
                    </div>
                </div>
            </div>
        </div>

        <?php foreach( IXP\Services\Grapher\Graph\Latency::PERIODS_DESC as $scale => $name ): ?>
            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Last <?= $name ?></h4>
                    </div>
                    <div class="card-body">
                        <img border="0" class="img-fluid" src="<?= $t->graph->setPeriod( $scale )->url() ?>" />
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if( Auth::check() && $isSuperUser ):?>
        <div class="modal" tabindex="-1" role="dialog" id="grapher-backend-info-modal">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Grapher Backend Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php foreach( $t->graph::PERIODS_DESC as $pvalue => $pname ): ?>
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

<?php $this->section( 'scripts' ) ?>
    <?php if( $t->vli->canGraphForLatency( IXP\Services\Grapher\Graph::PROTOCOL_IPV4 ) && $t->vli->canGraphForLatency( IXP\Services\Grapher\Graph::PROTOCOL_IPV6 ) ): ?>
        <script>
            let base_route   = "<?= url( 'statistics/latency' ) . '/' . $t->vli->id ?>";
            let dd_protocol = $('#select_protocol');

            dd_protocol.change( function() {
                window.location = `${base_route}/${dd_protocol.val()}`;
            });
        </script>
    <?php endif; ?>
<?php $this->append() ?>