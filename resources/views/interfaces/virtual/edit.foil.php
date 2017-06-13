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

<div class="well">

    <div class="row">

        <div class="col-sm-4">
            <h3>
                Virtual Interface Settings
            </h3>
            <hr>
            <?= Former::select( 'cust' )
                ->label( 'Customer' )
                ->fromQuery( $t->cust, 'name' )
                ->placeholder( 'Choose a Customer' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'vlan' )
                ->label( 'Vlan' )
                ->fromQuery( $t->vlan, 'name' )
                ->placeholder( 'Choose a Vlan' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::checkbox( 'trunk' )
                ->label('&nbsp;')
                ->text( 'Use 802.1q framing' )
                ->blockHelp( 'Indicates if this port should be configured for 802.1q framing / tagged packets.' )
                ->check( $t->vi ? $t->vi->getTrunk() : false )
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
                ->fromQuery( $t->switches, 'name' )
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
                ->fromQuery( $t->status, 'name' )
                ->placeholder( 'Choose a status' )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'Only virtual interfaces with at least one <em>connected</em> interface will be considered for monitoring / route server configuration, etc.' );
            ?>

            <?= Former::select( 'speed' )
                ->label( 'Speed' )
                ->fromQuery( $t->speed, 'name' )
                ->placeholder( 'Choose a speed' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'duplex' )
                ->label( 'Duplex' )
                ->fromQuery( $t->duplex, 'name' )
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

        <?= Former::hidden( 'id' )
            ->value( $t->vi ? $t->vi->getId() : null )
        ?>

        <?=Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( url( 'virtualInterface/list/' ) ),
            Former::success_button( 'Inline Help' )->id( 'help-btn' ),
            Former::info_link( 'External Documentation &Gt;' )->href( 'http://docs.ixpmanager.org/usage/interfaces/' )->target( '_blank' )->id( 'help-btn' )
        )->id('btn-group');?>

    </div>
</div>

<?= Former::close() ?>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>

        $(document).ready( function() {
            $( 'label.col-lg-2' ).removeClass('col-lg-2');
            $( '.input-group-addon' ).addClass('btn btn-default');

            if ($( '#ipv4-enabled' ).is(":checked") ) {
                $( "#ipv4-area" ).slideDown();
            }
            if ($( '#ipv6-enabled' ).is(":checked") ) {
                $( "#ipv6-area" ).slideDown();
            }

            setIPVx();

        });

        $( "#switch" ).change(function(){
            $( "#switch-port" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );

            switchId = $( "#switch" ).val();

            // ask what is that ?
            var type = "peering";
            url = "<?= url( '/api/v4/switcher' )?>/" + switchId + "/switch-port-not-assign-to-pi";

            $.ajax( url , {
                data: {type : type },
                type: 'POST'
            })
                .done( function( data ) {
                    var options = "<option value=\"\">Choose a switch port</option>\n";
                    $.each( data.listPorts, function( key, value ){
                        options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
                    });
                    $( "#switch-port" ).html( options );
                })
                .fail( function() {
                    throw new Error( "Error running ajax query for api/v4/switcher/$id/switch-port-not-assign-to-pi" );
                    alert( "Error running ajax query for api/v4/switcher/$id/switch-port-not-assign-to-pi" );
                })
                .always( function() {
                    $( "#switch-port" ).trigger( "chosen:updated" );
                });
        });




        $( "#vlan" ).on( 'change', function( event ) {

            setIPVx();

        });

        function setIPVx(){
            if( $("#vlan").val() ) {

                $( "#ipv4-address" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );
                $( "#ipv6-address" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );

                vlanid = $("#vlan").val();

                $.ajax( "<?= url( '/api/v4/vlan' )?>/" + vlanid + "/ipv-address" , {
                    data: {vliid: '', ipType: <?= \Entities\Router::PROTOCOL_IPV4 ?>  },
                    type: 'POST'
                })
                    .done( function( data ) {
                        var options = "<option value=\"\">Choose an IPv4</option>\n";
                        $.each( data.ipvList, function( key, value ){
                            options += "<option value=\"" + value.address + "\">" + value.address + " </option>\n";
                        });
                        $( "#ipv4-address" ).html( options );
                    })
                    .fail( function() {
                        throw new Error( "Error running ajax query for api/v4/vlan/$id/ipv-address" );
                        alert( "Error running ajax query for api/v4/vlan/$id/ipv-address" );
                    })
                    .always( function() {
                        $( "#ipv4-address" ).trigger( "chosen:updated" );
                    });


                $.ajax( "<?= url( '/api/v4/vlan' )?>/" + vlanid + "/ipv-address" , {
                    data: {vliid: '', ipType: <?= \Entities\Router::PROTOCOL_IPV6 ?>  },
                    type: 'POST'
                })
                    .done( function( data ) {
                        var options = "<option value=\"\">Choose an IPv6</option>\n";
                        $.each( data.ipvList, function( key, value ){
                            options += "<option value=\"" + value.address + "\">" + value.address + " </option>\n";
                        });
                        $( "#ipv6-address" ).html( options );
                    })
                    .fail( function() {
                        throw new Error( "Error running ajax query for api/v4/vlan/$id/ipv-address" );
                        alert( "Error running ajax query for api/v4/vlan/$id/ipv-address" );
                    })
                    .always( function() {
                        $( "#ipv6-address" ).trigger( "chosen:updated" );
                    });


            }
        }

        function randomString( length ) {
            var result = '';

            // if we do not have a cryptographically secure version of a PRNG, just alert and return
            if( window.crypto.getRandomValues === undefined ) {
                alert( 'No cryptographically secure PRNG available.' );
            } else {
                var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                var array = new Uint32Array(length);

                window.crypto.getRandomValues(array);
                for( var i = 0; i < length; i++ )
                    result += chars[ array[i] % chars.length ];
            }

            return result;
        }

        $( ".glyphicon-generator-ipv4" ).parent().on( 'click', function( e ) {
            $( "#ipv4-bgp-md5-secret" ).val( randomString( 12 ) );
        });

        $( ".glyphicon-generator-ipv6" ).parent().on( 'click', function( e ) {
            $( "#ipv6-bgp-md5-secret" ).val( $( "#ipv4-bgp-md5-secret" ).val().trim() === '' ? randomString( 12 ) : $( "#ipv4-bgp-md5-secret" ).val() );
        });

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

        /**
         * display or hide the fastlapc area
         */
        $( '#ipv4-enabled' ).change( function(){
            if( this.checked ){
                $( "#ipv4-area" ).slideDown();
            } else {
                $( "#ipv4-area" ).slideUp();
            }
        });

        /**
         * display or hide the fastlapc area
         */
        $( '#ipv6-enabled' ).change( function(){
            if( this.checked ){
                $( "#ipv6-area" ).slideDown();
            } else {
                $( "#ipv6-area" ).slideUp();
            }
        });

    </script>
<?php $this->append() ?>