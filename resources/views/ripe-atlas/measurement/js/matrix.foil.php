<script>
    $( 'document' ).ready( function(){

        $( "#table-am" ).show();

        let columnClicked   = false;

        /**
         * Highlight the column
         */
        $( "#table-am" ).on( 'mouseover mouseout', '.td-hover', function( event ) {
            if( columnClicked ) return;

            let xasn = $(this).attr( "data-asn-x" );
            let yasn = $(this).attr( "data-asn-y" );

            $( '.cell-y-' + yasn ).toggleClass( "highlight" );
            $( '.cell-x-' + xasn ).toggleClass( "highlight" );
            $( '.td-x-' + xasn ).toggleClass( "highlight2" );
            $( '.td-y-' + yasn ).toggleClass( "highlight2" );
        });

        /**
         * Highlight the column
         */
        $( "#table-am" ).on( 'mouseover mouseout', '.cell-hover', function( event ) {

            if( columnClicked ) return;

            let yasn = $(this).attr( "data-cust-asn" );

            $( '.cell-hover-' + yasn ).toggleClass( "highlight" );
        });
    });
</script>