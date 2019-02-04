<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Move Patch Panel Port - <?= $t->ee( $t->ppp->getPatchPanel()->getName() ) ?> :: <?= $t->ee( $t->ppp->getName() )?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-body">
                    <?= Former::open()->method( 'POST' )
                        ->action( route ( 'patch-panel-port@move' ) )
                        ->customInputWidthClass( 'col-sm-3' )
                    ?>

                    <?= Former::text( 'current-pos' )
                        ->label( 'Current position :' )
                        ->value( $t->ee( $t->ppp->getPatchPanel()->getName() ) . ' :: ' . $t->ee( $t->ppp->getName() ) )
                        ->blockHelp( 'The current patch panel and port.' )
                        ->disabled( true );
                    ?>

                    <?= Former::select( 'pp' )
                        ->label( 'New Patch Panel:' )
                        ->placeholder( 'Choose a Patch Panel' )
                        ->options( $t->ppAvailable )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The new patch panel to move this port to.' );
                    ?>

                    <?= Former::select( 'master-port' )
                        ->label( 'New Port:' )
                        ->placeholder( 'Choose a port' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The new port to move to.' );
                    ?>

                    <?php if( $t->ppp->hasSlavePort() ): ?>
                        <?= Former::select( 'slave-port' )
                            ->label( 'New Slave/Duplex Port:' )
                            ->placeholder( 'Choose a Duplex port' )
                            ->addClass( 'chzn-select' )
                            ->blockHelp( 'The original port is a duplex port so you must also chose the slave/partner/duplex port here.' );
                        ?>
                    <?php endif; ?>

                    <?= Former::hidden( 'id' )
                        ->value( $t->ppp->getId() )
                    ?>

                    <?= Former::hidden( 'has-duplex' )
                        ->value( $t->ppp->hasSlavePort() ? true : false )
                    ?>

                    <?=Former::actions(
                        Former::primary_submit( 'Save Changes' ),
                        Former::secondary_link( 'Cancel' )->href( route ( 'patch-panel-port/list/patch-panel' , [ 'id' => $t->ppp->getPatchPanel()->getId() ] ) ),
                        Former::success_button( 'Help' )->id( 'help-btn' )
                    )->id('btn-group')->class( "bg-light p-4 d-4 shadow-sm text-center" )?>

                    <?= Former::close() ?>
                </div>

            </div>

        </div>

    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <?= $t->insert( 'patch-panel-port/js/move' ); ?>
<?php $this->append() ?>