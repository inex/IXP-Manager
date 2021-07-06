<script>

    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:

    const cb_ipv6_enabled     = $( '#ipv6enabled' );
    const cb_ipv4_enabled     = $( '#ipv4enabled' );
    const cb_irrdbfilter      = $( '#irrdbfilter' );
    const dd_ipv6             = $( "#ipv6address" );
    const dd_ipv4             = $( "#ipv4address" );
    const dd_vlan             = $( "#vlanid" );
    const div_ipv6            = $( "#ipv6-area" );
    const div_ipv4            = $( "#ipv4-area" );
    const div_rsmorespecifics = $( "#div-rsmorespecifics" );

    // array of AJAX requests for we can execute other code when() then complete
    let ajaxRequests = [];

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    dd_ipv6.on( 'change', usedAcrossVlans );
    dd_ipv4.on( 'change', usedAcrossVlans );

    cb_ipv6_enabled.change( () => { cb_ipv6_enabled.is(":checked") ? div_ipv6.slideDown()            : div_ipv6.slideUp()            } );
    cb_ipv4_enabled.change( () => { cb_ipv4_enabled.is(":checked") ? div_ipv4.slideDown()            : div_ipv4.slideUp()            } );
    cb_irrdbfilter.change(  () => { cb_irrdbfilter.is(":checked")  ? div_rsmorespecifics.slideDown() : div_rsmorespecifics.slideUp() } );

    dd_vlan.on( 'change', updateIpAddresses );

    $( ".glyphicon-generator-ipv6" ).parent().click( updateMD5 );
    $( ".glyphicon-generator-ipv4" ).parent().click( updateMD5 );

    //////////////////////////////////////////////////////////////////////////////////////
    // initial states:

    // populate IP addresses if VLAN is already set
    if( dd_vlan.val() !== null ) { updateIpAddresses() }

    cb_irrdbfilter.trigger( 'change' );

</script>
