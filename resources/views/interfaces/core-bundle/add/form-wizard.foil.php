<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'core-bundle@list' )?>"></a>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Core Bundles / Add Wizard
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm" role="group">
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
                ->action( route( 'core-bundle@add-store' ) )
                ->customInputWidthClass( 'col-lg-6 col-sm-6' )
                ->customLabelWidthClass( 'col-lg-4 col-md-2 col-sm-4' )
                ->actionButtonsCustomClass( "grey-box")
            ?>
                <div class="card col-sm-12">
                    <div class="card-body row">

                        <div class="col-lg-6 col-md-12">
                            <h4>
                                General Core Bundle Settings :
                            </h4>
                            <hr>
                            <?= Former::select( 'customer' )
                                ->label( 'Customer' )
                                ->fromQuery( $t->customers, 'name' )
                                ->placeholder( 'Choose a customer' )
                                ->addClass( 'chzn-select' )
                                ->required( true )
                                ->blockHelp( '' );
                            ?>

                            <?= Former::text( 'description' )
                                ->label( 'Description' )
                                ->placeholder( 'Description' )
                                ->required( true )
                                ->blockHelp( 'help text' );
                            ?>

                            <?= Former::text( 'graph-title' )
                                ->label( 'Graph Title' )
                                ->placeholder( 'Graph Title' )
                                ->required( true )
                                ->blockHelp( 'help text' );
                            ?>

                            <div id="stp-div" class="collapse">
                                <?= Former::checkbox( 'stp' )
                                    ->id('stp')
                                    ->label( 'STP' )
                                    ->value( 1 )
                                    ->blockHelp( "" );
                                ?>
                            </div>

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

                            <?= Former::select( 'type' )
                                ->label( 'Type' )
                                ->fromQuery( Entities\CoreBundle::$TYPES , 'name' )
                                ->placeholder( 'Choose Core Bundle type' )
                                ->addClass( 'chzn-select' )
                                ->required( true )
                                ->blockHelp( '' )
                                ->value( Entities\CoreBundle::TYPE_ECMP );
                            ?>

                            <?= Former::checkbox( 'enabled' )
                                ->id( 'enabled' )
                                ->label( 'Enabled' )
                                ->value( 1 )
                                ->blockHelp( "" );
                            ?>
                        </div>

                        <div class="col-lg-6 col-md-12 mt-4 mt-lg-0">
                            <h4>
                                Virtual Interface Settings:
                            </h4>
                            <hr>
                            <?= Former::checkbox( 'framing' )
                                ->id( 'framing' )
                                ->label( 'Use 802.1q framing' )
                                ->value( 1 )
                                ->blockHelp( "" );
                            ?>
                            <?= Former::number( 'mtu' )
                                ->label( 'MTU' )
                                ->value( 9000 )
                                ->min( 0 )
                                ->blockHelp( '' );
                            ?>

                            <div class="lag-area collapse">
                                <?= Former::checkbox( 'fast-lacp' )
                                    ->label( 'Use Fast LACP' )
                                    ->value( 1 )
                                ?>
                            </div>

                            <div id="l3-lag-area" class="collapse">
                                <?= Former::checkbox( 'bfd' )
                                    ->label( 'BFD' )
                                    ->value( 1 )
                                ?>

                                <?= Former::text( 'subnet' )
                                    ->label( 'SubNet<sup>*</sup>' )
                                    ->placeholder( '192.0.2.0/30' )
                                    ->class( "subnet" )
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
                                        ->blockHelp( 'help text' )
                                        ->class( 'input-lx-lag' );
                                    ?>

                                    <?= Former::number( 'vi-channel-number-a' )
                                        ->label( 'Channel Group Number<sup>*</sup>' )
                                        ->placeholder( 'Channel Group Number' )
                                        ->blockHelp( 'help text' )
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
                                        ->blockHelp( 'help text' )
                                        ->class( 'input-lx-lag' );
                                    ?>

                                    <?= Former::number( 'vi-channel-number-b' )
                                        ->label( 'Channel Group Number<sup>*</sup>' )
                                        ->placeholder( 'Channel Group Number' )
                                        ->blockHelp( 'help text' )
                                        ->min( 0 )
                                        ->class( 'input-lx-lag' );
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <?= $t->insert( 'interfaces/core-bundle/add/core-link/settings' ); ?>

                <div id="div-links" class="card mt-4 collapse">

                    <div class="card-header d-flex">
                        <div class="mr-auto">
                            <h3>
                                Core Links :
                            </h3>
                        </div>

                    </div>

                    <div class="card-body" id="core-links-area">

                    </div>

                    <div class="form-group col-sm-12">
                        <div class="p-4 text-center col-lg-12">
                            <button id="add-new-core-link" type="button" class="btn btn-primary" title="Add Core link">Add Core Link</button>
                        </div>
                    </div>
                </div>

                <?= Former::actions(
                        Former::primary_submit( 'Add' )->id( 'core-bundle-submit-btn' )->class( "mb-2 mb-sm-0" ),
                        Former::secondary_link( 'Cancel' )->href( route( 'core-bundle@list' ) )->class( "mb-2 mb-sm-0" ),
                        Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                    )->id('btn-group-add')
                ;?>

                <!-- insert the core link for example -->
                <?= $t->insert( 'interfaces/core-bundle/add/core-link/form' ); ?>

            <?= Former::close() ?>
        </div>
    </div>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/common/js/cb-functions' ); ?>
    <?= $t->insert( 'interfaces/core-bundle/js/add-wizard' ); ?>
<?php $this->append() ?>