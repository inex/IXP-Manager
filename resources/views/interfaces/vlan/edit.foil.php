<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Vlan Interfaces
    /
    <?= $t->duplicateTo ? 'Duplicate' : ( $t->vli ? 'Edit' : 'Create' ) ?> VLAN Interface
    (<?= $t->vi ? $t->vi->customer->getFormattedName() : $t->vli->virtualInterface->customer->getFormattedName() ?>)
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'vlan-interface@list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <?php if( $t->vli ): ?>
            <a class="btn btn-white" href="<?= route( 'vlan-interface@view', [ 'vli' => $t->vli->id ] )?>" title="edit">
                <span class="fa fa-eye"></span>
            </a>
        <?php endif;?>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <?php if( $t->duplicateTo ): ?>
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-question-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            This form allows you to duplicate the selected VLAN interface from <em><?= $t->vli->vlan->name ?></em> to your chosen VLAN as indicated below.
                            The IP address(es) will be created if they do not already exist (and will be checked to ensure they are not already in use). The new interface will not
                            be created until you click the <em>Save Changes</em> button below.
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div id="instructions-alert" class="collapse alert alert-info mt-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-question-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">
                        <b>Instructions: </b> You are strongly advised to review <a href="http://docs.ixpmanager.org/usage/interfaces/">the official documentation</a> before adding / editing interfaces
                        on a production system.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>
                        General VLAN Settings
                    </h3>
                    <hr>
                    <?= Former::open()->method( $t->vli ? 'put' : 'post' )
                        ->action( $t->duplicateTo ?  route( 'vlan-interface@duplicate', [ 'vli' => $t->vli->id ] ) :
                            ( $t->vli ? route( 'vlan-interface@update', [ 'vli' => $t->vli->id ] ) : route( 'vlan-interface@store' ) ) )
                        ->customInputWidthClass( 'col-sm-6' )
                        ->customLabelWidthClass( 'col-md-3 col-sm-3 col-lg-4' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>

                    <div class="row">
                        <div class="col-md-12 col-lg-6">
                            <?= Former::select( 'vlanid' )
                                ->label( 'Vlan' )
                                ->fromQuery( $t->vlans, 'name' )
                                ->placeholder( 'Choose a VLAN' )
                                ->addClass( 'chzn-select' )
                                ->disabled( $t->duplicateTo ? true : false )
                                ->blockHelp( 'Pick the VLAN for this VLAN interface. IP address dropdowns will automatically populate on change.' );
                            ?>

                            <?= Former::checkbox( 'mcastenabled' )
                                ->label('&nbsp;')
                                ->text( 'Multicast Enabled' )
                                ->value( 1 )
                                ->blockHelp( 'Informational only unless you are using an automated provisioning system which will trigger a configuration changed based on this.' )
                            ?>

                            <?= Former::checkbox( 'busyhost' )
                                ->label('&nbsp;')
                                ->text( 'Busy host' )
                                ->value( 1 )
                                ->blockHelp( "This was created at INEX to quieten our monitoring systems. It is used to indicate that the " . config( 'ixp_fe.lang.customer.owner' ) . " router is unusually slow "
                                    . "to reply to ICMP echo requests and that when monitoring, the configuration should allow for warnings after a 5sec RTT rather than 1sec "
                                    . "(for example)." )

                            ?>

                            <?= Former::checkbox( 'ipv6enabled' )
                                ->label('&nbsp;')
                                ->text( 'IPv6 Enabled' )
                                ->value( 1 )
                                ->blockHelp( 'Click to enable IPv6 and configure associated settings.' )
                            ?>

                            <?= Former::checkbox( 'ipv4enabled' )
                                ->label('&nbsp;')
                                ->text( 'IPv4 Enabled' )
                                ->value( 1 )
                                ->blockHelp( 'Click to enable IPv4 and configure associated settings.' )
                            ?>

                        </div>

                        <div class="col-md-12 col-lg-6">
                            <?= Former::number( 'maxbgpprefix' )
                                ->label( 'Max BGP Prefixes' )
                                ->blockHelp( 'The maximum IPv4/6 prefixes that any router configured via IXP Manager should accept for this endpoing. '
                                    . 'See <a href="http://docs.ixpmanager.org/usage/customers/#peering-details">the official documentation</a> for more details.' );
                            ?>

                            <?= Former::checkbox( 'rsclient' )
                                ->label('&nbsp;')
                                ->text( 'Route Server Client' )
                                ->value( 1 )
                                ->blockHelp( 'If checked, then IXP Manager will configure a BGP peer for this connection when <a href="http://docs.ixpmanager.org/features/route-servers/">generating route server configurations</a>. '
                                    . 'It is also used in other areas to show if a member uses the route servers or not, by the Peering Manager to calculate missing '
                                    . 'BGP sessions, etc.' )
                            ?>

                            <?= Former::checkbox( 'irrdbfilter' )
                                ->label('&nbsp;')
                                ->text( 'Apply IRRDB Filtering' )
                                ->value( 1 )
                                ->check()
                                ->blockHelp( 'If Apply IRRDB Filtering is <b>not</b> set, then the route servers will accept any prefixes advertised by the  ' . config( 'ixp_fe.lang.customer.one' )
                                    . '(note that the default templates will filter martians and apply a max prefix limit). Generally speaking this is a very bad idea '
                                    . 'and should only be used in exceptional cases. INEX never uses this setting - but demand from other IX\'s had it added. '
                                    . 'See <a href=""http://docs.ixpmanager.org/features/irrdb/">the documentation</a> for more information.' )
                            ?>

                            <div id="div-rsmorespecifics" class="<?= old( 'irrdbfilter' ) || $t->vli && $t->vli->irrdbfilter ?: 'collapse' ?>" >
                                <?= Former::checkbox( 'rsmorespecifics' )
                                    ->label('&nbsp;')
                                    ->text( 'IRRDB - Allow More Specifics?' )
                                    ->value( 1 )
                                    ->blockHelp( 'If checked, then IXP Manager will configure the route server BGP peer for this connection such that it will '
                                        . 'allow more specific prefixes than those registered in the IRRDB. See the '
                                        . '<a href="http://docs.ixpmanager.org/features/route-servers/">route server configuration documentation for more details</a>.' )
                                ?>
                            </div>

                            <?php if( $t->as112UiActive() ): ?>
                                <?= Former::checkbox( 'as112client' )
                                    ->label('&nbsp;')
                                    ->text( 'AS112 Client' )
                                    ->value( 1 )
                                    ->blockHelp( 'If checked, then IXP Manager will configure a BGP peer for this connection when generating <a href="http://docs.ixpmanager.org/features/as112/">AS112 router configurations</a>.' )
                                ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div id='ipv6-area' class="col-md-12 col-lg-6 mt-4 <?= old( 'ipv6enabled' ) || $t->vli && $t->vli->ipv6enabled ?: 'collapse' ?> ">
                    <?= $t->insert( 'interfaces/common/vli/ipv6.foil.php' ) ?>
                </div>

                <div id='ipv4-area' class="col-md-12 col-lg-6 mt-4 <?= old( 'ipv4enabled' ) || Former::checkbox( 'ipv4enabled' )->getValue() ?: 'collapse' ?>">
                    <?= $t->insert( 'interfaces/common/vli/ipv4.foil.php' ) ?>
                </div>
            </div>

            <?= Former::hidden( 'virtualinterfaceid' )
                ->id( 'viid' )
                ->value( $t->vli ? $t->vli->virtualInterface->id : $t->vi->id)
            ?>

            <?php if( $t->duplicateTo ): ?>
                <?= Former::hidden( 'vlanid' )
                    ->id( 'vlanid' )
                    ->forceValue(  $t->duplicateTo->id )
                ?>
            <?php endif; ?>

            <?= Former::hidden( 'duplicate' )
                ->id( 'duplicate' )
                ->value(  $t->duplicateTo ? true : false )
            ?>

            <?= Former::hidden( 'redirect2vi' )
                ->value( $t->redirect2vi )
            ?>

            <?=Former::actions(
                Former::primary_submit( $t->duplicateTo ? 'Duplicate' : ( $t->vli ? 'Save Changes' : 'Create' ) )->class( "mb-2 mb-sm-0" ),
                Former::secondary_link( 'Cancel' )->id( 'cancel-btn' )->href( $t->vi ? route(  'virtual-interface@edit' , [ 'vi' => $t->vi->id ] ) :  route( 'vlan-interface@list' ) )->class( "mb-2 mb-sm-0" ),
                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
            )->id('btn-group') ?>

            <?= Former::close() ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/common/js/interface-functions' ); ?>
    <?= $t->insert( 'interfaces/common/js/vli-form-logic' ); ?>
    <?= $t->insert( 'interfaces/vlan/js/edit' ); ?>
<?php $this->append() ?>