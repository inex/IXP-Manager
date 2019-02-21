<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'headers' ) ?>
    <style>
        .checkbox input[type=checkbox]{
            margin-left: 0px;
        }

        .col-lg-offset-2{
            margin-left: 0px;
        }

        .checkbox{
            text-align: center;
        }

        #table-core-link tr td{
            vertical-align: middle;
        }

        #btn-group-add div{
            text-align : center;
        }
    </style>
<?php $this->append() ?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'core-bundle/list' )?>"></a>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Core Bundles / Add Wizard</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-outline-secondary" href="<?= route( 'core-bundle/list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
    </div>

<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <?= Former::open()->method( 'POST' )
                ->id( 'core-bundle-form' )
                ->action( route( 'core-bundle/store' ) )
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

                            <div id="stp-div">
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
                            <div class="lag-area" style="display: none" >
                                <?= Former::checkbox( 'fast-lacp' )
                                    ->label( 'Use Fast LACP' )
                                    ->value( 1 )
                                ?>
                            </div>
                            <div id="l3-lag-area" style="display: none">
                                <?= Former::checkbox( 'bfd' )
                                    ->label( 'BFD' )
                                    ->value( 1 )
                                ?>

                                <?= Former::text( 'subnet' )
                                    ->label( 'SubNet<sup>*</sup>' )
                                    ->placeholder( '192.0.2.0/30' )
                                ?>
                            </div>
                        </div>

                        <div class="lag-area row col-sm-12 mt-4" style="display: none" >

                            <div class="col-lg-6 col-md-12">
                                <h4>Side A:</h4>
                                <hr>
                                <?= Former::text( 'vi-name-a' )
                                    ->label( 'Name<sup>*</sup>' )
                                    ->placeholder( 'Name' )
                                    ->blockHelp( 'help text' );
                                ?>

                                <?= Former::number( 'vi-channel-number-a' )
                                    ->label( 'Channel Group Number<sup>*</sup>' )
                                    ->placeholder( 'Channel Group Number' )
                                    ->blockHelp( 'help text' )
                                    ->min( 0 );
                                ?>
                            </div>

                            <div class="col-lg-6 col-md-12 mt-4 mt-md-0">
                                <h4>Side B:</h4>
                                <hr>
                                <?= Former::text( 'vi-name-b' )
                                    ->label( 'Name<sup>*</sup>' )
                                    ->placeholder( 'Name' )
                                    ->blockHelp( 'help text' );
                                ?>

                                <?= Former::number( 'vi-channel-number-b' )
                                    ->label( 'Channel Group Number<sup>*</sup>' )
                                    ->placeholder( 'Channel Group Number' )
                                    ->blockHelp( 'help text' )
                                    ->min( 0 );
                                ?>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="card mt-4">
                    <div class="card-body">
                        <h4>
                            Common Link Settings :
                        </h4>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6 col-md-12">

                                <?= Former::select( 'switch-a' )
                                    ->id( 'switch-a' )
                                    ->label( 'Switch A' )
                                    ->fromQuery( $t->switches, 'name' )
                                    ->required( true )
                                    ->placeholder( 'Choose a switch' )
                                    ->addClass( 'chzn-select' )
                                ?>

                                <?= Former::select( 'speed' )
                                    ->label( 'Speed' )
                                    ->id( 'speed' )
                                    ->fromQuery( Entities\PhysicalInterface::$SPEED, 'name' )
                                    ->required( true )
                                    ->placeholder( 'Choose a Speed' )
                                    ->addClass( 'chzn-select' )

                                ?>

                                <?= Former::checkbox( 'auto-neg' )
                                    ->label( 'Auto-Neg' )
                                    ->value( 1 )
                                    ->check()
                                ?>

                            </div>

                            <div class="col-lg-6 col-md-12">

                                <?= Former::select( 'switch-b' )
                                    ->id( 'switch-b' )
                                    ->label( 'Switch B' )
                                    ->required( true )
                                    ->placeholder( 'Choose a switch' )
                                    ->addClass( 'chzn-select' )
                                ?>

                                <?= Former::select( 'duplex' )
                                    ->id( 'duplex' )
                                    ->label( 'Duplex' )
                                    ->fromQuery( Entities\PhysicalInterface::$DUPLEX, 'name' )
                                    ->placeholder( 'Choose a duplex' )
                                    ->required( true )
                                    ->select( 'full' )
                                    ->addClass( 'chzn-select' )
                                ?>

                            </div>

                        </div>

                        <div class="card former-help-text">
                            <div class="card-body bg-light">
                                You have a number of options when assigning a port:

                                <ul>
                                    <li>
                                        If you have pre-wired the patch panel to a port, enter the switch and port here. So long as no customer has been
                                        assigned to the switch port, the patch panel port will remain available but will be marked as connected to
                                        the given switch port in the patch panel port list.
                                    </li>
                                </ul>

                                If you need to reset these fields, just click either of the <em>Reset</em> button.
                            </div>
                        </div>
                    </div>
                </div>



                <div id="div-links" class="card mt-4" style="display: none">

                    <div class="card-header d-flex">
                        <div class="mr-auto">
                            <h3>
                                Core Links :
                            </h3>
                        </div>

                        <div clas="my-auto">
                            <button style="float: right; margin-right: 20px" id="add-new-core-link" type="button" class=" btn-sm btn btn-outline-secondary" href="#" title="Add Core link">
                                <span class="fa fa-plus"></span>
                            </button>
                        </div>

                    </div>

                    <div class="card-body" id="core-links-area">

                    </div>

                </div>

                <?= Former::hidden( 'nb-core-links' )
                    ->id( 'nb-core-links')
                    ->value( 0 )
                ?>

                <?=Former::actions(
                    Former::primary_submit( 'Save Changes' )->id( 'core-bundle-submit-btn' )->class( "mb-2 mb-sm-0" ),
                    Former::secondary_link( 'Cancel' )->href( route( 'core-bundle/list' ) )->class( "mb-2 mb-sm-0" ),
                    Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                )->id('btn-group-add')
                ;?>

            <?= Former::close() ?>
        </div>
    </div>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script type="text/javascript" src="<?= asset( '/bower_components/ip-address/dist/ip-address-globals.js' ) ?>"></script>
    <?= $t->insert( 'interfaces/common/js/cb-functions' ); ?>
    <?= $t->insert( 'interfaces/core-bundle/js/add-wizard' ); ?>
<?php $this->append() ?>