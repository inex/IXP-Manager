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
    <a href="<?= action( 'Interfaces\CoreBundleController@list' )?>">Core Bundles</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Add Core Bundles Wizard</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class=" btn-group btn-group-xs" role="group">

        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?= $t->alerts() ?>
    <?= Former::open()->method( 'POST' )
        ->id( 'core-bundle-form' )
        ->action( action ( 'Interfaces\CoreBundleController@storeWizard' ) )
        ->customWidthClass( 'col-sm-6' )
    ?>
        <div class="well col-sm-12">
            <div class="col-sm-6">
                <h3>
                    General Core Bundle Settings :
                </h3>
                <hr>
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

                <div id="stp-div">
                    <?= Former::checkbox( 'stp' )
                        ->id('stp')
                        ->label( 'STP' )
                        ->checked_value( 1 )
                        ->unchecked_value( 0 )
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
                    ->label( 'Type<sup>*</sup>' )
                    ->fromQuery( $t->types, 'name' )
                    ->placeholder( 'Choose Core Bundle type' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' )
                    ->value( Entities\CoreBundle::TYPE_ECMP );
                ?>

                <?= Former::checkbox( 'enabled' )
                    ->id( 'enabled' )
                    ->label( 'Enabled' )
                    ->unchecked_value( 0 )
                    ->blockHelp( "" );
                ?>
            </div>
            <div class="col-sm-6">
                <h3>
                    Virtual Interface Settings:
                </h3>
                <hr>
                <?= Former::checkbox( 'framing' )
                    ->id( 'framing' )
                    ->label( 'Use 802.1q framing' )
                    ->unchecked_value( 0 )
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
                        ->unchecked_value( 0 )
                        ->value( 1 )
                    ?>
                </div>
                <div id="l3-lag-area" style="display: none">
                    <?= Former::checkbox( 'bfd' )
                        ->label( 'BFD' )
                        ->unchecked_value( 0 )
                        ->value( 1 )
                    ?>

                    <?= Former::text( 'subnet' )
                        ->label( 'SubNet' )
                        ->placeholder( '192.0.2.0/30' )
                    ?>
                </div>
            </div>
            <div class="lag-area col-sm-12" style="display: none" >
                <div class="col-sm-6">
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

                <div class="col-sm-6">
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
        <br/>
        <div style="clear: both"></div>
        <div class="well col-sm-12">
            <div class="col-sm-12">
                <h4>Common Link Settings :</h4>
                <hr>
            </div>

            <div class="col-sm-6">
                <?= Former::select( 'switch-a' )
                    ->id( 'switch-a' )
                    ->label( 'Switch A<sup>*</sup> :' )
                    ->fromQuery( $t->switches, 'name' )
                    ->placeholder( 'Choose a switch' )
                    ->addClass( 'chzn-select' )
                ?>
            </div>

            <div class="col-sm-6">
                <?= Former::select( 'switch-b' )
                    ->id( 'switch-b' )
                    ->label( 'Switch B<sup>*</sup> :' )
                    ->placeholder( 'Choose a switch' )
                    ->addClass( 'chzn-select' )
                ?>
            </div>
            <br/>
            <div style="clear: both"></div>
            <br/>
            <div class="col-sm-6">
                <?= Former::select( 'speed' )
                    ->label( 'Speed<sup>*</sup> :' )
                    ->id( 'speed' )
                    ->fromQuery( $t->speed, 'name' )
                    ->placeholder( 'Choose a Speed' )
                    ->addClass( 'chzn-select' )

                ?>
            </div>
            <div class="col-sm-6">
                <?= Former::select( 'duplex' )
                    ->id( 'duplex' )
                    ->label( 'Duplex<sup>*</sup> :' )
                    ->fromQuery( $t->duplex, 'name' )
                    ->placeholder( 'Choose a duplex' )
                    ->select( 'full' )
                    ->addClass( 'chzn-select' )
                ?>
            </div>
            <br/>
            <div style="clear: both"></div>
            <br/>
            <div class="col-sm-6">
                <?= Former::checkbox( 'auto-neg' )
                    ->label( 'Auto-Neg :' )
                    ->unchecked_value( 0 )
                    ->value( 1 )
                    ->check( true )
                    ->style('margin-left : 50%' )
                ?>
            </div>
            <div class="well help-block">
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
        <div style="clear: both"></div>
        <div id="div-links" style="display: none">
            <h3>
                Core Links :

                <button style="float: right; margin-right: 20px" id="add-new-core-link" type="button" class=" btn-xs btn btn-default" href="#" title="Add Core link">
                    <span class="glyphicon glyphicon-plus"></span>
                </button>

            </h3>
            <div class="col-sm-12" id="core-links-area">

            </div>

        </div>

        <?= Former::hidden( 'nb-core-links' )
            ->id( 'nb-core-links')
            ->value( 0 )
        ?>

        <?=Former::actions(
            Former::primary_submit( 'Save Changes' )->id( 'core-bundle-submit-btn' ),
            Former::default_link( 'Cancel' )->href( action( 'Interfaces\CoreBundleController@list' ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->id('btn-group-add')
        ;?>

    <?= Former::close() ?>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script type="text/javascript" src="<?= asset( '/bower_components/ip-address/dist/ip-address-globals.js' ) ?>"></script>
    <?= $t->insert( 'interfaces/core-bundle/js/add-wizard' ); ?>
    <script>
        $(document).ready( function() {
            $( 'label.col-lg-2' ).removeClass('col-lg-2');

            actionRunnig = false;
            nbCoreLink = 0;

            // array of switch port selected
            exludedSwitchPort = [];

            if( $( "#type" ).val() ){
                displayCoreLinks();
            }

            $( "#speed").chosen();
            $( "#duplex").chosen();
        });
    </script>
<?php $this->append() ?>