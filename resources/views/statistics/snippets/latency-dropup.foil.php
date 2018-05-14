
<?php

    // discover latency graph details
    $latencyGraphs = [];
    foreach( $t->vi->getVlanInterfaces() as $vli ) {
        foreach( IXP\Services\Grapher\Graph::PROTOCOLS_REAL as $p ) {
            if( $vli->canGraphForLatency( $p ) ) {
                $latencyGraph = Grapher::latency( $vli )->setProtocol( $p );
                if( Grapher::backendsForGraph( $latencyGraph ) ) {
                    $latencyGraphs[] = $latencyGraph;
                }
            }
        }
    }

?>

<?php if( count( $latencyGraphs ) ): ?>
    <?php if( $t->grapher()->canAccessAllCustomerLatencyGraphs() || ( Auth::check() && Auth::user()->getCustomer()->getId() == $t->vi->getCustomer()->getId() ) ): ?>

        <div class="btn-group dropup">
            <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" title="Latency Graphs"  aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-time"></i>
                &nbsp;
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>

            <ul class="dropdown-menu">
                <li class="dropdown-header">Latency Graphs - Targets</li>
                <li role="separator" class="divider"></li>
                <?php foreach( $latencyGraphs as $latencyGraph ): ?>
                    <li>
                        <a href="<?= route( 'statistics@latency', [ 'vliid' => $latencyGraph->vli()->getId(), 'protocol' => $latencyGraph->protocol() ] ) ?>">
                            <?= $latencyGraph->vli()->getIPAddress( $latencyGraph->protocol() )->getAddress() ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    <?php endif; ?>
<?php endif; ?>

