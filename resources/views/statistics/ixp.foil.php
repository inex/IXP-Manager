<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?= config( 'identity.orgname' ) ?> Public Traffic Statistics
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <div class="row">

        <div class="col-md-12">

            <?= $t->alerts() ?>

            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">

                <a class="navbar-brand" href="<?= route( "statistics/ixp" ) ?>">Graph Options:</a>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">

                        <form class="navbar-form navbar-left form-inline">

                            <li class="nav-item mr-2">
                                <div class="nav-link d-flex ">
                                    <label for="period" class="mr-2">Category:</label>
                                    <select id="form-select-category" name="category" class="form-control">
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                                            <option value="<?= $cvalue ?>" <?= $t->category != $cvalue ?: 'selected="selected"' ?>><?= $cname ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>
                            </li>
                            <a class="btn btn-outline-secondary" href="<?= route( 'statistics/infrastructure' ) ?>">Per-Infrastructure Graphs</a>

                        </form>
                    </ul>
                </div>

            </nav>

            <div class="row">
                <?php foreach( IXP\Services\Grapher\Graph::PERIODS as $pvalue => $pname ): ?>

                    <div class="col-md-6">

                        <div class="card mb-4">
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

    let base_route   = "<?= route( 'statistics/ixp' ) ?>";
    let sel_category = $("#form-select-category");

    function changeGraph() {
        window.location = `${base_route}/${sel_category.val()}`;
    }

    sel_category.on( 'change', changeGraph );

</script>

<?php $this->append() ?>
