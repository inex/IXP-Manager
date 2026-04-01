<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );

$isSuperUser = Auth::check() ? Auth::getUser()->isSuperUser() : false;

?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::check() && $isSuperUser ): ?>
        <a href="<?= route( 'customer@overview', [ 'cust' => $t->srcCustomer->id ] ) ?>" >
            <?= $t->srcCustomer->getFormattedName() ?>
        </a>
        /
        <a href="<?= route( 'statistics@member', [ 'cust' => $t->srcCustomer->id ] ) ?>" >
            Statistics
        </a>
        /
        <a href="<?= route( 'statistics@p2p-table', [ 'custid' => $t->srcCustomer->id ] ) ?>" >
            P2P
        </a>
        /
        Traffic Exchanged with
        <a href="<?= route( 'statistics@p2p-totals', [ 'srcCust' => $this->dstCustomer->id, 'dstCust' => $t->srcCustomer->id ] ) ?>">
            <?= $this->dstCustomer->getFormattedName() ?>
        </a>

    <?php else: ?>
        Peer to Peer Graphs
    <?php endif; ?>

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <div class="row">
        <?= $t->alerts() ?>
        <div class="col-md-12">
            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand" href="<?= route( "statistics@p2p-totals", ['srcCust' => $t->srcCustomer->id, 'dstCust' => $t->dstCustomer->id] ) ?>">
                    Graph Options:
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <form class="navbar-form navbar-left form-inline d-block d-lg-flex">
                        <ul class="navbar-nav mt-2 mt-lg-0">
                            <li class="nav-item">
                                <div class="nav-link d-flex">
                                    <label for="form-select-protocol" class="col-lg-6 col-sm-4">Protocol:</label>
                                    <select id="form-select-protocol" name="protocol" class="form-control">
                                        <?php foreach( IXP\Services\Grapher\Graph::PROTOCOL_DESCS as $pvalue => $pname  ): ?>
                                            <?php if( !in_array($pvalue, $t->possibleProtocols)) continue; ?>
                                            <option value="<?= $pvalue ?>" <?= $t->protocol !== $pvalue ?: 'selected="selected"' ?>><?= $pname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link d-flex">
                                    <label for="form-select-category" class="col-lg-6 col-sm-4" >Category:</label>
                                    <select id="form-select-category" name="category" class="form-control" >
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                                            <option value="<?= $cvalue ?>" <?= $t->category !== $cvalue ?: 'selected="selected"' ?>><?= $cname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                        </ul>
                    </form>
                </div>
            </nav>

            <div class="row">
                <?php foreach( IXP\Services\Grapher\Graph::PERIODS as $pvalue => $pname ): ?>
                    <div class="col-md-12 col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h3><?= IXP\Services\Grapher\Graph::resolvePeriod( $pvalue ) ?> Graph</h3>
                            </div>
                            <div class="card-body">
                                <a href="<?= route( 'statistics@p2p-per-vlan', [
                                        'srcCust' => $t->srcCustomer->id,
                                        'dstCust' => $t->dstCustomer->id,
                                        'protocol' => $t->protocol,
                                        'category' => $t->category,
                                        'period' => $pvalue] ) ?>">
                                    <?= $t->graph->setPeriod( $pvalue )->renderer()->boxLegacy() ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


    <div class="card mt-4">
        <div class="card-header">
            <h3>Total Peer-to-Peer Traffic with <?= $t->ee( $this->dstCustomer->getFormattedName() ) ?></h3>
        </div>
        <div class="card-body">
            <p>
                These graphs show the <strong>total</strong> traffic exchanged between you and <?= $t->ee( $this->dstCustomer->getFormattedName() ) ?> between the following ports:
            </p>

            <div class="row">
                <div class="col-lg-5 col-sm-12 text-center">
                    <p>Your ports</p>
                    <pre class="p-2 border text-center"><?php
                        foreach ($t->myPorts as $portDescription): ?>
<?= $portDescription ?><br><?php
                        endforeach; ?>
</pre>
                </div>

                <div class="col-lg-5 col-sm-12 text-center">
                    <p>Their ports:</p>
                    <pre class="p-2 border text-center"><?php
                        foreach ($t->theirPorts as $portDescription): ?>
<?= $portDescription ?><br><?php
                        endforeach; ?>
</pre>
                </div>
            </div>

            <?php if (count($t->possibleProtocols) > 1): ?>
            <p>
                You can select a particular protocol to view, or choose all to see the total traffic for all protocols.
            </p>
            <?php endif; ?>

            <p>
                Click any graph to view graphs of all traffic flows for each port separately.
            </p>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        let base_route   = "<?= route( 'statistics@p2p-totals', ['srcCust' => $t->srcCustomer->id, 'dstCust' => $t->dstCustomer->id] ) ?>";
        let sel_category = $("#form-select-category");
        let sel_protocol = $("#form-select-protocol");

        function changeGraph() {
            window.location = `${base_route}?protocol=${sel_protocol.val()}&category=${sel_category.val()}`;
        }

        sel_category.on(  'change', changeGraph );
        sel_protocol.on( 'change', changeGraph );

    </script>
<?php $this->append() ?>