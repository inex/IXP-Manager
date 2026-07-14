<script>

    $( document ).ready( function() {

        function showJsonModal(rawJson) {
            let formattedJson;

            try {
                formattedJson = JSON.stringify(JSON.parse(rawJson), null, 2);
            } catch (e) {
                // Fallback to raw string if parsing fails
                formattedJson = rawJson;
            }

            const content = $('<pre>').text(formattedJson);

            bootbox.dialog({
                message: content,
                size: 'large',
                title: "View Json",
                buttons: {
                    cancel: {
                        label: 'Close',
                        className: 'btn-secondary',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;
                        }
                    }
                }
            });
        }

        /**
         * Display in a readable way a json value
         */
        $( '.json-view' ).on( 'click', function( event ) {
            event.preventDefault();

            const element = $(this);
            const dataType = element.attr('data-type');
            const dataValue = element.attr('data-value');

            if( dataType === 'DB' ) {
                // dataValue is the raw JSON to be displayed
                showJsonModal(dataValue);
            } else {
                $.ajax( dataValue )
                    .done( function( data ) {
                        showJsonModal( data.response );
                    })
                    .fail( function() {
                        bootbox.alert( "Error running ajax query for " + dataValue );
                        throw new Error( "Error running ajax query for " + dataValue );
                    });
            }
        } );
    });
</script>
