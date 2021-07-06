<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Virtual Interfaces / Create Interface Wizard
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class=" btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'virtual-interface@list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-plus"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= route( 'virtual-interface@wizard' )?>" >
                Create Interface Wizard...
            </a>
            <a class="dropdown-item" href="<?= route( 'virtual-interface@create' )?>" >
                Virtual Interface Only...
            </a>
        </ul>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $t->alerts() ?>
            <?= Former::open()->method( 'POST' )
                ->action( route( 'virtual-interface@wizard-store' ) )
                ->customInputWidthClass( 'col-sm-7' )
                ->customLabelWidthClass( 'col-sm-3' )
                ->actionButtonsCustomClass( "grey-box")
            ?>

            <div id="div-well" class="collapse">
                <div class="row">
                    <div class="col-md-12 col-lg-4 mt-md-4">
                        <h3>
                            Virtual Interface Settings
                        </h3>
                        <hr>

                        <?= Former::select( 'custid' )
                            ->label( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) )
                            ->fromQuery( $t->custs, 'name' )
                            ->placeholder( 'Choose a ' . config( 'ixp_fe.lang.customer.one' ) )
                            ->addClass( 'chzn-select' )
                            ->disabled( $t->selectedCust ? true : false )
                            ->blockHelp( '' );
                        ?>

                        <?php if( $t->selectedCust ): ?>
                            <?= Former::hidden( 'custid' )
                                ->forceValue( $t->selectedCust->id )
                            ?>
                        <?php endif; ?>

                        <?= Former::select( 'vlanid' )
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

                        <?= Former::checkbox( 'ipv6enabled' )
                            ->label('&nbsp;')
                            ->text( 'IPv6 Enabled' )
                            ->blockHelp( ' ' )
                            ->value( 1 )
                            ->inline()
                        ?>

                        <?= Former::checkbox( 'ipv4enabled' )
                            ->label('&nbsp;')
                            ->text( 'IPv4 Enabled' )
                            ->blockHelp( ' ' )
                            ->value( 1 )
                            ->inline()
                        ?>

                    </div>

                    <div class="col-md-12 col-lg-4 mt-4 mt-md-4">
                        <h3>
                            Physical Interface Settings
                        </h3>
                        <hr>
                        <div id="fanout-box" class="collapse">
                            <?= Former::checkbox( 'fanout_physical_interface_id' )
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

                        <?= Former::select( 'switchportid' )
                            ->label( 'Switch Port' )
                            ->placeholder( 'Choose a switch port' )
                            ->addClass( 'chzn-select' )
                            ->blockHelp( 'Shows ports that have a type of <em>Peering</em> or <em>Unknown</em> and have not been associated with any other ' . config( 'ixp_fe.lang.customer.one' ) . ' / virtual interface.' );
                        ?>

                        <?= Former::hidden( 'original-switch-port')
                            ->id('original-switch-port')
                            ->forceValue( old('switch-port') )
                        ?>

                        <?= Former::select( 'status' )
                            ->label( 'Status' )
                            ->fromQuery( \IXP\Models\PhysicalInterface::$STATES , 'name' )
                            ->placeholder( 'Choose a status' )
                            ->addClass( 'chzn-select' )
                            ->blockHelp( 'Only virtual interfaces with at least one <em>connected</em> interface will be considered for monitoring / route server configuration, etc.' );
                        ?>

                        <?= Former::select( 'speed' )
                            ->label( 'Speed' )
                            ->fromQuery( \IXP\Models\PhysicalInterface::$SPEED , 'name' )
                            ->placeholder( 'Choose a speed' )
                            ->addClass( 'chzn-select' )
                            ->blockHelp( '' );
                        ?>

                        <?= Former::select( 'duplex' )
                            ->label( 'Duplex' )
                            ->fromQuery( \IXP\Models\PhysicalInterface::$DUPLEX , 'name' )
                            ->placeholder( 'Choose Duplex' )
                            ->addClass( 'chzn-select' )
                            ->value(1)
                            ->blockHelp( '' );
                        ?>
                    </div>

                    <div class="col-md-12 col-lg-4 mt-4 mt-md-4">
                        <h3>
                            General VLAN Settings
                        </h3>
                        <hr>
                        <?= Former::number( 'maxbgpprefix' )
                            ->label( 'Max BGP Prefixes' )
                            ->blockHelp( 'Setting this will override the overall ' . config( 'ixp_fe.lang.customer.one' ) . ' setting. Leave blank to use the overall setting.' );
                        ?>

                        <?= Former::checkbox( 'rsclient' )
                            ->label( '&nbsp;' )
                            ->text( 'Route Server Client' )
                            ->blockHelp( 'Indicates if IXP Manager should configure route server BGP sessions for this interface.' )
                            ->value( 1 )
                            ->inline()
                        ?>

                        <?= Former::checkbox( 'irrdbfilter' )
                            ->label('&nbsp')
                            ->text( 'Apply IRRDB Filtering' )
                            ->blockHelp( "<strong>Strongly recommended!</strong> Filter routes learned on route servers based on the " . config( 'ixp_fe.lang.customer.owner' ) . " IRRDB entries." )
                            ->value( 1 )
                            ->check( true )
                            ->inline()
                        ?>

                        <div id="div-rsmorespecifics" style="<?= old( 'irrdbfilter' ) || ( $t->vli && $t->vli->irrdbfilter ) ?: 'display: none;' ?>">
                            <?= Former::checkbox( 'rsmorespecifics' )
                                ->label('&nbsp;')
                                ->text( 'IRRDB - Allow More Specifics?' )
                                ->value( 1 )
                                ->inline()
                                ->blockHelp( 'If checked, then IXP Manager will configure the route server BGP peer for this connection such that it will '
                                    . 'allow more specific prefixes than those registered in the IRRDB. See the '
                                    . '<a href="http://docs.ixpmanager.org/features/route-servers/">route server configuration documentation for more details</a>.' )
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

                <div class="row mt-4">
                    <div id='ipv6-area' class="col-md-12 col-lg-6 mt-4 <?= old( 'ipv6enabled' ) || Former::checkbox( 'ipv6enabled')->getValue() ?: 'collapse' ?>">
                        <?= $t->insert( 'interfaces/common/vli/ipv6.foil.php' ) ?>
                    </div>

                    <div id='ipv4-area' class="col-md-12 col-lg-6 mt-4 <?= old( 'ipv4enabled' ) || Former::checkbox( 'ipv4enabled')->getValue() ?: 'collapse' ?>">
                        <?= $t->insert( 'interfaces/common/vli/ipv4.foil.php' ) ?>
                    </div>
                </div>

                <?=Former::actions(
                    Former::primary_submit( 'Create' )->class( "mb-2 mb-sm-0" ),
                    Former::secondary_link( 'Cancel' )->href( route( 'virtual-interface@list' ) )->class( "mb-2 mb-sm-0" ),
                    Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" ),
                    Former::info_link( 'External Documentation &Gt;' )->href( 'http://docs.ixpmanager.org/usage/interfaces/' )->target( '_blank' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                )->id('btn-group')
                ?>

                <?= Former::close() ?>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/common/js/interface-functions' ); ?>
    <?= $t->insert( 'interfaces/common/js/pi-form-logic' ); ?>
    <?= $t->insert( 'interfaces/common/js/vli-form-logic' ); ?>
    <?= $t->insert( 'interfaces/virtual/js/wizard' ); ?>
<?php $this->append() ?>