<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    Home /  Statistics /  Graphs ( Bits / Day )
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Home /  Statistics /  Graphs ( Bits / Day )
    </li>

    <span class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" id="add-l2a">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </span>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <div class="row-fluid">

        <?php
            foreach( $t->graphs as $graph ): ?>

        <div class="col-md-12 col-lg-4">

            <div class="well">
                <h4 style="vertical-align: middle">
                    <?= $graph->customer()->getFormattedName() ?>
                    <?php if( config('grapher.backends.sflow.enabled') /* && ( $category == 'bits' || $category == 'pkts' ) */ ): ?>
                        <span class="btn btn-mini" style="float: right">
                            <?php /* {genUrl controller="statistics" action="p2p" shortname=$graph->customer()->getShortname() category=$category period=$period} */ ?>
                            <a href="#"><i class="icon-random"></i></a>
                        </span>
                    <?php endif; ?>
                </h4>

                <p>
                    <br />
                    <?= $graph->renderer()->boxLegacy() ?>
                </p>
            </div>

        </div>

    <?php endforeach; ?>

</div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<?php $this->append() ?>