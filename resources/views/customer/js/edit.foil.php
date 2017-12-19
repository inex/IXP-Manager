<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:

    const input_name                = $( '#name' );
    const input_shortname           = $( '#shortname' );
    const input_abbreviated_name    = $( '#abbreviatedName' );
    const input_corp_www            = $( '#corpwww' );
    const input_autsys              = $( '#autsys' );

    const dd_type                   = $( '#type' );
    const dd_peering_policy         = $( '#peeringpolicy' );
    const cb_isResold               = $( '#isResold' );
    const div_reseller_area         = $( '#reseller-area' );
    const btn_populate              = $( '#btn-populate' );

    /**
     * set the colo_reference in empty input by the name input value
     */
    input_name.blur( function() {
        if( input_abbreviated_name.val() == '' ){
            input_abbreviated_name.val( input_name.val() );
        }
    });

    /**
     * display or hide form section depending on the type selected
     */
    dd_type.change( function( ){
        if( dd_type.val() == 2 ){  // associate member
            $( '.full-member-details' ).slideUp( 'fast' );
        } else {
            $( '.full-member-details' ).slideDown( 'fast' );
        }
    });

    <?php if( $t->resellerMode ): ?>
    $( "#isResold" ).change( function(){
        if( $( this ).prop( "checked" ) ) {
            $( '#reseller-area' ).show();
            if( $( '#isReseller' ).prop( "checked" ) ){
                $( '#isReseller' ).prop( "checked", false );
            }
        } else {
            $( '#reseller-area' ).hide();
        }
    });

    $( "#isReseller" ).change( function(){
        if( $( this ).prop( "checked" ) ) {
            if( $( '#isResold' ).prop( "checked" ) ){
                $( '#isResold' ).prop( "checked", false ).trigger( "change" );
            }
        }
    });


    <?php endif; ?>

    $(document).ready( function(){
        <?php if( $t->cust && $t->cust->getType() == \Entities\Customer::TYPE_ASSOCIATE ): ?>
        $( '.full-member-details' ).slideUp( 'fast' );
        <?php endif; ?>

        /**
         * Display the reseller dropdown if resold customer
         */
        if( cb_isResold.prop('checked') ) {
            div_reseller_area.show();
        }
    });


    /**
     * Display the reseller dropdown if resold customer
     */
    $( "#btn-populate" ).click( function(){
        pupulateFormViaAsn();
    });

    /**
     * Ajax request to fill the inputs depending on the ASN entered
     */
    function pupulateFormViaAsn() {
        if( $( "#asn-search" ).val() ){
            btn_populate.attr("disabled","disabled");
            $( '#error-message' ).remove();
            let url = " <?= url( "customer/populate-customer/asn") ?>/" + $( "#asn-search" ).val();
            $.ajax( url )
                .done( function( data ) {
                    if( !data.error ){
                        if( data.informations ){
                            // fill inputs with info received
                            input_name.val( data.informations.name );
                            input_shortname.val( data.informations.aka );
                            input_abbreviated_name.val( data.informations.name );
                            input_corp_www.val( data.informations.website );
                            input_autsys.val( data.informations.asn );
                            dd_peering_policy.val(  data.informations.policy_general.toLowerCase() ).trigger( "change" );
                        }
                    }else{
                        $( '#form' ).prepend( `<div id="error-message" class="alert alert-danger" role="alert"> ${data.informations.meta.error} </div>` );
                    }

                })
                .fail( function() {
                    alert( "Error running ajax query for " + url );
                    throw "Error running ajax query for " + url;
                })
                .always( function() {
                    btn_populate.attr("disabled", false);
                });
        }
    }
</script>