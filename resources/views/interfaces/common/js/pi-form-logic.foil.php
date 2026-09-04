<script type="module">
    $(document).ready(function() {
        //////////////////////////////////////////////////////////////////////////////////////
        // Element handles:
        const dd_pswitch = $( "#switch" );
        const dd_fswitch = $( "#switch-fanout" );

        //////////////////////////////////////////////////////////////////////////////////////
        // Action bindings:
        dd_pswitch.on( 'change', updateSwitchPort );
        dd_fswitch.on( 'change', updateSwitchPort );

        //////////////////////////////////////////////////////////////////////////////////////
        // Initial states:
        if( dd_pswitch.val() !== null && dd_pswitch.val() !== '' ) { dd_pswitch.change(); }
        if( dd_fswitch.val() !== null && dd_fswitch.val() !== '' ) { dd_fswitch.change(); }
    });
</script>