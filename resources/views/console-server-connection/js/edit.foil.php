<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const cb_autobaud         = $( "#autobaud" );
    const div_autobaud        = $( "#autobaud-section" );

    $(document).ready(function(){
        cb_autobaud.change( );
        $( "#notes" ).parent().removeClass().addClass( "col-sm-12" )
    });

    /**
     * display or hide the autobaud area
     */
    cb_autobaud.change( function(){
        this.checked ? div_autobaud.slideUp() : div_autobaud.slideDown();
    });
</script>