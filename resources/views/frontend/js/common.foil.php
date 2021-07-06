<script>

    $( document ).ready( function() {

        /**
         * Display in a readable way a json value
         */
        $( '.json-view' ).on( 'click', function( event ) {
            event.preventDefault();

            let url, json;

            if( $( this ).attr( "data-type" ) == 'DB' ) {
                json = $( this ).attr( "data-value" );
                let html = '<pre>' + JSON.stringify( JSON.parse( json ) , null, 2 ) + '</pre>';

                callPopup( html );

            } else {
                url = $( this ).attr( "data-value" );

                $.ajax( url )
                    .done( function( data ) {
                        json = data.response;

                        let html = '<pre>' + JSON.stringify( JSON.parse( json ) , null, 2 ) + '</pre>';

                        callPopup( html );

                    })
                    .fail( function(){
                        bootbox.alert( "Error running ajax query for " + url );
                        throw new Error( "Error running ajax query for " + url );
                    })
                    .always( function() {

                    });

            }




        } );

        function callPopup( html ) {

            bootbox.dialog({
                message: html,
                size: 'large' ,
                title: "View Json",
                buttons: {
                    cancel: {
                        label: 'Close',
                        className: 'btn-secondary',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;
                        }
                    },
                }
            });

        }
    });
</script>