<script>

//////////////////////////////////////////////////////////////////////////////////////
// we'll need these handles to html elements in a few places:

const input_name                = $( '#name' );
const input_shortname           = $( '#shortname' );
const input_abbreviated_name    = $( '#abbreviatedName' );
const input_corp_www            = $( '#corpwww' );
const input_autsys              = $( '#autsys' );
const input_datejoin            = $( '#datejoin' );
const input_maxprefixes         = $( '#maxprefixes' );
const input_nocphone            = $( '#nocphone' );
const input_nocemail            = $( '#nocemail' );
const input_peeringemail        = $( '#peeringemail' );
const input_peeringmacro        = $( '#peeringmacro' );

const dd_type                   = $( '#type' );
const dd_peering_policy         = $( '#peeringpolicy' );

const cb_isResold               = $( '#isResold' );
const div_reseller_area         = $( '#reseller-area' );
const btn_populate              = $( '#btn-populate' );

/**
 * Set the abbreviated name (if empty) when the name is set:
 */
input_name.blur( () => { if( input_abbreviated_name.val() === '' ) { input_abbreviated_name.val( input_name.val() ); } });

/**
 * display or hide form section depending on the type selected
 */
dd_type.change( () => {
    if( dd_type.val() === "<?= Entities\Customer::TYPE_ASSOCIATE ?>" ) {  // associate member
        $( '.full-member-details' ).slideUp( 'fast' );
    } else {
        $( '.full-member-details' ).slideDown( 'fast' );
    }
});

/**
 * Handler for button request to populate from PeeringDB
 */
btn_populate.on( 'click', populateFormViaAsn );



<?php if( $t->resellerMode() ): ?>

/*
 * ------------------------------------------------
 * Reseller mode is enabled: {
 */

const cb_isReseller             = $( '#isReseller' );

cb_isResold.change( function(){
    if( $( this ).prop( "checked" ) ) {
        $( '#reseller-area' ).show();
        if( cb_isReseller.prop( "checked" ) ) {
            cb_isReseller.prop( "checked", false );
        }
    } else {
        $( '#reseller-area' ).hide();
    }
});

cb_isReseller.change( function() {
    if( $( this ).prop( "checked" ) ) {
        if( cb_isResold.prop( "checked" ) ) {
            cb_isResold.prop( "checked", false ).trigger( "change" );
        }
    }
});

/*
 * Reseller mode is enabled }
 * ------------------------------------------------
 */

<?php endif; ?>


$(document).ready( function(){

    <?php if( $t->cust && $t->cust->isTypeAssociate() ): ?>
    $( '.full-member-details' ).slideUp( 'fast' );
    <?php endif; ?>

    /*
     * Display the reseller dropdown if resold customer
     */
    if( cb_isResold.prop('checked') ) {
        div_reseller_area.show();
    }

});


/**
 * Ajax request to fill the inputs depending on the ASN entered
 */
function populateFormViaAsn() {
    const input_asn_search = $( '#asn-search' );

    if( input_asn_search.val() ){
        let error = '';
        let peering_policy = '';

        btn_populate.attr("disabled", "disabled" );

        $( '#error-message' ).remove();
        let url = " <?= url( "customer/populate-customer/asn") ?>/" + input_asn_search.val();
        $.ajax( url )
            .done( function( data ) {
                if( !data.error ){
                    if( data.informations ){
                        $('#form').trigger("reset");
                        // fill inputs with info received
                        input_name.val( data.informations.name );
                        input_shortname.val( data.informations.name.replace(/[^a-zA-Z0-9]+/g, "").toLowerCase() );
                        input_datejoin.val( getCurrentDate() );

                        if( data.informations.info_prefixes4 !== "undefined" ){
                            input_maxprefixes.val( Math.round(data.informations.info_prefixes4 * 1.2 ) );
                        }

                        input_abbreviated_name.val( data.informations.name );
                        input_corp_www.val( data.informations.website );
                        input_autsys.val( data.informations.asn );
                        input_peeringmacro.val( data.informations.irr_as_set );
                        dd_peering_policy.val(  data.informations.policy_general.toLowerCase() ).trigger( "change" );

                        if( data.informations.poc_set !== "undefined" ){
                            $.each( data.informations.poc_set, function( key, noc ) {
                                if( noc.role === "NOC"){
                                    if( noc.phone !== "undefined" ){
                                        input_nocphone.val( noc.phone );
                                    }
                                    if( noc.email !== "undefined" ){
                                        input_nocemail.val( noc.email );
                                        input_peeringemail.val( noc.email );
                                    }

                                }

                            });
                        }


                        switch ( data.informations.policy_general ) {
                            case "Open":
                                peering_policy = "open";
                                break;
                            case "Selective":
                                peering_policy = "selective";
                                break;
                            case "No":
                                peering_policy = "closed";
                                break;
                        }

                        dd_peering_policy.val( peering_policy ).trigger( "change" );
                    }
                }else{

                    if( data.informations.meta !== undefined ){
                        error = data.informations.meta.error;
                    } else {
                        error = data.informations;
                    }
                    $( '#form' ).prepend( `<div id="error-message" class="alert alert-danger" role="alert"> ${error} </div>` );
                }

            })
            .fail( function() {
                alert( "Error running ajax query for " + url );
                throw "Error running ajax query for " + url;
            })
            .always( function() {
                btn_populate.attr("disabled", false );
            });
    }
}

function getCurrentDate(){
    let now = new Date();
    let month = (now.getMonth() + 1);
    let day = now.getDate();
    if (month < 10){
        month = "0" + month;
    }

    if (day < 10){
        day = "0" + day;
    }

    return now.getFullYear() + '-' + month + '-' + day;
}

</script>
