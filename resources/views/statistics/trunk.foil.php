<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    Trunk Graphs - <?= $t->ee( $t->graph->title() ) ?>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <div class="row">

        <div class="col-md-12">

            <?= $t->alerts() ?>

            <nav class="navbar navbar-default">
                <div class="">

                    <div class="navbar-header">
                        <a class="navbar-brand" href="<?= route( "statistics/trunk" ) ?>">Graph Options:</a>
                    </div>

                    <form class="navbar-form navbar-left form-inline">

                        <div class="form-group">

                            <label for="trunkid">Trunk:</label>
                            <select id="form-select-trunkid" name="trunkid" class="form-control">
                                <?php foreach( $t->graphs as $id => $name ): ?>
                                    <option value="<?= $id ?>" <?= $t->trunkid != $id ?: 'selected="selected"' ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="category">Category:</label>
                            <select id="form-select-category" name="category" class="form-control">
                                <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                                    <option value="<?= $cvalue ?>" <?= $t->category != $cvalue ?: 'selected="selected"' ?>><?= $cname ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>

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

    let base_route   = "<?= route( 'statistics/trunk' ) ?>";
    let sel_trunkid  = $("#form-select-trunkid");
    let sel_category = $("#form-select-category");

    function changeGraph() {
        window.location = `${base_route}/${sel_trunkid.val()}/${sel_category.val()}`;
    }

    sel_trunkid.on(  'change', changeGraph );
    sel_category.on( 'change', changeGraph );

</script>

<?php $this->append() ?>
