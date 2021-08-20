<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'core-bundle@list' )?>"></a>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Core Bundles / Create Wizard
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
            <div id="message-cb" class="message"></div>

            <?= Former::open()->method( 'POST' )
                ->id( 'core-bundle-form' )
                ->action( route( 'core-bundle@store' ) )
                ->customInputWidthClass( 'col-lg-6 col-sm-6' )
                ->customLabelWidthClass( 'col-lg-4 col-md-2 col-sm-4' )
                ->actionButtonsCustomClass( "grey-box")
            ?>
                <div class="card col-sm-12">
                    <div class="card-body row">
                        <div class="col-lg-6 col-md-12 tw-mt-4">
                            <h4>
                                General Core Bundle Settings:
                            </h4>
                            <hr>
                            <?= Former::select( 'custid' )
                                ->label( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) )
                                ->fromQuery( $t->customers, 'name' )
                                ->placeholder( 'Choose a ' . config( 'ixp_fe.lang.customer.one' ) )
                                ->addClass( 'chzn-select' )
                                ->required( true )
                                ->blockHelp( "All core bundles must be associated with the internal IXP " . config( 'ixp_fe.lang.customer.one' )
                                        . ". This is the " . config( 'ixp_fe.lang.customer.one' ) . " you would have creating during installation "
                                        . "to represent your own IXP and where your superadmin users are associated."
                                    );
                            ?>

                            <?= Former::text( 'description' )
                                ->label( 'Description' )
                                ->placeholder( 'Description' )
                                ->required( true )
                                ->blockHelp( 'A short description of this core bundle to be used in lists of bundles for example.' );
                            ?>

                            <?= Former::text( 'graph_title' )
                                ->label( 'Graph Title' )
                                ->placeholder( 'Graph Title' )
                                ->required( true )
                                ->blockHelp( 'The title for graphs showing traffic on this bundle.' );
                            ?>

                            <div id="stp-div" class="collapse">
                                <?= Former::checkbox( 'stp' )
                                    ->id('stp')
                                    ->label( 'STP' )
                                    ->value( 1 )
                                    ->class( 'mx-1' )
                                    ->blockHelp( 'If spanning tree protocol (or other such as Trill) is configured on these links. Informational unless you are provisioning your switches from IXP Manager.' );
                                ?>
                            </div>

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

                            <?= Former::select( 'type' )
                                ->label( 'Type' )
                                ->fromQuery( \IXP\Models\CoreBundle::$TYPES , 'name' )
                                ->placeholder( 'Choose core bundle type' )
                                ->addClass( 'chzn-select' )
                                ->required( true )
                                ->blockHelp( 'See the documentation for an explanation of types.' )
                                ->value( \IXP\Models\CoreBundle::TYPE_ECMP );
                            ?>

                            <?= Former::checkbox( 'enabled' )
                                ->id( 'enabled' )
                                ->label( 'Enabled' )
                                ->value( 1 )
                                ->class( 'mx-1' )
                                ->blockHelp( 'Will cease graphing and other IXP Manager features. Otherwise, informational unless you are provisioning your switches from IXP Manager.' );
                            ?>
                        </div>

                        <div class="col-lg-6 col-md-12 tw-mt-4">
                            <h4>
                                Virtual Interface Settings:
                            </h4>
                            <hr>
                            <?= Former::checkbox( 'framing' )
                                ->id( 'framing' )
                                ->label( 'Use 802.1q framing' )
                                ->value( 1 )
                                ->blockHelp( "Allows you to configure the interface as an access port or a VLAN trunk. Informational unless you are provisioning your switches from IXP Manager." );
                            ?>
                            <?= Former::number( 'mtu' )
                                ->label( 'MTU' )
                                ->value( 9000 )
                                ->min( 0 )
                                ->blockHelp( 'The MTU of the interface - as defined / required by your provisioning scripts. Informational unless you are provisioning your switches from IXP Manager.' );
                            ?>

                            <div class="lag-area collapse">
                                <?= Former::checkbox( 'fast-lacp' )
                                    ->label( 'Use Fast LACP' )
                                    ->value( 1 )
                                    ->blockHelp( 'Your provisioning scripts should determine if a link aggregation protocol should be configured. This allows you to indicate fast or slow LACP. Informational unless you are provisioning your switches from IXP Manager.')
                                ?>
                            </div>

                            <div id="l3-lag-area" class="collapse">
                                <?= Former::checkbox( 'bfd' )
                                    ->label( 'BFD' )
                                    ->value( 1 )
                                    ->blockHelp( 'If the BFD protocol should be configured across the links of this bundle. Informational unless you are provisioning your switches from IXP Manager.')
                                ?>

                                <?= Former::text( 'ipv4_subnet' )
                                    ->label( 'Subnet<sup>*</sup>' )
                                    ->placeholder( '192.0.2.0/31' )
                                    ->class( "subnet" )
                                    ->blockHelp( "The IP addressing to be configured by your provisioning scripts. The 'a side' should be given the lower IP by those scripts for consistency. Informational unless you are provisioning your switches from IXP Manager.")

                                ?>
                            </div>
                        </div>

                        <div class="lag-area col-sm-12 collapse">
                            <div class="row mt-4 d-flex">
                                <div class="col-lg-6 col-md-12">
                                    <h4>Virtual Interface Side A:</h4>
                                    <hr>
                                    <?= Former::text( 'vi-name-a' )
                                        ->label( 'Name<sup>*</sup>' )
                                        ->placeholder( 'Name' )
                                        ->blockHelp( 'This is used to indicate the '
                                            . 'interface base name for LAGs. E.g. on a Cisco, this would be <code>Port-Channel</code>.<br><br>'
                                            . 'Some systems require trailing white space after the name. For this, use double-quotes which will '
                                            . 'be removed automatically. E.g. for a Force10 device, enter: <code>"Port-channel "</code>.' )
                                        ->class( 'input-lx-lag' );
                                    ?>

                                    <?= Former::number( 'vi-channel-number-a' )
                                        ->label( 'Channel Group Number<sup>*</sup>' )
                                        ->placeholder( 'Channel Group Number' )
                                        ->blockHelp( 'Used to indicate the unique LAG number / port-channel number for the interface.' )
                                        ->min( 0 )
                                        ->class( 'input-lx-lag' );
                                    ?>
                                </div>

                                <div class="col-lg-6 col-md-12 mt-4 mt-md-0">
                                    <h4>Virtual Interface Side B:</h4>
                                    <hr>
                                    <?= Former::text( 'vi-name-b' )
                                        ->label( 'Name<sup>*</sup>' )
                                        ->placeholder( 'Name' )
                                        ->blockHelp( 'This is used to indicate the '
                                            . 'interface base name for LAGs. E.g. on a Cisco, this would be <code>Port-Channel</code>.<br><br>'
                                            . 'Some systems require trailing white space after the name. For this, use double-quotes which will '
                                            . 'be removed automatically. E.g. for a Force10 device, enter: <code>"Port-channel "</code>.' )
                                        ->class( 'input-lx-lag' );
                                    ?>

                                    <?= Former::number( 'vi-channel-number-b' )
                                        ->label( 'Channel Group Number<sup>*</sup>' )
                                        ->placeholder( 'Channel Group Number' )
                                        ->blockHelp( 'Used to indicate the unique LAG number / port-channel number for the interface.' )
                                        ->min( 0 )
                                        ->class( 'input-lx-lag' );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?= $t->insert( 'interfaces/core-bundle/create/core-link/settings' ); ?>

                <div id="div-links" class="card mt-4 collapse">
                    <div class="card-header d-flex">
                        <div class="mr-auto">
                            <h3>
                                Core Links:
                            </h3>
                        </div>
                    </div>


                    <div class="card-body" id="core-links-area"></div>

                    <div class="form-group col-sm-12">
                        <div class="p-4 text-center col-lg-12">
                            <button id="add-new-core-link" type="button" class="btn btn-secondary" title="Add Core link">
                                Add another core link to the bundle...
                            </button>
                        </div>
                    </div>
                </div>

                <?= Former::actions(
                        Former::primary_submit( 'Create' )->id( 'core-bundle-submit-btn' )->class( "mb-2 mb-sm-0" ),
                        Former::secondary_link( 'Cancel' )->href( route( 'core-bundle@list' ) )->class( "mb-2 mb-sm-0" ),
                        Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                    )->id( 'btn-group-create' )
                ;?>

                <!-- insert the core link for example -->
                <?= $t->insert( 'interfaces/core-bundle/create/core-link/form' ); ?>

            <?= Former::close() ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/core-bundle/js/cb-functions' ); ?>
    <?= $t->insert( 'interfaces/core-bundle/js/create-wizard' ); ?>
<?php $this->append() ?>