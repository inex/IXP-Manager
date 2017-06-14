<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'interfaces/virtual/list' )?>">Virtual Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Add Interface Wizard</li>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?= $t->alerts() ?>

<?= Former::open()->method( 'POST' )
    ->action( route( 'interfaces/virtual/wizard-save' ) )
    ->customWidthClass( 'col-sm-7' )
?>

<div id="div-well" class="well collapse"> <?php /* collapse as we change CSS is JS and will reveal it afterwards */ ?>

    <div class="row">

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
                ->blockHelp( '' );
            ?>

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
                ->check( false )
            ?>

            <?= Former::checkbox( 'ipv4-enabled' )
                ->label('&nbsp;')
                ->text( 'IPv4 Enabled' )
                ->blockHelp( ' ' )

            ?>

            <?= Former::checkbox( 'ipv6-enabled' )
                ->label('&nbsp;')
                ->text( 'IPv6 Enabled' )
                ->blockHelp( ' ' )
            ?>

        </div>

        <div class="col-sm-4">
            <h3>
                Physical Interface Settings
            </h3>
            <hr>
            <?= Former::select( 'switch' )
                ->label( 'Switch' )
                ->fromQuery( $t->pi_switches, 'name' )
                ->placeholder( 'Choose a Switch' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'switch-port' )
                ->label( 'Switch Port' )
                ->placeholder( 'Choose a switch port' )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'Shows ports that have a type of <em>Peering</em> or <em>Unknown</em> and have not been associated with any other customer / virtual interface.' );
            ?>

            <?= Former::select( 'status' )
                ->label( 'Status' )
                ->fromQuery( $t->pi_states, 'name' )
                ->placeholder( 'Choose a status' )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'Only virtual interfaces with at least one <em>connected</em> interface will be considered for monitoring / route server configuration, etc.' );
            ?>

            <?= Former::select( 'speed' )
                ->label( 'Speed' )
                ->fromQuery( $t->pi_speeds, 'name' )
                ->placeholder( 'Choose a speed' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'duplex' )
                ->label( 'Duplex' )
                ->fromQuery( $t->pi_duplexes, 'name' )
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

            <?= Former::checkbox( 'irrdbfilter' )
                ->label('&nbsp')
                ->text( 'Apply IRRDB Filtering' )
                ->blockHelp( "<strong>Strongly recommended!</strong> Filter routes learned on route servers based on the customer's IRRDB entries." )
                ->check( true )
            ?>

            <?= Former::checkbox( 'mcastenabled' )
                ->label('&nbsp')
                ->text( 'Multicast Enabled' )
                ->blockHelp( 'Indicates if this port should be configured to allow multicast.' )
            ?>

            <?= Former::checkbox( 'rsclient' )
                ->label('&nbsp;')
                ->text( 'Route Server Client' )
                ->blockHelp( 'Indicates if IXP Manager should configure route server BGP sessions for this interface.' )
            ?>

            <?= Former::checkbox( 'as112client' )
                ->label( '&nbsp;' )
                ->text( 'AS112 Client' )
                ->blockHelp( 'Indicates if IXP Manager should configure AS112 BGP sessions for this interface.' )

            ?>
        </div>
    </div>
</div>

<br/>

<div class="row">
    <div id='ipv4-area' class="col-sm-5" style="display: none">
        <h3>
            IPv4 Details
        </h3>
        <hr>
        <?= Former::select( 'ipv4-address' )
            ->label( 'IPv4 Address' )
            ->placeholder( 'Choose IPv4 Address' )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Select the IP address to assign to this VLAN interface. If empty, ensure you have selected a VLAN above and that the VLAN has available addresses.' );
        ?>
        <?= Former::text( 'ipv4-hostname' )
            ->label( 'IPv4 Hostname' )
            ->blockHelp( 'The PTR ARPA record that should be associated with this IP address. Normally selected by the customer. E.g. <code>customer.ixpname.net</code>.' );
        ?>

        <?= Former::text( 'ipv4-bgp-md5-secret' )
            ->label( 'IPv4 BGP MD5 Secret' )
            ->appendIcon( 'generator-ipv4 glyphicon glyphicon-refresh' )
            ->blockHelp( 'MD5 secret for route server / collector / AS112 BGP sessions. If supported by your browser, it can be generated in a cryptographically secure manner by clicking the <em>refresh</em> button.' );
        ?>

        <?= Former::checkbox( 'ipv4-can-ping' )
            ->label( '&nbsp;' )
            ->text( 'IPv4 Ping Allowed / Possible' )
            ->check( true )
            ->blockHelp( "IXP's typically monitor customer interfaces for reachability / latency using pings. If the customer has asked you not to do this, uncheck this box." )
        ?>

        <?= Former::checkbox( 'ipv4-monitor-rcbgp' )
            ->label( '&nbsp;' )
            ->text( 'IPv4 Monitor Route Collector BGP' )
            ->check(true)
            ->blockHelp( "IXP's often monitor a customer's route collector BGP session. If this is not possible / unsuitable for this customer, uncheck this box." )
        ?>
    </div>

    <div id='ipv6-area' class="col-sm-5" style="display: none">
        <h3>
            IPv6 Details
        </h3>
        <hr>
        <?= Former::select( 'ipv6-address' )
            ->label( 'IPv6 Address' )
            ->placeholder( 'Choose IPv6 Address' )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Select the IP address to assign to this VLAN interface. If empty, ensure you have selected a VLAN above and that the VLAN has available addresses.' );
        ?>
        <?= Former::text( 'ipv6-hostname' )
            ->label( 'IPv6 Hostname' )
            ->blockHelp( 'The PTR ARPA record that should be associated with this IP address. Normally selected by the customer. E.g. <code>customer.ixpname.net</code>.' );
        ?>

        <?= Former::text( 'ipv6-bgp-md5-secret' )
            ->label( 'IPv6 BGP MD5 Secret' )
            ->appendIcon( 'generator-ipv6 glyphicon glyphicon-refresh' )
            ->blockHelp( 'MD5 secret for route server / collector / AS112 BGP sessions. Can be copied from the IPv4 version if set or (if supported by your browser), it can be generated in a cryptographically secure manner by clicking the <em>refresh</em> button.' );
        ?>

        <?= Former::checkbox( 'ipv6-can-ping' )
            ->label( '&nbsp;' )
            ->text( 'IPv6 Ping Allowed / Possible' )
            ->check(true)
            ->blockHelp( "IXP's typically monitor customer interfaces for reachability / latency using pings. If the customer has asked you not to do this, uncheck this box." )
        ?>

        <?= Former::checkbox( 'ipv6-monitor-rcbgp' )
            ->label( '&nbsp;' )
            ->text( 'IPv6 Monitor Route Collector BGP' )
            ->check(true)
            ->blockHelp( "IXP's often monitor a customer's route collector BGP session. If this is not possible / unsuitable for this customer, uncheck this box." )
        ?>
    </div>
</div>

<div class="row">
    <div class="span12">

        <br><br>

        <?=Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( route( 'interfaces/virtual/list' ) ),
            Former::success_button( 'Inline Help' )->id( 'help-btn' ),
            Former::info_link( 'External Documentation &Gt;' )->href( 'http://docs.ixpmanager.org/usage/interfaces/' )->target( '_blank' )->id( 'help-btn' )
        )->id('btn-group');?>

    </div>
</div>

<?= Former::close() ?>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<?= $t->insert( 'interfaces/virtual/js/wizard' ); ?>
<?php $this->append() ?>

