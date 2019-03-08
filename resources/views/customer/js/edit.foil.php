<?php /** @var Foil\Template\Template $t */ ?>
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
}).trigger('change');

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

    // Display the reseller dropdown if resold customer
    if( cb_isResold.prop('checked') ) {
        div_reseller_area.show();
    }

});


/**
 * Ajax request to fill the inputs depending on the ASN entered
 */
function populateFormViaAsn() {

    const input_asn_search = $( '#asn-search' );

    if( input_asn_search.val() && /^\s*\d+\s*$/.test( input_asn_search.val() ) ) {

        let peering_policy = '';
        let url = " <?= url( "api/v4/customer/query-peeringdb/asn" ) ?>/" + input_asn_search.val().trim();

        btn_populate.attr( "disabled", "disabled" );
        $( '#error-message' ).remove();
        $( '.form-group' ).removeClass( 'has-success' );

        $.ajax( url )

            .done( function( response ) {
                if( typeof response.net !== "undefined" ) {

                    // fill inputs with info received
                    input_name.val(             htmlEntities( response.net.name ) ).closest( 'div.form-group' ).addClass( 'has-success' );
                    input_abbreviated_name.val( htmlEntities( response.net.name ) ).closest( 'div.form-group' ).addClass( 'has-success' );
                    input_shortname.val(        htmlEntities( response.net.name ).replace( /[^a-zA-Z0-9]+/g, "" ).toLowerCase().substr( 0, 10 ) ).closest( 'div.form-group' ).addClass( 'has-success' );
                    input_datejoin.val(         getCurrentDate() ).closest( 'div.form-group' ).addClass( 'has-success' );
                    input_corp_www.val(         htmlEntities( response.net.website ) ).closest( 'div.form-group' ).addClass( 'has-success' );
                    input_autsys.val(           htmlEntities( response.net.asn ) ).closest( 'div.form-group' ).addClass( 'has-success' );
                    input_peeringmacro.val(     htmlEntities( response.net.irr_as_set ) ).closest( 'div.form-group' ).addClass( 'has-success' );

                    if( response.net.info_prefixes4 !== "undefined" ) {
                        input_maxprefixes.val( Math.ceil( htmlEntities( response.net.info_prefixes4 ) * 1.2 ) ).closest( 'div.form-group' ).addClass( 'has-success' );
                    }

                    dd_peering_policy.val(  htmlEntities( response.net.policy_general ).toLowerCase() ).trigger( "change" ).closest( 'div.form-group' ).addClass( 'has-success' );

                    if( response.net.poc_set !== "undefined" ) {
                        $.each( response.net.poc_set, function( key, noc ) {
                            if( noc.role.toUpperCase() === "NOC" ) {
                                if( noc.phone !== "undefined" ){
                                    input_nocphone.val( htmlEntities( noc.phone ) ).closest( 'div.form-group' ).addClass( 'has-success' );
                                }
                                if( noc.email !== "undefined" ) {
                                    input_nocemail.val( htmlEntities( noc.email ) );
                                    input_peeringemail.val( htmlEntities( noc.email ) ).closest( 'div.form-group' ).addClass( 'has-success' );
                                }
                            }
                        });
                    }

                    switch ( response.net.policy_general ) {
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

                    dd_peering_policy.val( peering_policy ).trigger( "change" ).closest( 'div.form-group' ).addClass( 'has-success' );

                } else {

                    $( '#form' ).prepend( `<div id="error-message" class="alert alert-danger" role="alert"> ${response.error} </div>` );
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

/**
 * Get date as Y-m-d
 * @returns {string}
 */
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
