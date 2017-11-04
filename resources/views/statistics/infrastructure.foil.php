<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    Infrastructure Aggregate Graphs - <?= $t->infra->getName() ?> (<?= IXP\Services\Grapher\Graph::resolveCategory( $t->category ) ?>)
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

<div class="row">

<?= $t->alerts() ?>

<div class="col-md-12">

<p>
<form class="form-horizontal">
<table>
<tr>
    <td width="20"></td>
    <td valign="middle"><strong>Select Infrastructure:&nbsp;</strong></td>
    <td width="300">
        <select id="form-select-infraid" name="infraid" class="chzn-select" data-minimum-results-for-search="10">
            <?php foreach( $t->infras as $id => $i ): ?>
                <option value="<?= $id ?>" <?= $t->infraid != $id ?: 'selected="selected"' ?>><?= $i ?></option>
            <?php endforeach; ?>
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>Category:&nbsp;</strong></td>
    <td width="100">
        <select id="form-select-category" name="category" class="chzn-select" data-minimum-results-for-search="10">
            <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                <option value="<?= $cvalue ?>" <?= $t->category != $cvalue ?: 'selected="selected"' ?>><?= $cname ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
</table>
</form>
</p>

</div>

<?php foreach( IXP\Services\Grapher\Graph::PERIODS as $pvalue => $pname ): ?>


    <div class="col-md-6">

        <div class="well">
            <h3><?= IXP\Services\Grapher\Graph::resolvePeriod( $pvalue ) ?> Graph</h3>
            <?= $t->graph->setPeriod( $pvalue )->renderer()->boxLegacy() ?>
        </div>
    </div>

<?php endforeach; ?>

</div>

<?php $this->append() ?>



<?php $this->section( 'scripts' ) ?>

<script>

    let base_route   = "<?= route( 'statistics/infrastructure' ) ?>";
    let sel_infraid  = $("#form-select-infraid");
    let sel_category = $("#form-select-category");

    function changeGraph() {
        window.location = `${base_route}/${sel_infraid.val()}/${sel_category.val()}`;
    }

    sel_infraid.on(  'change', changeGraph );
    sel_category.on( 'change', changeGraph );

</script>

<?php $this->append() ?>
