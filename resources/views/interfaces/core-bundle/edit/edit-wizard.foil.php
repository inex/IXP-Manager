<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Core Bundles / Edit
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'core-bundle@list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-plus"></i> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= route( 'core-bundle@create-wizard' )?>" >
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
                    <?= Former::open()->method( 'PUT' )
                        ->id( 'core-bundle-form' )
                        ->action( route ( 'core-bundle@update', [ 'cb' => $t->cb->id ] ) )
                        ->customInputWidthClass( 'col-lg-8 col-md-6 col-sm-6' )
                        ->customLabelWidthClass( 'col-lg-4 col-md-3 col-sm-4' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>
                    <div class="d-flex">
                        <div class="mr-auto">
                            <h3>
                                General Core Bundle Settings :
                            </h3>
                        </div>

                        <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\CoreBundle::class, 'logSubject') ): ?>
                            <div class="">
                                <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'CoreBundle' , 'model_id' => $t->cb->id ] ) ?>">
                                    View logs
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <hr>
                    <div class="col-lg-6 col-sm-12">
                        <?= Former::select( 'custid' )
                            ->label( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) )
                            ->fromQuery( $t->customers, 'name' )
                            ->placeholder( 'Choose a ' . config( 'ixp_fe.lang.customer.one' ) )
                            ->addClass( 'chzn-select' )
                            ->blockHelp( '' );
                        ?>

                        <?= Former::text( 'description' )
                            ->label( 'Description' )
                            ->placeholder( 'Description' )
                            ->blockHelp( 'help text' );
                        ?>

                        <?= Former::text( 'graph_title' )
                            ->label( 'Graph Title' )
                            ->placeholder( 'Graph Title' )
                            ->blockHelp( 'help text' );
                        ?>

                        <?= Former::select( 'type' )
                            ->label( 'Type<sup>*</sup>' )
                            ->fromQuery( \IXP\Models\CoreBundle::$TYPES , 'name' )
                            ->placeholder( 'Choose Core Bundle type' )
                            ->addClass( 'chzn-select' )
                            ->blockHelp( '' )
                            ->value( \IXP\Models\CoreBundle::TYPE_ECMP )
                            ->disabled( true );
                        ?>
                    </div>

                    <div class="col-lg-6 col-sm-12">
                        <?php if( $t->cb->typeL2LAG() ): ?>
                            <?= Former::checkbox( 'stp' )
                                ->id('stp')
                                ->label( 'STP' )
                                ->value( 1 )
                                ->inline()
                                ->class( 'mx-1' )
                                ->blockHelp( '' );
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
                            ->class( 'mx-1' )
                            ->blockHelp( '' );
                        ?>

                        <?php if( $t->cb->typeL3LAG() ): ?>
                            <?= Former::checkbox( 'bfd' )
                                ->label( 'BFD' )
                                ->value( 1 )
                                ->inline()
                                ->blockHelp( "" );
                            ?>

                            <?= Former::text( 'ipv4_subnet' )
                                ->label( 'SubNet' )
                                ->placeholder( '192.0.2.0/30' )
                                ->blockHelp( "" )
                                ->class( "subnet" );
                            ?>
                        <?php endif; ?>

                        <?= Former::hidden( 'type' )
                            ->id( 'type')
                            ->value( $t->cb->type );
                        ?>

                        <?= Former::hidden( 'cb' )
                            ->id( 'cb')
                            ->value( $t->cb->id )
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
                        <a class="btn btn-danger mr-4 btn-delete-cb" id="btn-delete-cb" href="<?= route( 'core-bundle@delete', [ 'cb' => $t->cb->id ] ) ?>"  title="Delete">
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
    <?= $t->insert( 'interfaces/core-bundle/js/cb-functions' ); ?>
<?php $this->append() ?>