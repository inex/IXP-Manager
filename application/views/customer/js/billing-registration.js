
$( "#billingAddress1" ).removeClass( "span6" ).addClass("span10");
$( "#billingAddress1" ).wrap( '<div class="input-append"></div>' );
$( "#billingAddress1" ).after( '<button title="Copy address from registration details" class="btn" type="button" id="btnBillingAddress1Copy"><i class="icon-retweet"></i></button>');
$( "#btnBillingAddress1Copy" ).on( 'click', function( e ) {
    $( "#billingAddress1" ).val( $( "#address1" ).val() );
    $( "#billingAddress2" ).val( $( "#address2" ).val() );
    $( "#billingAddress3" ).val( $( "#address3" ).val() );
    $( "#billingTownCity" ).val( $( "#townCity" ).val() );
    $( "#billingPostcode" ).val( $( "#postcode" ).val() );

    if( $( "#country" ).val() ) {
        $( "#billingCountry" ).val( $( "#country" ).val() );
        $( "#billingCountry" ).trigger( "chosen:updated" );
    }
});
