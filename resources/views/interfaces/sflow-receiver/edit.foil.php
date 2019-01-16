<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Sflow Receivers / <?= $t->sflr ? 'Edit' : 'Add' ?> Sflow Receiver
<?php $this->append() ?>



<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-outline-secondary" href="<?= route( 'interfaces/sflow-receiver/list' ) ?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
    </div>
<?php $this->append() ?>


<?php $this->section('content') ?>

    <div class="row">

        <div class="col-md-12">

            <?= $t->alerts() ?>

            <?= Former::open()->method( 'post' )
                ->action( route( 'sflow-receiver@store' ) )
                ->customInputWidthClass( 'col-sm-4' )
            ?>

                <?= Former::text( 'dst_ip' )
                    ->label( 'Destination IP' )
                    ->blockHelp( 'help text' );
                ?>

                <?= Former::number( 'dst_port' )
                    ->label( 'Destination Port' )
                    ->blockHelp( 'help text' );
                ?>

                <?= Former::hidden( 'id' )
                    ->value( $t->sflr ? $t->sflr->getId() : null )
                ?>

                <?= Former::hidden( 'viid' )
                    ->value( $t->sflr ? $t->sflr->getVirtualInterface()->getId() : $t->vi->getId() )
                ?>

                <?=Former::actions(
                    Former::primary_submit( $t->sflr ? 'Save Changes' : 'Add' ),
                    Former::secondary_link( 'Cancel' )->href( $t->vi ? route(  'interfaces/virtual/edit' , [ 'id' => $t->vi->getId() ] ) :  route( 'interfaces/sflow-receiver/list' ) ),
                    Former::success_button( 'Help' )->id( 'help-btn' )
                )->id('btn-group')->class( "bg-light p-4 mt-4 shadow-sm text-center" );?>

                <?= Former::close() ?>


            <div class="card mt-4 bg-light">
                <div class="card-body">
                    <h3>Sflow Receivers / Exporting Sflow Telemetry</h3>

                    <p>
                        This feature allows you to export sflow telemetry to IXP participants using PMacct. Please see
                        <a href="https://www.ixpmanager.org/media/2016/201610-ripe73-inex-nh-exporting-sflow.pdf">these slides</a>
                        and <a href="https://ripe73.ripe.net/archives/video/1458/">this video</a> from Nick Hilliard at RIPE73
                        for more details.
                    </p>
                </div>
            </div>

        </div>

    </div>

<?php $this->append() ?>
