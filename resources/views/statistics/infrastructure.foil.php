<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Infrastructure Aggregate Graphs - <?= $t->infra->name ?> (<?= IXP\Services\Grapher\Graph::resolveCategory( $t->category ) ?>)
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <div class="row">
        <?= $t->alerts() ?>
        <div class="col-md-12">
            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand" href="<?= route( "statistics@infrastructure" ) ?>">
                    Graph Options:
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex">
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="category" class="col-lg-6 col-sm-4">Infrastructure:</label>
                                    <select id="form-select-infraid" name="infraid" class="form-control" >
                                        <?php foreach( $t->infras as $i ): ?>
                                            <option value="<?= $i->id ?>" <?= $t->infra->id !== $i->id ?: 'selected="selected"' ?>><?= $i->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="period" class="col-lg-6 col-sm-4" >Category:</label>
                                    <select id="form-select-category" name="category" class="form-control">
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                                            <option value="<?= $cvalue ?>" <?= $t->category !== $cvalue ?: 'selected="selected"' ?>><?= $cname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                            <a class="btn btn-white float-right ml-2" href="<?= route( 'statistics@ixp' ) ?>">
                                Overall IXP Graphs
                            </a>
                        </form>
                    </ul>
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
                                <?= $t->graph->setPeriod( $pvalue )->renderer()->boxLegacy() ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        let base_route   = "<?= route( 'statistics@infrastructure' ) ?>";
        let sel_infraid  = $("#form-select-infraid");
        let sel_category = $("#form-select-category");

        function changeGraph() {
            window.location = `${base_route}/${sel_infraid.val()}/${sel_category.val()}`;
        }

        sel_infraid.on(  'change', changeGraph );
        sel_category.on( 'change', changeGraph );

    </script>
<?php $this->append() ?>