<script>

//////////////////////////////////////////////////////////////////////////////////////
// we'll need these handles to html elements in a few places:
const dd_pswitch      = $( "#switch" );
const dd_fswitch      = $( "#switch-fanout" );

//////////////////////////////////////////////////////////////////////////////////////
// action bindings:
dd_pswitch.on( 'change', updateSwitchPort );
dd_fswitch.on( 'change', updateSwitchPort );

//////////////////////////////////////////////////////////////////////////////////////
// initial states:

// populate switch ports dropdown if switch is already set
if( dd_pswitch.val() !== null ) { dd_pswitch.change(); }
if( dd_fswitch.val() !== null ) { dd_fswitch.change(); }

</script>