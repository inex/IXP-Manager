<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'interfaces/vlan/list' )?>">Vlan Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Edit Vlan Interface</li>
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

<?= $t->alerts() ?>
    <div class="well">
        <?= Former::open()->method( 'POST' )
            ->action( action( 'Interfaces\VlanInterfaceController@store' ) )
            ->customWidthClass( 'col-sm-6' )
        ?>
        <div class="col-sm-6">
            <h3>
                General Vlan Settings
            </h3>
            <hr>

            <?= Former::select( 'vlan' )
                ->label( 'Vlan' )
                ->fromQuery( $t->vlan, 'name' )
                ->placeholder( 'Choose a Vlan' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::checkbox( 'irrdbfilter' )
                ->label( 'Apply IRRDB Filtering' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( ' ' )
            ?>

            <?= Former::checkbox( 'mcastenabled' )
                ->label( 'Multicast Enabled' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( ' ' )
            ?>

            <?= Former::checkbox( 'ipv4-enabled' )
                ->label( 'IPv4 Enabled' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( ' ' )

            ?>

        </div>

        <div class="col-sm-6">
            <br>
            <br>
            <br>
            <?= Former::number( 'maxbgpprefix' )
                ->label( 'Max BGP Prefixes' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::checkbox( 'rsclient' )
                ->label( 'Route Server Client' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( ' ' )
            ?>

            <?php if( $t->as112UiActive ): ?>
                <?= Former::checkbox( 'as112client' )
                    ->label( 'AS112 Client' )
                    ->unchecked_value( 0 )
                    ->value( 1 )
                    ->blockHelp( ' ' )
                ?>
            <?php endif; ?>


            <?= Former::checkbox( 'busyhost' )
                ->label( 'Busy host' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( ' ' )

            ?>

            <?= Former::checkbox( 'ipv6-enabled' )
                ->label( 'IPv6 Enabled' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( ' ' )
            ?>
        </div>

        <br/>
        <div id='ipv4-area' class="col-sm-6" style="display: none;float: left">
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
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( ' ' )
            ?>

            <?= Former::checkbox( 'ipv4-monitor-rcbgp' )
                ->label( 'IPv4 Monitor RC BGP' )
                ->unchecked_value( 0 )
                ->value( 1 )
                ->blockHelp( ' ' )
            ?>
        </div>

        <div id='ipv6-area' class="col-sm-6" style="display: none;float: right">
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
            ->value( $t->vli ? $t->vli->getId() : null )
        ?>

        <?= Former::hidden( 'viid' )
            ->value( $t->vli ? $t->vli->getVirtualInterface()->getId() : $t->vi->getId())
        ?>

        <?= Former::hidden( 'vi-redirect' )
            ->value( $t->vi ? true : false )
        ?>

        <?=Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( $t->vi ? route(  'interfaces/virtual/edit' , [ 'id' => $t->vi->getId() ] ) :  route( 'interfaces/vlan/list' ) ),
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

        $( "#vlan" ).on( 'change', function( event ) {
            setIPVx();
        });

        function setIPVx(){
            if( $("#vlan").val() ) {

                $( "#ipv4-address" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );
                $( "#ipv6-address" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );

                vlanid = $("#vlan").val();

                $.ajax( "<?= url( '/api/v4/vlan' )?>/" + vlanid + "/ip-addresses" )
                    .done( function( data ) {
                        var options = "<option value=\"\">Choose an IPv4</option>\n";
                        $.each( data.ipv4, function( key, value ){
                            options += "<option value=\"" + value.address + "\">" + value.address + " </option>\n";
                        });
                        $( "#ipv4-address" ).html( options );

                        <?php if( $t->vli && $t->vli->getIpv4enabled() && $t->vli->getIPv4Address()) :?>
                            $('#ipv4-address').val('<?= $t->vli->getIPv4Address()->getAddress() ?>');
                        <?php endif; ?>



                        $.each( data.ipv6, function( key, value ){
                            options += "<option value=\"" + value.address + "\">" + value.address + " </option>\n";
                        });
                        $( "#ipv6-address" ).html( options );

                        <?php if( $t->vli && $t->vli->getIpv6enabled() && $t->vli->getIPv6Address()) :?>
                            $('#ipv6-address').val('<?= $t->vli->getIPv6Address()->getAddress() ?>');
                        <?php endif; ?>

                    })
                    .fail( function() {
                        throw new Error( "Error running ajax query for api/v4/vlan/$id/ip-addresses" );
                        alert( "Error running ajax query for api/v4/vlan/$id/ip-addresses" );
                    })
                    .always( function() {
                        $( "#ipv4-address" ).trigger( "chosen:updated" );
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