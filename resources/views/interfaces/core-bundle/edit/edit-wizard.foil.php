<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'headers' ) ?>
    <style>
        #table-core-link tr td{
            vertical-align: middle;
        }
    </style>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Core Bundles / Edit
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'core-bundle@list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-plus"></i> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= route( 'core-bundle@add-wizard' )?>" >
                Add Core Bundle Wizard...
            </a>

        </ul>
    </div>

<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <div id="message-cb"></div>

            <div class="card">
                <div class="card-body">
                    <?= Former::open()->method( 'POST' )
                        ->id( 'core-bundle-form' )
                        ->action( route ( 'core-bundle@edit-store' ) )
                        ->customInputWidthClass( 'col-lg-8 col-md-6 col-sm-6' )
                        ->customLabelWidthClass( 'col-lg-4 col-md-3 col-sm-4' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>

                        <h3>
                            General Core Bundle Settings :
                        </h3>
                        <hr>
                        <div class="col-lg-6 col-sm-12">

                            <?= Former::select( 'customer' )
                                ->label( 'Customer' )
                                ->fromQuery( $t->customers, 'name' )
                                ->placeholder( 'Choose a customer' )
                                ->addClass( 'chzn-select' )
                                ->blockHelp( '' );
                            ?>

                            <?= Former::text( 'description' )
                                ->label( 'Description' )
                                ->placeholder( 'Description' )
                                ->blockHelp( 'help text' );
                            ?>

                            <?= Former::text( 'graph-title' )
                                ->label( 'Graph Title' )
                                ->placeholder( 'Graph Title' )
                                ->blockHelp( 'help text' );
                            ?>

                            <?= Former::select( 'type' )
                                ->label( 'Type<sup>*</sup>' )
                                ->fromQuery( Entities\CoreBundle::$TYPES , 'name' )
                                ->placeholder( 'Choose Core Bundle type' )
                                ->addClass( 'chzn-select' )
                                ->blockHelp( '' )
                                ->value( Entities\CoreBundle::TYPE_ECMP )
                                ->disabled( true );
                            ?>

                        </div>

                        <div class="col-lg-6 col-sm-12">

                            <?php if( $t->cb->isL2LAG() ): ?>
                                <?= Former::checkbox( 'stp' )
                                    ->id('stp')
                                    ->label( 'STP' )
                                    ->value( 1 )
                                    ->inline()
                                    ->blockHelp( "" );
                                ?>
                            <?php endif; ?>

                            <?= Former::number( 'cost' )
                                ->label( 'Cost' )
                                ->placeholder( '10' )
                                ->min( 0 )
                                ->blockHelp( 'help text' );
                            ?>

                            <?= Former::number( 'preference' )
                                ->label( 'Preference' )
                                ->placeholder( '10' )
                                ->min( 0 )
                                ->blockHelp( 'help text' );
                            ?>

                            <?= Former::checkbox( 'enabled' )
                                ->id( 'enabled' )
                                ->label( 'Enabled' )
                                ->value( 1 )
                                ->blockHelp( "" );
                            ?>

                            <?php if( $t->cb->isL3LAG() ): ?>

                                <?= Former::checkbox( 'bfd' )
                                    ->label( 'BFD' )
                                    ->value( 1 )
                                    ->inline()
                                    ->blockHelp( "" );
                                ?>

                                <?= Former::text( 'subnet' )
                                    ->label( 'SubNet' )
                                    ->placeholder( '192.0.2.0/30' )
                                    ->blockHelp( "" )
                                    ->class( "subnet" );
                                ?>
                            <?php endif; ?>

                            <?= Former::hidden( 'type' )
                                ->id( 'type')
                                ->value( $t->cb->getType() );
                            ?>

                            <?= Former::hidden( 'cb' )
                                ->id( 'cb')
                                ->value( $t->cb->getId() )
                            ?>

                        </div>

                        <?=Former::actions(
                            Former::primary_submit( 'Save Changes' )->id( 'core-bundle-submit-btn' ),
                            Former::secondary_link( 'Cancel' )->href( route( 'core-bundle@list' ) ),
                            Former::success_button( 'Help' )->id( 'help-btn' )
                        )?>


                    <?= Former::close() ?>

                </div>
            </div>

            <?= $t->insert( 'interfaces/core-bundle/edit/virtual-interfaces' ); ?>

            <?= $t->insert( 'interfaces/core-bundle/edit/core-link/list' ); ?>

            <?= $t->insert( 'interfaces/core-bundle/edit/core-link/form' ); ?>

            <!-- Delete Core Bundle area -->
            <div class="alert alert-danger mt-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="col-sm-12 d-flex">
                        <b class="mr-auto my-auto">
                            If you are sure you want to delete this Core Bundle:
                        </b>
                        <a class="btn btn-danger mr-4" id="cb-delete-<?= $t->cb->getId() ?>" href="#" title="Delete">
                            Delete
                        </a>

                    </div>
                </div>
            </div>

        </div>

    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/core-bundle/js/edit-wizard' ); ?>
    <?= $t->insert( 'interfaces/common/js/cb-functions' ); ?>
<?php $this->append() ?>