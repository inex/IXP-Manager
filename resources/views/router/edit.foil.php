<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    <a href="<?= action( 'RouterController@list' )?>">Router</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Edit</li>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <?= $t->alerts() ?>
    <div  class="well col-sm-12">


        <?= Former::open()->method( 'POST' )
            ->action( action( 'RouterController@store' ) )
            ->customWidthClass( 'col-sm-6' )
            ->addClass( 'col-md-10' );
        ?>

        <?= Former::text( 'handle' )
            ->label( 'Handle' )
            ->placeholder( 'rs1-lan1-ipv4' )
            ->blockHelp( "The handle is like the router's name. It is suggested you use something like: <code>purpose-proto-lan</code>. A
                good example of this is <code>rs1-lan1-ipv4</code> for <em>router server #1</em> on <em>lan1</em> using <em>IPv4</em>.
                These handles are used in API calls and other areas such as Nagios configuration generation." );

        ?>

        <?= Former::select( 'vlan' )
            ->label( 'Vlan' )
            ->fromQuery( $t->vlans, 'name' )
            ->placeholder( 'Choose a VLAN' )
            ->addClass( 'chzn-select' );

        ?>

        <?= Former::select( 'protocol' )
            ->label( 'Protocol' )
            ->fromQuery( $t->protocols )
            ->placeholder( 'Choose the protocol' )
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::select( 'type' )
            ->label( 'Type' )
            ->fromQuery( $t->types )
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
            ->blockHelp( "The router's AS number." );
        ?>

        <?= Former::select( 'software' )
            ->label( 'Software' )
            ->fromQuery( $t->softwares )
            ->placeholder( 'Choose the platform / software' )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'There is no specific use for this as yet but you should choose appropriately for future correctness. If
                your platform does not exist here, please open an issue on GitHub.' );
        ?>

        <?= Former::text( 'mgmt_host' )
            ->label( 'Management Host' )
            ->placeholder( '192.0.2.89 / 2001:db8::89 / rs1-lan1-ipv4.mgmt.example.com')
            ->blockHelp( "The hostname or IP address for accessing the management interface of the host. This will be used
                for creating Nagios configurations, etc." );
        ?>

        <?= Former::select( 'api_type' )
            ->label( 'API Type' )
            ->fromQuery( $t->apiTypes )
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
            ->fromQuery( $t->lgAccess )
            ->placeholder( 'Choose Minimum Looking Glass Access Privileges' )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'What (minimum) privileges must a user have to access the looking glass of this router. <em>Only relevant 
                if the API type is not none.</em>' );
        ?>

        <?= Former::checkbox( 'quarantine' )
            ->label( 'Quarantine' )
            ->text( 'Router will be used for quarantine procedures only' )
            ->unchecked_value( 0 )
            ->value( 1 )
            ->blockHelp( "Is this router used in quarantine rather than production? The effect of this is that BGP client
                sessions are only generated for interfaces that have a physical interface in quarantine." );

        ?>

        <?= Former::checkbox( 'bgp_lc' )
            ->label('BGP LC')
            ->text( 'Enable Large BGP Communities / RFC8092' )
            ->unchecked_value( 0 )
            ->value( 1 )
            ->blockHelp( "Enable support for Large BGP Communities? (RFC8092). NB: must be supported by both the 
                template and the software / platform!" );
        ?>

        <?= Former::checkbox( 'skip_md5' )
            ->label( 'Skip MD5' )
            ->text( 'Do not include any MD5 configuration' )
            ->unchecked_value( 0 )
            ->value( 1 )
            ->blockHelp( 'If checked, all sessions will be configured without MD5 whether they have an
                MD5 password set on an interface or not.' );
        ?>

        <?= Former::text( 'template' )
            ->label( 'Template' )
            ->blockHelp( "The template to use to generate the router's configuration. This is a path to a template file
                starting at either the <code>resources/views</code> or <code>resources/skins/\$skin</code>. It is best
                to read the documentation for this but examples of route server, route collector and AS112 configs as
                used at INEX can be used with the bundled templates by entering one of the following:<br><br>
                    &middot; <code>api/v4/router/server/bird/standard</code><br>
                    &middot; <code>api/v4/router/collector/bird/standard</code><br>
                    &middot; <code>api/v4/router/as112/bird/standard</code>
                " );
        ?>

        <?=Former::actions( Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( action( 'RouterController@list' ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        );?>

        <?= Former::hidden( 'id' )
            ->value( $t->rt ? $t->rt->getId() : '' )
        ?>

    <?= Former::close() ?>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<script>
    /**
     * hide the help block at loading
     */
    $('p.help-block').hide();

    /**
     * display / hide help sections on click on the help button
     */
    $( "#help-btn" ).click( function() {
        $( "p.help-block" ).toggle();
    });

</script>
<?php $this->append() ?>