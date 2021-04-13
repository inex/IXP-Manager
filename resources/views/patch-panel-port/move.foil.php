<?php
    /** @var Foil\Template\Template $t */
    $ppp = $t->ppp; /** @var $ppp \IXP\Models\PatchPanelPort */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Move Patch Panel Port - <?= $t->ee( $ppp->patchPanel->name ) ?> :: <?= $t->ee( $ppp->name() )?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <div class="card">
                <div class="card-body">
                    <?= Former::open()->method( 'PUT' )
                        ->action( route ( 'patch-panel-port@move', [ 'ppp'=> $ppp->id ] ) )
                        ->customInputWidthClass( 'col-lg-4 col-sm-6' )
                        ->customLabelWidthClass( 'col-lg-3 col-sm-4' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>

                    <?= Former::text( 'name' )
                        ->label( 'Current position' )
                        ->value( $t->ee( $ppp->patchPanel->name ) . ' :: ' . $t->ee( $ppp->name() ) )
                        ->blockHelp( 'The current patch panel and port.' )
                        ->disabled( true );
                    ?>

                    <?= Former::select( 'patch_panel_id' )
                        ->label( 'New Patch Panel' )
                        ->placeholder( 'Choose a Patch Panel' )
                        ->fromQuery( $t->ppAvailable, 'name' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The new patch panel to move this port to.' );
                    ?>

                    <?= Former::select( 'port_id' )
                        ->label( 'New Port' )
                        ->placeholder( 'Choose a port' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The new port to move to.' );
                    ?>

                    <?php if( $ppp->duplexSlavePorts()->exists() ): ?>
                        <div id="area_slave" class="collapse">
                            <?= Former::select( 'slave_id' )
                                ->label( 'New Slave/Duplex Port' )
                                ->placeholder( 'Choose a Duplex port' )
                                ->addClass( 'chzn-select' )
                                ->blockHelp( 'The original port is a duplex port so you must also choose the slave/partner/duplex port here.' );
                            ?>
                        </div>
                    <?php endif; ?>

                    <?= Former::hidden( 'id' )
                        ->value( $ppp->id )
                    ?>

                    <?= Former::hidden( 'has_duplex' )
                        ->value( $ppp->duplexSlavePorts()->exists() )
                    ?>

                    <?=Former::actions(
                        Former::primary_submit( 'Save Changes' )->class( "mb-2 mb-sm-0" ),
                        Former::secondary_link( 'Cancel' )->href( route ( 'patch-panel-port@list-for-patch-panel' , [ 'pp' => $ppp->patch_panel_id ] ) )->class( "mb-2 mb-sm-0" ),
                        Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                    )->id('btn-group')?>

                    <?= Former::close() ?>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <?= $t->insert( 'patch-panel-port/js/move' ); ?>
<?php $this->append() ?>