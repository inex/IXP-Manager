<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Router / <?=  $t->rt  ? 'Edit : '. $t->rt->name : 'Create' ?></li>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/routers/">
            Documentation
        </a>
        <a class="btn btn-white" href="<?= route('router@list' ) ?>" title="list">
            <i class="fa fa-th-list"></i>
        </a>
        <?php if( $t->rt ): ?>
            <a class="btn btn-white" href="<?= route ('router@create' ) ?>" title="create">
                <i class="fa fa-plus"></i>
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div  class="col-sm-12">
            <?= $t->alerts() ?>
            <?php if( $t->rt ): ?>
                <div class="alert alert-warning" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-exclamation-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            <b>WARNING:</b> Do not change any parameters of a router object if it is in production. Please consider
                            change control procedures when ever editing the configuration of a critical service such as a route server.
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <?= Former::open()
                        ->method( $t->rt ? 'PUT' : 'POST' )
                        ->action(  $t->rt ?
                            route('router@update', [ 'router' => $t->rt ] )
                            : route('router@store' ) )
                        ->customInputWidthClass( 'col-sm-6' )
                        ->addClass( 'col-md-10' )
                        ->actionButtonsCustomClass( "grey-box");
                    ?>

                    <?= Former::text( 'handle' )
                        ->label( 'Handle' )
                        ->placeholder( 'rs1-lan1-ipv4' )
                        ->blockHelp( "The handle is like the router's name. It is suggested you use something like: <code>purpose-proto-lan</code>. A
                    good example of this is <code>rs1-lan1-ipv4</code> for <em>route server #1</em> on <em>lan1</em> using <em>IPv4</em>.
                    These handles are used in API calls and other areas such as Nagios configuration generation." );
                    ?>

                    <?= Former::select( 'vlan_id' )
                        ->label( 'Vlan' )
                        ->fromQuery( $t->vlans, 'name' )
                        ->placeholder( 'Choose a VLAN' )
                        ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::select( 'protocol' )
                        ->label( 'Protocol' )
                        ->fromQuery( \IXP\Models\Router::$PROTOCOLS )
                        ->placeholder( 'Choose the protocol' )
                        ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::select( 'type' )
                        ->label( 'Type' )
                        ->fromQuery( \IXP\Models\Router::$TYPES )
                        ->placeholder( 'Choose a type / function' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The function of this router. We define three. If you use <em>Other</em> then we suggest opening a 
                    conversation on the IXP Manager mailing list to see if a fourth type is warranted.' );
                    ?>

                    <?= Former::text( 'name' )
                        ->label( 'Name' )
                        ->placeholder('Route Server #1 - IXP LAN1 - IPv4')
                        ->blockHelp( "A long descriptive name for the router. Following the example of the handle above 
                    (<code>rs1-lan1-ipv4</code>), we would use the following here: <em>Route Server #1 - IXP LAN1 - IPv4</em>." );
                    ?>

                    <?= Former::text( 'shortname' )
                        ->label( 'ShortName' )
                        ->placeholder('RS1 - LAN1 - IPv4')
                        ->maxlength( 20 )
                        ->blockHelp( "A shorter version of the name to be used in (for example) dropdowns or other space constrained areas 
                    where name may be too long. Using the example name above, the short name might be: <em>RS1 - LAN1 - IPv4</em>" );
                    ?>

                    <?= Former::text( 'router_id' )
                        ->label( 'Router ID' )
                        ->placeholder('192.0.2.8')
                        ->blockHelp( "The router's BGP ID (e.g. <code>192.0.2.8</code>). Must validate as an IPv4 address." );
                    ?>

                    <?= Former::text( 'peering_ip' )
                        ->label( 'Peering IP' )
                        ->placeholder( '192.0.2.8 / 2001:db8::8' )
                        ->blockHelp( 'The IPv4/6 address that this router initiates BGP peering sessions from.' );
                    ?>

                    <?= Former::text( 'asn' )
                        ->label( 'ASN' )
                        ->placeholder( '65501' )
                        ->blockHelp( "The router's AS number.<br><br>"
                            . "If you are adding a route server, you are strongly advised to use a 16-bit ASN as otherwise "
                            . "community filtering will be unavailable."
                        );
                    ?>

                    <?= Former::select( 'software' )
                        ->label( 'Software' )
                        ->fromQuery( \IXP\Models\Router::$SOFTWARES )
                        ->placeholder( 'Choose the platform / software' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The software used for establishing BGP sessions with this router configuration.' );
                    ?>

                    <?= Former::text( 'software_version' )
                        ->label( 'Software Version' )
                        ->placeholder( '2.0.4')
                        ->blockHelp( "The version of the BGP software daemon (free text, used in IX-F export" );
                    ?>

                    <?= Former::text( 'operating_system' )
                        ->label( 'Operating System' )
                        ->placeholder( 'Ubuntu Linux / FreeBSD / Debian / ...')
                        ->blockHelp( "The operating system that runs the BGP software daemon (free text, used in IX-F export" );
                    ?>

                    <?= Former::text( 'operating_system_version' )
                        ->label( 'OS Version' )
                        ->placeholder( '18.04 / 11.2 / 7 / ...')
                        ->blockHelp( "The operating system version that runs the BGP software daemon (free text, used in IX-F export" );
                    ?>

                    <?= Former::text( 'mgmt_host' )
                        ->label( 'Management Host' )
                        ->placeholder( '192.0.2.89 / 2001:db8::89 / rs1-lan1-ipv4.mgmt.example.com')
                        ->blockHelp( "The hostname or IP address for accessing the management interface of the host. This will be used
                    for creating Nagios configurations, etc." );
                    ?>

                    <?= Former::select( 'api_type' )
                        ->label( 'API Type' )
                        ->fromQuery( \IXP\Models\Router::$API_TYPES )
                        ->placeholder( 'Choose an API type' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( "For monitoring and looking glass functionality, we support API access to the router. The only currently
                    implemented version is Birdseye. See the IXP Manager documentation for more information." );
                    ?>

                    <?= Former::text( 'api' )
                        ->label( 'API Endpoint' )
                        ->placeholder( "https://rc1-lan1-ipv4.mgmt.example.com/api" )
                        ->blockHelp( "The URL endpoint of the API. <em>Only relevant if the API type is not none.</em>" );
                    ?>

                    <?= Former::select( 'lg_access' )
                        ->label( 'LG Access Privileges' )
                        ->fromQuery( \IXP\Models\User::$PRIVILEGES_ALL )
                        ->placeholder( 'Choose Minimum Looking Glass Access Privileges' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'What (minimum) privileges must a user have to access the looking glass of this router. <em>Only relevant 
                    if the API type is not none.</em>' );
                    ?>

                    <?= Former::checkbox( 'quarantine' )
                        ->label( 'Quarantine' )
                        ->text( 'Router will be used for quarantine procedures only' )
                        ->value( 1 )
                        ->inline()
                        ->blockHelp( "Is this router used in quarantine rather than production? The effect of this is that BGP client
                    sessions are only generated for interfaces that have a physical interface in quarantine." );
                    ?>

                    <?= Former::checkbox( 'bgp_lc' )
                        ->label('BGP LC')
                        ->text( 'Enable Large BGP Communities / RFC8092' )
                        ->value( 1 )
                        ->inline()
                        ->blockHelp( "Enable support for Large BGP Communities? (RFC8092). NB: must be supported by both the 
                    template and the software / platform!" );
                    ?>

                    <?= Former::checkbox( 'rpki' )
                        ->label('RPKI')
                        ->text( 'Enable RPKI filtering' )
                        ->value( 1 )
                        ->inline()
                        ->blockHelp( "Enable support for RPKI filtering. NB: must be supported by both the 
                    template and the software / platform! Ensure you have created at least one RPKI-RTR daemon also." );
                    ?>

                    <?= Former::checkbox( 'rfc1997_passthru' )
                        ->label('RFC1997 Passthru')
                        ->text( 'Pass through RFC1997 well-known communities (recommended)' )
                        ->value( 1 )
                        ->inline()
                        ->blockHelp( 'Pass through RFC1997 well-known communities on route servers. It is recommended that this be
                            enabled on route servers (note that it will reset BGP sessions on Bird so should be changed in a 
                            planned maintenance window only). See 
                            <a href="https://docs.ixpmanager.org/features/route-servers/#rfc1997-passthru">this documentation</a>
                            for more details.' );
                    ?>

                    <?= Former::checkbox( 'skip_md5' )
                        ->label( 'Skip MD5' )
                        ->text( 'Do not include any MD5 configuration' )
                        ->value( 1 )
                        ->inline()
                        ->blockHelp( 'If checked, all sessions will be configured without MD5 whether they have an
                    MD5 password set on an interface or not.' );
                    ?>

                    <?= Former::text( 'template' )
                        ->label( 'Template' )
                        ->blockHelp( "The template to use to generate the router's configuration. This is a path to a template file
                    starting at either the <code>resources/views</code> or <code>resources/skins/\$skin</code>. It is best
                    to read the documentation for this but examples of route server, route collector and AS112 configs as
                    used at INEX can be used with the bundled templates by entering one of the following:<br><br>
                        &middot; <code>api/v4/router/server/bird2/standard</code><br>
                        &middot; <code>api/v4/router/collector/bird2/standard</code><br>
                        &middot; <code>api/v4/router/as112/bird2/standard</code><br>
                        &middot; <code>api/v4/router/server/bird/standard</code><br>
                        &middot; <code>api/v4/router/collector/bird/standard</code><br>
                        &middot; <code>api/v4/router/as112/bird/standard</code><br><br>
                        
                        <b>NB: only <code>bird2</code> templates support RPKI and advanced looking glass features.</b>
                    " );
                    ?>

                    <?=Former::actions( Former::primary_submit( $t->rt ? 'Save Changes' : 'Create' )->id('btn-submit-form')->class( "mb-2 mb-sm-0"),
                        Former::secondary_link( 'Cancel' )->href( route( 'router@list' ) )->class( "mb-2 mb-sm-0"),
                        Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0")
                    );?>

                    <?= Former::close() ?>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>