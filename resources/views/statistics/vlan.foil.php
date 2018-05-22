<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    VLAN Graphs - <?= $t->vlan->getName() ?> (<?= IXP\Services\Grapher\Graph::resolveProtocol( $t->protocol ) ?>)
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <div class="row">

        <div class="col-md-12">

            <?= $t->alerts() ?>

            <div class="alert alert-info">
                VLAN graphs are based on sflow sampling. These can under/over report true traffic levels due to known issues
                with some switching hardware. See the
                <a href="<?= route( 'statistics/infrastructure' ) ?>">infrastructure graphs</a>
                for a more realistic representation of overall traffic.
            </div>

            <nav class="navbar navbar-default">
                <div class="">

                    <div class="navbar-header">
                        <a class="navbar-brand" href="<?= route( "statistics/vlan" ) ?>">Graph Options:</a>
                    </div>

                    <form class="navbar-form navbar-left form-inline">

                        <div class="form-group">
                            <label for="form-select-vlanid">Vlan:</label>

                            <select id="form-select-vlanid" name="vlanid" class="form-control" >
                                <?php foreach( $t->vlans as $id => $v ): ?>
                                    <option value="<?= $id ?>" <?= $t->vlanid != $id ?: 'selected="selected"' ?>><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="form-select-protocol">Protocol:</label>
                            <select id="form-select-protocol" name="protocol" class="form-control">
                                <?php foreach( IXP\Services\Grapher\Graph::PROTOCOL_REAL_DESCS as $pvalue => $pname ): ?>
                                    <option value="<?= $pvalue ?>" <?= $t->protocol != $pvalue ?: 'selected="selected"' ?>><?= $pname ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="form-select-category">Category:</label>
                            <select id="form-select-category" name="category" class="form-control">
                                <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                                    <option value="<?= $cvalue ?>" <?= $t->category != $cvalue ?: 'selected="selected"' ?>><?= $cname ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a class="btn btn-default" href="<?= route( 'statistics/infrastructure' ) ?>">Infrastructure Graphs</a>

                    </form>

                </div>
            </nav>


            <?php foreach( IXP\Services\Grapher\Graph::PERIODS as $pvalue => $pname ): ?>

                <div class="col-md-12">

                    <h3><?= IXP\Services\Grapher\Graph::resolvePeriod( $pvalue ) ?> Graph</h3>

                    <img border="0" src="<?= $t->graph->setPeriod( $pvalue )->url() ?>" />
                    <br><br><br>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

<?php $this->append() ?>



<?php $this->section( 'scripts' ) ?>

<script>

    let base_route   = "<?= route( 'statistics/vlan' ) ?>";
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
