<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>


<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'interfaces/vlan/list' )?>">Vlan Interfaces</a>
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        <?= $t->duplicateTo ? 'Duplicate' : ( $t->vli ? 'Edit' : 'Add' ) ?> VLAN Interface
        (<?= $t->vi->getCustomer()->getFormattedName() ?>)
    </li>
<?php $this->append() ?>


<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route( 'interfaces/vlan/list' )?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <?php if( $t->vli ): ?>
                <a type="button" class="btn btn-default" href="<?= route( 'interfaces/vlan/view', [ 'id' => $t->vli->getId() ] )?>" title="edit">
                    <span class="glyphicon glyphicon-eye-open"></span>
                </a>
            <?php endif;?>
        </div>
    </li>
<?php $this->append() ?>



<?php $this->section('content') ?>

    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <?php if( $t->duplicateTo ): ?>

                <div class="alert alert-info">
                    This form allows you to duplicate the selected VLAN interface from <em><?= $t->vli->getVlan()->getName() ?></em> to your chosen VLAN as indicated below.
                    The IP address(es) will be created if they do not already exist (and will be checked to ensure they are not already in use). The new interface will not
                    be created until you click the <em>Save Changes</em> button below.
                </div>

            <?php endif; ?>

            <div id="instructions-alert" class="alert alert-info" style="display: none;">
                <b>Instructions: </b> You are strongly advised to review <a href="http://docs.ixpmanager.org/usage/interfaces/">the official documentation</a> before adding / editing interfaces
                on a production system.
            </div>

            <div class="well">

                <h3>
                    General VLAN Settings
                </h3>
                <hr>

                <?= Former::open()->method( 'post' )
                    ->action( route( 'interfaces/vlan/store' ) )
                    ->customWidthClass( 'col-sm-6' )
                ?>


                <div class="col-md-6">

                    <?= Former::select( 'vlan' )
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
                        ->blockHelp( "This was created at INEX to quieten our monitoring systems. It is used to indicate that the customer's router is unusually slow "
                            . "to reply to ICMP echo requests and that when monitoring, the configuration should allow for warnings after a 5sec RTT rather than 1sec "
                            . "(for example)." )

                    ?>

                    <?= Former::checkbox( 'ipv6-enabled' )
                        ->label('&nbsp;')
                        ->text( 'IPv6 Enabled' )
                        ->value( 1 )
                        ->blockHelp( 'Click to enable IPv6 and reveal associated settings.' )
                    ?>

                    <?= Former::checkbox( 'ipv4-enabled' )
                        ->label('&nbsp;')
                        ->text( 'IPv4 Enabled' )
                        ->value( 1 )
                        ->blockHelp( 'Click to enable IPv4 and reveal associated settings.' )
                    ?>

                </div>

                <div class="col-md-6">

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
                        ->blockHelp( 'If Apply IRRDB Filtering is <b>not</b> set, then the route servers will accept any prefixes advertised by the customer '
                            . '(note that the default templates will filter martians and apply a max prefix limit). Generally speaking this is a very bad idea '
                            . 'and should only be used in exceptional cases. INEX never uses this setting - but demand from other IX\'s had it added. '
                            . 'See <a href=""http://docs.ixpmanager.org/features/irrdb/">the documentation</a> for more information.' )
                    ?>

                    <div id="div-rsmorespecifics" style="<?= old( 'irrdbfilter' ) || $t->vli && $t->vli->getIrrdbfilter() ?: 'display: none;' ?>">
                        <?= Former::checkbox( 'rsmorespecifics' )
                            ->label('&nbsp;')
                            ->text( 'IRRDB - Allow More Specifics?' )
                            ->value( 1 )
                            ->blockHelp( 'If checked, then IXP Manager will configure the route server BGP peer for this connection such that it will '
                                . 'allow more specific prefixes than those registered in the IRRDB. See the '
                                . '<a href="http://docs.ixpmanager.org/features/route-servers/">route server configuration documenation for more details</a>.' )
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

                <br/>
                <div style="clear: both"></div>


                <div id='ipv6-area' class="col-md-6" style="<?= old( 'ipv6-enabled' ) || $t->vli && $t->vli->getIPv6Enabled() ?: 'display: none;' ?>">

                    <?= $t->insert( 'interfaces/common/vli/ipv6.foil.php' ) ?>
                    <br>

                </div>


                <div id='ipv4-area' class="col-md-6" style="<?= old( 'ipv4-enabled' ) || Former::checkbox( 'ipv4-enabled' )->getValue() ?: 'display: none;' ?>">
                    <?= $t->insert( 'interfaces/common/vli/ipv4.foil.php' ) ?>
                    <br>

                </div>


                <?= Former::hidden( 'id' )
                    ->value( $t->vli ? $t->vli->getId() : null )
                ?>

                <?= Former::hidden( 'viid' )
                    ->id( 'viid' )
                    ->value( $t->vli ? $t->vli->getVirtualInterface()->getId() : $t->vi->getId())
                ?>

                <?php if( $t->duplicateTo ): ?>
                    <?= Former::hidden( 'vlan' )
                        ->id( 'vlan' )
                        ->value(  $t->duplicateTo )
                    ?>
                <?php endif; ?>

                <?= Former::hidden( 'duplicate' )
                    ->id( 'duplicate' )
                    ->value(  $t->duplicateTo ? true : false )
                ?>

                <?= Former::hidden( 'viid' )
                    ->id( 'viid' )
                    ->value( $t->vli ? $t->vli->getVirtualInterface()->getId() : $t->vi->getId())
                ?>

                <?= Former::hidden( 'redirect2vi' )
                    ->value( $t->vi ? true : false )
                ?>

                <?=Former::actions(
                    Former::primary_submit( $t->vli ? 'Save Changes' : 'Add' ),
                    Former::default_link( 'Cancel' )->id( 'cancel-btn' )->href( $t->vi ? route(  'interfaces/virtual/edit' , [ 'id' => $t->vi->getId() ] ) :  route( 'interfaces/vlan/list' ) ),
                    Former::success_button( 'Help' )->id( 'help-btn' )
                )->id('btn-group');?>

                <?= Former::close() ?>

            </div>




        </div>


    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/common/js/interface-functions' ); ?>
    <?= $t->insert( 'interfaces/common/js/vli-form-logic' ); ?>
    <?= $t->insert( 'interfaces/vlan/js/edit' ); ?>
<?php $this->append() ?>