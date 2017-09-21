<script>

    $(document).ready( function() {

        $( 'label.col-lg-2' ).removeClass('col-lg-2');
        $( '.input-group-addon' ).addClass('btn btn-default');

        setIPVx();

        $( "#ipv4-address" ).select2({
            tags: true,
            width: '100%'
        });

        $( "#ipv6-address" ).select2({
            tags: true,
            width: '100%'
        });

        if( $( '#ipv4-enabled' ).is(":checked") ) {
            $( "#ipv4-area" ).slideDown();
        }
        if( $( '#ipv6-enabled' ).is(":checked") ) {
            $( "#ipv6-area" ).slideDown();
        }

        $( "#ipv4-address" ).on( 'change', usedAcrossVlans );
        $( "#ipv6-address" ).on( 'change', usedAcrossVlans );
    });

    $( "#vlan" ).on( 'change', setIPVx );

    /**
     * display or hide the ipv4 area
     */
    $( '#ipv4-enabled' ).change( function() {
        if( this.checked ){
            $( "#ipv4-area" ).slideDown();
        } else {
            $( "#ipv4-area" ).slideUp();
        }
    });

    /**
     * display or hide the ipv6 area
     */
    $( '#ipv6-enabled' ).change( function(){
        if( this.checked ){
            $( "#ipv6-area" ).slideDown();
        } else {
            $( "#ipv6-area" ).slideUp();
        }
    });

    function setIPVx()
    {
        let vlanid = $("#vlan").val();

        if( vlanid ) {
            $( '.ip-is-used-alert').html( '' ).hide();
            let ipv4dd = $( "#ipv4-address" );
            let ipv6dd = $( "#ipv6-address" );

            ipv4dd.html( "<option value=\"\">Loading, please wait...</option>\n" ).trigger( "changed" );
            ipv6dd.html( "<option value=\"\">Loading, please wait...</option>\n" ).trigger( "changed" );

            $.ajax( "<?= url( '/api/v4/vlan' )?>/" + vlanid + "/ip-addresses" )
                .done( function( data ) {
                    let options = "<option value=\"\">Choose an IPv4 address...</option>\n";
                    $.each( data.ipv4, function( key, value ) {

                        // check if we have to include the IP associated to the vlan interface ( edit mode ) in order to display it in the dropdown
                        let includeIPv4 = false;

                        <?php if( $t->vli && $t->vli->getIpv4enabled() && $t->vli->getIPv4Address() ) :?>

                        let ipv4Id = "<?= $t->vli->getIPv4Address()->getId() ?>";

                        if( ipv4Id === value.id && !$( "#duplicated" ).val() ){
                            includeIPv4 = true;
                        }

                        <?php endif; ?>

                        if( value.vli_id === null || includeIPv4 ) {
                            options += "<option value=\"" + value.address + "\">" + value.address + " </option>\n";
                        }
                    });

                    ipv4dd.html( options );

                    <?php if( $t->vli && $t->vli->getIpv4enabled() && $t->vli->getIPv4Address()) :?>
                    ipv4dd.val('<?= $t->vli->getIPv4Address()->getAddress() ?>');
                    <?php endif; ?>


                    options = "<option value=\"\">Choose an IPv6 address...</option>\n";
                    $.each( data.ipv6, function( key, value ){

                        // check if we have to include the IP associated to the vlan interface ( edit mode ) in order to display it in the dropdown
                        let includeIPv6 = false;

                        <?php if( $t->vli && $t->vli->getIpv6enabled() && $t->vli->getIPv6Address() ) :?>

                        let ipv6Id = "<?= $t->vli->getIPv6address()->getId() ?>";
                        if( ipv6Id === value.id){
                            includeIPv6 = true;
                        }

                        <?php endif; ?>

                        if( value.vli_id === null || includeIPv6 ) {
                            options += "<option value=\"" + value.address + "\">" + value.address + " </option>\n";
                        }
                    });
                    ipv6dd.html( options );

                    <?php if( $t->vli && $t->vli->getIpv6enabled() && $t->vli->getIPv6Address()) :?>
                    ipv6dd.val('<?= $t->vli->getIPv6Address()->getAddress() ?>');
                    <?php endif; ?>

                })
                .fail( function() {
                    alert( "Error running ajax query for api/v4/vlan/$id/ip-addresses" );
                    throw new Error( "Error running ajax query for api/v4/vlan/$id/ip-addresses" );
                })
                .always( function() {
                    ipv4dd.trigger( "changed" );
                    ipv6dd.trigger( "changed" );
                });
        }
    }

</script>