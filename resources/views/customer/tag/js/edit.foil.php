<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const input_tag                 = $( '#tag' );
    const input_display_as          = $( '#display_as' );

    $(document).ready( function(){
        // Copy the tag in the input display_as if empty
        input_tag.focusout(function() {
            if( input_display_as.val() === '' ){
                input_display_as.val( input_tag.val() );
            }
        });
    });
</script>
