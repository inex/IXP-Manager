<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Core Bundles / Edit
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/core-bundles/">
            Documentation
        </a>
        <a class="btn btn-white" href="<?= route( 'core-bundle@list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
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
                                General Core Bundle Settings:
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
                            ->blockHelp( "All core bundles must be associated with the internal IXP " . config( 'ixp_fe.lang.customer.one' )
                                . ". This is the " . config( 'ixp_fe.lang.customer.one' ) . " you would have creating during installation "
                                . "to represent your own IXP and where your superadmin users are associated."
                            );
                        ?>

                        <?= Former::text( 'description' )
                            ->label( 'Description' )
                            ->placeholder( 'Description' )
                            ->blockHelp( 'A short description of this core bundle to be used in lists of bundles for example.' );
                        ?>

                        <?= Former::text( 'graph_title' )
                            ->label( 'Graph Title' )
                            ->placeholder( 'Graph Title' )
                            ->blockHelp( 'The title for graphs showing traffic on this bundle.' );
                        ?>

                        <?= Former::select( 'type' )
                            ->label( 'Type<sup>*</sup>' )
                            ->fromQuery( \IXP\Models\CoreBundle::$TYPES , 'name' )
                            ->placeholder( 'Choose Core Bundle type' )
                            ->addClass( 'chzn-select' )
                            ->blockHelp( 'See the documentation for an explanation of types.' )
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
                                ->blockHelp( 'If spanning tree protocol (or other such as Trill) is configured on these links. Informational unless you are provisioning your switches from IXP Manager.' );
                            ?>
                        <?php endif; ?>

                        <?= Former::number( 'cost' )
                            ->label( 'Cost' )
                            ->placeholder( '10' )
                            ->min( 0 )
                            ->blockHelp( 'Cost for dynamic protocols as required for your provisioning configuration. E.g. can be the cost for STP, a metric for BGP, etc. Informational unless you are provisioning your switches from IXP Manager.' );
                        ?>

                        <?= Former::number( 'preference' )
                            ->label( 'Preference' )
                            ->placeholder( '10' )
                            ->min( 0 )
                            ->blockHelp( 'Preference for dynamic protocols as required for your provisioning configuration. Informational unless you are provisioning your switches from IXP Manager.' );
                        ?>

                        <?= Former::checkbox( 'enabled' )
                            ->id( 'enabled' )
                            ->label( 'Enabled' )
                            ->value( 1 )
                            ->class( 'mx-1' )
                            ->blockHelp( 'Will cease graphing and other IXP Manager features. Otherwise, informational unless you are provisioning your switches from IXP Manager.' );
                        ?>

                        <?php if( $t->cb->typeL3LAG() ): ?>
                            <?= Former::checkbox( 'bfd' )
                                ->label( 'BFD' )
                                ->value( 1 )
                                ->inline()
                                ->blockHelp( 'If the BFD protocol should be configured across the links of this bundle. Informational unless you are provisioning your switches from IXP Manager.')
                            ?>

                            <?= Former::text( 'ipv4_subnet' )
                                ->label( 'Subnet' )
                                ->placeholder( '192.0.2.0/31' )
                                ->blockHelp( "" )
                                ->blockHelp( "The IP addressing to be configured by your provisioning scripts. The 'a side' should be given the lower IP by those scripts for consistency. Informational unless you are provisioning your switches from IXP Manager.")
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
            <div class="alert alert-warning tw-mt-8" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div class="col-sm-12 d-flex">
                        <b class="mr-auto my-auto">
                            Do you want to delete this core bundle?
                        </b>
                        <a class="btn btn-warning mr-4 btn-delete-cb" id="btn-delete-cb" href="<?= route( 'core-bundle@delete', [ 'cb' => $t->cb->id ] ) ?>"  title="Delete">
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