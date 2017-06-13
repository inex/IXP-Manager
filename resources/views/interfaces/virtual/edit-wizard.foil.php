<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'virtualInterface/list' )?>">(Virtual) Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Add Interface Wizard</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class=" btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-plus"></i> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a id="" href="<?= url( '/virtualInterface/add-wizard' )?>" >
                        Add Interface Wizard...
                    </a>
                </li>
                <li>
                    <a id="" href="<?= url( '/virtualInterface/add' )?>" >
                        Virtual Interface Only...
                    </a>
                </li>
            </ul>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?= $t->alerts() ?>
    <div class="well">
        <?= Former::open()->method( 'POST' )
            ->action( url( 'virtualInterface/storeWizard' ) )
            ->customWidthClass( 'col-sm-7' )
        ?>
        <div class="col-sm-4">
            <h3>
                General Interface Settings
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
                ->label( 'Use 802.1q framing' )
                ->blockHelp( 'Indicates if operators / provisioning systems should configure this port with 802.1q framing / tagged packets.' )
                ->check( $t->vi ? $t->vi->getTrunk() : false )
            ?>

            <?= Former::checkbox( 'ipv4-enabled' )
                ->label( 'IPv4 Enabled' )
                ->blockHelp( ' ' )

            ?>

            <?= Former::checkbox( 'ipv6-enabled' )
                ->label( 'IPv6 Enabled' )
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
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'status' )
                ->label( 'Status' )
                ->fromQuery( $t->status, 'name' )
                ->placeholder( 'Choose a status' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
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
                ->blockHelp( 'help text' );
            ?>

            <?= Former::checkbox( 'irrdbfilter' )
                ->label( 'Apply IRRDB Filtering' )
                ->blockHelp( ' ' )
                ->check( true )
            ?>

            <?= Former::checkbox( 'mcastenabled' )
                ->label( 'Multicast Enabled' )
                ->blockHelp( ' ' )
            ?>

            <?= Former::checkbox( 'rsclient' )
                ->label( 'Route Server Client' )
                ->blockHelp( ' ' )
            ?>

            <?= Former::checkbox( 'as112client' )
                ->label( 'AS112 Client' )
                ->blockHelp( ' ' )

            ?>
        </div>

        <br/>
        <div id='ipv4-area' class="col-sm-5" style="display: none">
            <h3>
                IPv4 Details
            </h3>
            <hr>
            <?= Former::select( 'ipv4-address' )
                ->label( 'IPv4 Address' )
                ->placeholder( 'Choose IPv4' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>
            <?= Former::text( 'ipv4-hostname' )
                ->label( 'IPv4 Hostname' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::text( 'ipv4-bgp-md5-secret' )
                ->label( 'IPv4 BGP MD5 Secret' )
                ->appendIcon( 'generator-ipv4 glyphicon glyphicon-refresh' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::checkbox( 'ipv4-can-ping' )
                ->label( 'IPv4 Can Ping' )
                ->blockHelp( ' ' )
            ?>

            <?= Former::checkbox( 'ipv4-monitor-rcbgp' )
                ->label( 'IPv4 Monitor RC BGP' )
                ->blockHelp( ' ' )
            ?>
        </div>

        <div id='ipv6-area' class="col-sm-5" style="display: none">
            <h3>
                IPv6 Details
            </h3>
            <hr>
            <?= Former::select( 'ipv6-address' )
                ->label( 'IPv6 Address' )
                ->placeholder( 'Choose IPv6' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>
            <?= Former::text( 'ipv6-hostname' )
                ->label( 'IPv6 Hostname' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::text( 'ipv6-bgp-md5-secret' )
                ->label( 'IPv6 BGP MD5 Secret' )
                ->appendIcon( 'generator-ipv6 glyphicon glyphicon-refresh' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::checkbox( 'ipv6-can-ping' )
                ->label( 'IPv6 Can Ping' )
                ->blockHelp( ' ' )
            ?>

            <?= Former::checkbox( 'ipv6-monitor-rcbgp' )
                ->label( 'IPv6 Monitor RC BGP' )
                ->blockHelp( ' ' )
            ?>
        </div>

        <?= Former::hidden( 'id' )
            ->value( $t->vi ? $t->vi->getId() : null )
        ?>

        <?=Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( url( 'virtualInterface/list/' ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->id('btn-group');?>

        <?= Former::close() ?>
    </div>

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