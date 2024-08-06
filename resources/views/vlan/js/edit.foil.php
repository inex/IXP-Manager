<script>
    $( document ).ready( function() {
        $( "#notes" ).parent().removeClass().addClass( "col-sm-12" )

        // private should automatically mean do not export to IX-F and vice versa:
        // const originalExportToIxfStatus = $("#export_to_ixf").prop('checked');
        if( $( "#private" ).prop( 'checked' ) ) {
            $("#export_to_ixf").prop( 'checked', false );
            $("#export_to_ixf").attr( 'disabled', true );
        }

        $( "#private" ).on( 'change', function( e ) {
            if( $( "#private" ).prop( 'checked' ) ) {
                $("#export_to_ixf").prop( 'checked', false );
                $("#export_to_ixf").attr( 'disabled', true );
            } else {
                $("#export_to_ixf").removeAttr( 'disabled' );
                $("#export_to_ixf").prop( 'checked', true );
            }
        })

    });

</script>
