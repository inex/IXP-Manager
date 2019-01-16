<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Virtual Interfaces / Add Interface Wizard
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class=" btn-group btn-group-sm" role="group">
        <a class="btn btn-outline-secondary" href="<?= action( 'Interfaces\VirtualInterfaceController@list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-plus"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= action( 'Interfaces\VirtualInterfaceController@wizard' )?>" >
                Add Interface Wizard...
            </a>

            <a class="dropdown-item" href="<?= action( 'Interfaces\VirtualInterfaceController@add' )?>" >
                Virtual Interface Only...
            </a>
        </ul>
    </div>

<?php $this->append() ?>

<?php $this->section('content') ?>
<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>

        <?= Former::open()->method( 'POST' )
            ->action( route( 'interfaces/virtual/wizard-save' ) )
            ->customInputWidthClass( 'col-sm-7' )
        ?>

        <div id="div-well" class="card bg-light collapse"> <?php /* collapse as we change CSS is JS and will reveal it afterwards */ ?>

            <div class="row card-body">

                <div class="col-sm-4">
                    <h3>
                        Virtual Interface Settings
                    </h3>
                    <hr>
                    <?= Former::select( 'cust' )
                        ->label( 'Customer' )
                        ->fromQuery( $t->custs, 'name' )
                        ->placeholder( 'Choose a Customer' )
                        ->addClass( 'chzn-select' )
                        ->disabled( $t->selectedCust ? true : false )
                        ->blockHelp( '' );
                    ?>

                    <?php if( $t->selectedCust ): ?>
                        <?= Former::hidden( 'cust' )
                            ->value( $t->selectedCust->getId() )
                        ?>
                    <?php endif; ?>

                    <?= Former::select( 'vlan' )
                        ->label( 'Vlan' )
                        ->fromQuery( $t->vlans, 'name' )
                        ->placeholder( 'Choose a Vlan' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( '' );
                    ?>

                    <?= Former::checkbox( 'trunk' )
                        ->label('&nbsp;')
                        ->text( 'Use 802.1q framing' )
                        ->blockHelp( 'Indicates if this port should be configured for 802.1q framing / tagged packets.' )
                        ->value( 1 )
                        ->inline()
                    ?>

                    <?= Former::checkbox( 'ipv6-enabled' )
                        ->label('&nbsp;')
                        ->text( 'IPv6 Enabled' )
                        ->blockHelp( ' ' )
                        ->value( 1 )
                        ->inline()
                    ?>

                    <?= Former::checkbox( 'ipv4-enabled' )
                        ->label('&nbsp;')
                        ->text( 'IPv4 Enabled' )
                        ->blockHelp( ' ' )
                        ->value( 1 )
                        ->inline()
                    ?>

                </div>

                <div class="col-sm-4">
                    <h3>
                        Physical Interface Settings
                    </h3>
                    <hr>
                    <div id="fanout-box" class="collapse">
                        <?= Former::checkbox( 'fanout' )
                            ->label('&nbsp;')
                            ->text( 'Associate a fanout port' )
                            ->blockHelp( ' ' )
                            ->value( 1 )
                            ->inline()
                        ?>
                    </div>
                    <?= Former::select( 'switch' )
                        ->label( 'Switch' )
                        ->fromQuery( $t->pi_switches, 'name' )
                        ->placeholder( 'Choose a switch' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( '' );
                    ?>

                    <?= Former::select( 'switch-port' )
                        ->label( 'Switch Port' )
                        ->placeholder( 'Choose a switch port' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'Shows ports that have a type of <em>Peering</em> or <em>Unknown</em> and have not been associated with any other customer / virtual interface.' );
                    ?>

                    <?= Former::hidden( 'original-switch-port')
                        ->id('original-switch-port')
                        ->forceValue( old('switch-port') )
                    ?>

                    <?= Former::select( 'status' )
                        ->label( 'Status' )
                        ->fromQuery( Entities\PhysicalInterface::$STATES , 'name' )
                        ->placeholder( 'Choose a status' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'Only virtual interfaces with at least one <em>connected</em> interface will be considered for monitoring / route server configuration, etc.' );
                    ?>

                    <?= Former::select( 'speed' )
                        ->label( 'Speed' )
                        ->fromQuery( Entities\PhysicalInterface::$SPEED , 'name' )
                        ->placeholder( 'Choose a speed' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( '' );
                    ?>

                    <?= Former::select( 'duplex' )
                        ->label( 'Duplex' )
                        ->fromQuery( Entities\PhysicalInterface::$DUPLEX , 'name' )
                        ->placeholder( 'Choose Duplex' )
                        ->addClass( 'chzn-select' )
                        ->value(1)
                        ->blockHelp( '' );
                    ?>
                </div>

                <div class="col-sm-4">
                    <h3>
                        General VLAN Settings
                    </h3>
                    <hr>
                    <?= Former::number( 'maxbgpprefix' )
                        ->label( 'Max BGP Prefixes' )
                        ->blockHelp( 'Setting this will override the overall customer setting. Leave blank to use the overall setting.' );
                    ?>

                    <?= Former::checkbox( 'rsclient' )
                        ->label('&nbsp;')
                        ->text( 'Route Server Client' )
                        ->blockHelp( 'Indicates if IXP Manager should configure route server BGP sessions for this interface.' )
                        ->value( 1 )
                        ->inline()
                    ?>

                    <?= Former::checkbox( 'irrdbfilter' )
                        ->label('&nbsp')
                        ->text( 'Apply IRRDB Filtering' )
                        ->blockHelp( "<strong>Strongly recommended!</strong> Filter routes learned on route servers based on the customer's IRRDB entries." )
                        ->value( 1 )
                        ->check( true )
                        ->inline()
                    ?>

                    <div id="div-rsmorespecifics" style="<?= old( 'irrdbfilter' ) || $t->vli && $t->vli->getIrrdbfilter() ?: 'display: none;' ?>">
                        <?= Former::checkbox( 'rsmorespecifics' )
                            ->label('&nbsp;')
                            ->text( 'IRRDB - Allow More Specifics?' )
                            ->value( 1 )
                            ->inline()
                            ->blockHelp( 'If checked, then IXP Manager will configure the route server BGP peer for this connection such that it will '
                                . 'allow more specific prefixes than those registered in the IRRDB. See the '
                                . '<a href="http://docs.ixpmanager.org/features/route-servers/">route server configuration documenation for more details</a>.' )
                        ?>
                    </div>

                    <?= Former::checkbox( 'as112client' )
                        ->label( '&nbsp;' )
                        ->text( 'AS112 Client' )
                        ->blockHelp( 'Indicates if IXP Manager should configure AS112 BGP sessions for this interface.' )
                        ->value( 1 )
                        ->inline()
                    ?>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div id='ipv6-area' class="col-sm-6" style="<?= old( 'ipv6-enabled' ) || Former::checkbox( 'ipv6-enabled')->getValue() !== null ?: 'display: none' ?>">
                <?= $t->insert( 'interfaces/common/vli/ipv6.foil.php' ) ?>
            </div>

            <div id='ipv4-area' class="col-sm-6" style="<?= old( 'ipv4-enabled' ) || Former::checkbox( 'ipv4-enabled')->getValue() !== null ?: 'display: none' ?>">
                <?= $t->insert( 'interfaces/common/vli/ipv4.foil.php' ) ?>
            </div>
        </div>


        <?=Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::secondary_link( 'Cancel' )->href( route( 'interfaces/virtual/list' ) ),
            Former::success_button( 'Help' )->id( 'help-btn' ),
            Former::info_link( 'External Documentation &Gt;' )->href( 'http://docs.ixpmanager.org/usage/interfaces/' )->target( '_blank' )->id( 'help-btn' )
        )->id('btn-group')->class( "text-center bg-light mt-4 p-4 shadow-sm");?>

        <?= Former::close() ?>

    </div>

</div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/common/js/interface-functions' ); ?>
    <?= $t->insert( 'interfaces/common/js/pi-form-logic' ); ?>
    <?= $t->insert( 'interfaces/common/js/vli-form-logic' ); ?>
    <?= $t->insert( 'interfaces/virtual/js/wizard' ); ?>
<?php $this->append() ?>

