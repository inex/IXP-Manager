<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    VLAN Graphs - <?= $t->vlan->name ?> (<?= IXP\Services\Grapher\Graph::resolveProtocol( $t->protocol ) ?>)
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>
            <div class="alert alert-info" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-info-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">
                        VLAN graphs are based on sflow sampling. These can under/over report true traffic levels due to known issues
                        with some switching hardware. See the
                        <a href="<?= route( 'statistics@infrastructure' ) ?>">infrastructure graphs</a>
                        for a more realistic representation of overall traffic.
                    </div>
                </div>
            </div>

            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand" href="<?= route( "statistics@vlan" ) ?>">
                    Graph Options:
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex">
                            <li class="nav-item mr-md-2">
                                <div class="nav-link d-flex ">
                                    <label class="col-4 col-md-4 col-lg-3" for="form-select-vlanid">Vlan:</label>
                                    <select id="form-select-vlanid" name="vlanid" class="form-control" >
                                        <?php foreach( $t->vlans as $v ): ?>
                                            <option value="<?= $v->id ?>" <?= $t->vlan->id !== $v->id ?: 'selected="selected"' ?>><?= $v->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item mr-md-2">
                                <div class="nav-link d-flex ">
                                    <label class="col-4 col-md-4 col-lg-6" for="form-select-protocol">Protocol:</label>
                                    <select id="form-select-protocol" name="protocol" class="form-control">
                                        <?php foreach( IXP\Services\Grapher\Graph::PROTOCOL_REAL_DESCS as $pvalue => $pname ): ?>
                                            <option value="<?= $pvalue ?>" <?= $t->protocol !== $pvalue ?: 'selected="selected"' ?>><?= $pname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item mr-md-2">
                                <div  class="nav-link d-flex ">
                                    <label class="col-4 col-md-4 col-lg-6" for="form-select-category">Category:</label>
                                    <select id="form-select-category" name="category" class="form-control">
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                                            <option value="<?= $cvalue ?>" <?= $t->category !== $cvalue ?: 'selected="selected"' ?>><?= $cname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                            <a class="btn btn-white float-right" href="<?= route( 'statistics@infrastructure' ) ?>">
                                Infrastructure Graphs
                            </a>
                        </form>
                    </ul>
                </div>
            </nav>

            <?php foreach( IXP\Services\Grapher\Graph::PERIODS as $pvalue => $pname ): ?>
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3>
                                <?= IXP\Services\Grapher\Graph::resolvePeriod( $pvalue ) ?> Graph
                            </h3>
                        </div>
                        <div class="card-body">
                            <img class="img-fluid" src="<?= $t->graph->setPeriod( $pvalue )->url() ?>" />
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        let base_route   = "<?= route( 'statistics@vlan' ) ?>";
        let sel_vlanid   = $("#form-select-vlanid");
        let sel_protocol = $("#form-select-protocol");
        let sel_category = $("#form-select-category");

        function changeGraph() {
            window.location = `${base_route}/${sel_vlanid.val()}/${sel_protocol.val()}/${sel_category.val()}`;
        }

        sel_vlanid.on(  'change',  changeGraph );
        sel_protocol.on( 'change', changeGraph );
        sel_category.on( 'change', changeGraph );

    </script>
<?php $this->append() ?>