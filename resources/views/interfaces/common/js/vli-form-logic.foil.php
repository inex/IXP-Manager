<script>

//////////////////////////////////////////////////////////////////////////////////////
// we'll need these handles to html elements in a few places:

const cb_ipv6_enabled = $( '#ipv6-enabled' );
const cb_ipv4_enabled = $( '#ipv4-enabled' );
const dd_ipv6         = $( "#ipv6-address" );
const dd_ipv4         = $( "#ipv4-address" );
const dd_vlan         = $( "#vlan" );
const div_ipv6        = $( "#ipv6-area" );
const div_ipv4        = $( "#ipv4-area" );

//////////////////////////////////////////////////////////////////////////////////////
// action bindings:

dd_ipv6.select2({ tags: true, width: '100%', allowClear: true, placeholder: "Select an IPv6 address..." }).on( 'change', usedAcrossVlans );
dd_ipv4.select2({ tags: true, width: '100%', allowClear: true, placeholder: "Select an IPv4 address..." }).on( 'change', usedAcrossVlans );

cb_ipv6_enabled.change( () => { cb_ipv6_enabled.is(":checked") ? div_ipv6.slideDown() : div_ipv6.slideUp() } );
cb_ipv4_enabled.change( () => { cb_ipv4_enabled.is(":checked") ? div_ipv4.slideDown() : div_ipv4.slideUp() } );

dd_vlan.on( 'change', updateIpAddresses );

$( ".glyphicon-generator-ipv6" ).parent().on( 'click', updateMD5 );
$( ".glyphicon-generator-ipv4" ).parent().on( 'click', updateMD5 );



//////////////////////////////////////////////////////////////////////////////////////
// initial states:

// populate IP addresses if VLAN is already set
if( dd_vlan.val() !== null ) { updateIpAddresses() }

</script>
