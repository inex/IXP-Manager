<script>

    $( document ).ready(function() {
        $( ".radio-button" ).click(function() {
            $( ".radio-button" ).prop( "checked", false );
            $( this ).prop( "checked", true );
            $( "#existingUserId" ).val( $( this ).val() )

        });
    });


</script>