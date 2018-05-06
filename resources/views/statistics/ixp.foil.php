<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <?= config( 'identity.orgname' ) ?> Public Traffic Statistics
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <div class="row">

        <div class="col-md-12">

            <?= $t->alerts() ?>

            <nav class="navbar navbar-default">
                <div class="">

                    <div class="navbar-header">
                        <a class="navbar-brand" href="<?= route( "statistics/ixp" ) ?>">Graph Options:</a>
                    </div>

                    <form class="navbar-form navbar-left form-inline">

                        <div class="form-group">
                            <label for="period">Category:</label>
                            <select id="form-select-category" name="category" class="form-control">
                                <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                                    <option value="<?= $cvalue ?>" <?= $t->category != $cvalue ?: 'selected="selected"' ?>><?= $cname ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <a class="btn btn-default" href="<?= route( 'statistics/infrastructure' ) ?>">Per-Infrastructure Graphs</a>

                    </form>

                </div>
            </nav>

            <?php foreach( IXP\Services\Grapher\Graph::PERIODS as $pvalue => $pname ): ?>

                <div class="col-md-6">

                    <div class="well">
                        <h3><?= IXP\Services\Grapher\Graph::resolvePeriod( $pvalue ) ?> Graph</h3>
                        <?= $t->graph->setPeriod( $pvalue )->renderer()->boxLegacy() ?>
                    </div>
                </div>

            <?php endforeach; ?>

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
