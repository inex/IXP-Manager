<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>


<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'interfaces/vlan/list' )?>">Vlan Interfaces</a>
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
    <li><?= $t->vli ? 'Edit' : 'Add' ?> VLAN Interface</li>
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

<div class="container-fluid">

    <?= $t->alerts() ?>

    <div id="instructions-alert" class="alert alert-info" style="display: none;">
        <b>Instructions: </b> You are strongly advised to review <a href="http://docs.ixpmanager.org/usage/interfaces/">the official documentation</a> before adding / editing interfaces
        on a production system.
    </div>

    <div class="row">
        <h3>
            General VLAN Settings
        </h3>
        <hr>
    </div>


    <?= Former::open()->method( 'post' )
        ->action( route( 'interfaces/vlan/store' ) )
        ->customWidthClass( 'col-sm-6' )
    ?>

    <div class="row">

        <div class="col-md-6">

            <?= Former::select( 'vlan' )
                ->label( 'Vlan' )
                ->fromQuery( $t->vlan, 'name' )
                ->placeholder( 'Choose a VLAN' )
                ->addClass( 'chzn-select' )
                ->disabled( $t->duplicated ? true : false)
                ->blockHelp( 'Pick the VLAN for this VLAN interface. IP address dropdowns will automatically populate on change.' );
            ?>

            <?= Former::checkbox( 'mcastenabled' )
                ->label('&nbsp;')
                ->text( 'Multicast Enabled' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( 'Informational only unless you are using an automated provisioning system which will trigger a configuration changed based on this.' )
            ?>

            <?= Former::checkbox( 'busyhost' )
                ->label('&nbsp;')
                ->text( 'Busy host' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( "This was created at INEX to quieten our monitoring systems. It is used to indicate that the customer's router is unusually slow "
                    . "to reply to ICMP echo requests and that when monitoring, the configuration should allow for warnings after a 5sec RTT rather than 1sec "
                    . "(for example)." )

            ?>

            <?= Former::checkbox( 'ipv6-enabled' )
                ->label('&nbsp;')
                ->text( 'IPv6 Enabled' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( 'Click to enable IPv6 and reveal associated settings.' )
            ?>

            <?= Former::checkbox( 'ipv4-enabled' )
                ->label('&nbsp;')
                ->text( 'IPv4 Enabled' )
                ->unchecked_value( 0 )
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
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( 'If checked, then IXP Manager will configure a BGP peer for this connection when <a href="http://docs.ixpmanager.org/features/route-servers/">generating route server configurations</a>. '
                    . 'It is also used in other areas to show if a member uses the route servers or not, by the Peering Manager to calculate missing '
                    . 'BGP sessions, etc.' )
            ?>

            <?= Former::checkbox( 'irrdbfilter' )
                ->label('&nbsp;')
                ->text( 'Apply IRRDB Filtering' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( 'If Apply IRRDB Filtering is <b>not</b> set, then the route servers will accept any prefixes advertised by the customer '
                    . '(note that the default templates will filter martians and apply a max prefix limit). Generally speaking this is a very bad idea '
                    . 'and should only be used in exceptional cases. INEX never uses this setting - but demand from other IX\'s had it added. '
                    . 'See <a href=""http://docs.ixpmanager.org/features/irrdb/">the documentation</a> for more information.' )
            ?>


            <?php if( $t->as112UiActive() ): ?>
                <?= Former::checkbox( 'as112client' )
                    ->label('&nbsp;')
                    ->text( 'AS112 Client' )
                    ->unchecked_value( 0 )
                    ->value( 1 )
                    ->blockHelp( 'If checked, then IXP Manager will configure a BGP peer for this connection when generating <a href="http://docs.ixpmanager.org/features/as112/">AS112 router configurations</a>.' )
                ?>
            <?php endif; ?>



        </div>

    </div>

    <br/>

    <div class="row">

        <div id='ipv6-area' class="col-md-6" style="<?= $t->vli && $t->vli->getIPv6Enabled() ?: 'display: none;' ?> float: right;">

            <h4>
                IPv6 Details
            </h4>

            <hr>

            <div id='alert-ipv6-address' class="alert alert-warning collapse ip-is-used-alert" role="alert"></div>

            <?= Former::select( 'ipv6-address' )
                ->label( 'IPv6 Address' )
                ->placeholder( 'Choose an IPv6 address...' )
                ->blockHelp( 'Chose the IPv6 address from the list of available addresses for this VLAN.' );
            ?>
            <?= Former::text( 'ipv6-hostname' )
                ->label( 'IPv6 Hostname' )
                ->blockHelp( 'This can/should be used for generated a DNS ARPA record against the above address.' );
            ?>

            <?= Former::text( 'ipv6-bgp-md5-secret' )
                ->label( 'IPv6 BGP MD5 Secret' )
                ->blockHelp( 'The MD5 secret to protect the BGP session with. Optional but encouraged on a shared LAN such as that found at an IXP.<br><br>'
                    . 'This is initially generated using a cryptographically secure randomly generated string.');
            ?>

            <?= Former::checkbox( 'ipv6-can-ping' )
                ->label( 'Can Ping' )
                ->blockHelp( 'IXP Manager generates configuration for a number of other tools such as Smokeping and Nagios which ping customer routers. These are '
                    . 'invaluable tools for problem solving, monitoring and graphing long term trends. We enable this by default unless a customer specifically '
                    . 'asks us not to.' )
            ?>

            <?= Former::checkbox( 'ipv6-monitor-rcbgp' )
                ->label( 'IPv6 Monitor RC BGP' )
                ->blockHelp( '<b>Will be deprecated.</b> A legacy option for configuration builders that used to check for established route collector '
                    . 'BGP sessions and warn if not present. This is deprecated and will be removed in favour of looking glass functionality.' )
            ?>

            <br>

        </div>

        <div id='ipv4-area' class="col-md-6" style="<?= $t->vli && $t->vli->getIPv4Enabled() ?: 'display: none;' ?> float: left;">

            <h4>
                IPv4 Details
            </h4>

            <hr>

            <div id='alert-ipv4-address' class="alert alert-warning collapse ip-is-used-alert" role="alert"></div>

            <?= Former::select( 'ipv4-address' )
                ->label( 'IPv4 Address' )
                ->placeholder( 'Choose an IPv4 address...' )
                ->blockHelp( 'Chose the IPv4 address from the list of available addresses for this VLAN.' );
            ?>

            <?= Former::text( 'ipv4-hostname' )
                ->label( 'IPv4 Hostname' )
                ->blockHelp( 'This can/should be used for generated a DNS ARPA record against the above address.' );
            ?>

            <?= Former::text( 'ipv4-bgp-md5-secret' )
                ->label( 'IPv4 BGP MD5 Secret' )
                ->blockHelp( 'The MD5 secret to protect the BGP session with. Optional but encouraged on a shared LAN such as that found at an IXP.<br><br>'
                    . 'This is initially generated using a cryptographically secure randomly generated string.');
            ?>

            <?= Former::checkbox( 'ipv4-can-ping' )
                ->label( 'Can Ping' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( 'IXP Manager generates configuration for a number of other tools such as Smokeping and Nagios which ping customer routers. These are '
                    . 'invaluable tools for problem solving, monitoring and graphing long term trends. We enable this by default unless a customer specifically '
                    . 'asks us not to.' )
            ?>

            <?= Former::checkbox( 'ipv4-monitor-rcbgp' )
                ->label( 'Monitor RC BGP' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( '<b>Will be deprecated.</b> A legacy option for configuration builders that used to check for established route collector '
                    . 'BGP sessions and warn if not present. This is deprecated and will be removed in favour of looking glass functionality.' )
            ?>

            <br>

        </div>

    </div>

    <div class="row">
        <?= Former::hidden( 'id' )
            ->value( $t->vli ? $t->vli->getId() : null )
        ?>

        <?= Former::hidden( 'viid' )
            ->id( 'viid' )
            ->value( $t->vli ? $t->vli->getVirtualInterface()->getId() : $t->vi->getId())
        ?>

        <?php if( $t->duplicated ): ?>
            <?= Former::hidden( 'vlan' )
                ->id( 'vlan' )
                ->value(  $t->duplicated )
            ?>
        <?php endif; ?>

        <?= Former::hidden( 'duplicated' )
            ->id( 'duplicated' )
            ->value(  $t->duplicated ? true : false )
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
            Former::default_link( 'Cancel' )->href( $t->vi ? route(  'interfaces/virtual/edit' , [ 'id' => $t->vi->getId() ] ) :  route( 'interfaces/vlan/list' ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->id('btn-group');?>

        <?= Former::close() ?>
    </div>

</div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/virtual/js/acrossVlans' ); ?>
    <?= $t->insert( 'interfaces/vlan/js/edit' ); ?>
<?php $this->append() ?>