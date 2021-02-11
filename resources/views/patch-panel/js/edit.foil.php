<script>
    $( document ).ready( function() {
        //////////////////////////////////////////////////////////////////////////////////////
        // we'll need these handles to html elements in a few places:
        const input_name              = $( '#name' );
        const input_colo_ref          = $( '#colo_reference' );

        /**
         * set the today date on click on the today button
         */
        $( "#date-today" ).click( () => { $( "#installation_date" ).val( '<?= date( "Y-m-d" ) ?>' ) } );

        /**
         * set the colo_reference in empty input by the name input value
         */
        input_name.blur( function() {
            if( input_colo_ref.val() === '' ){
                input_colo_ref.val( input_name.val() );
            }
        });

        /**
         * set data to the tooltip
         */
        $( "#icon-nb-port" ).parent().attr( 'data-toggle','popover' ).attr( 'title' , 'Help - Number of Ports' ).attr( 'data-content' , '<b>Note that duplex ports should be entered as two ports.</b>' );

        /**
         * configuration of the tooltip
         */
        $( "[data-toggle=popover]" ).popover( { placement: 'left',container: 'body', html: true, trigger: "hover" } );

        $( "#location_notes" ).parent().removeClass().addClass( "col-sm-12" )
    });
</script>