<script>
    $(document).ready( function() {


        $( 'a[id|="cont-list-delete"]' ).off( 'click' ).on(  'click', function( event ) {

            event.preventDefault();
            let url = $(this).attr( "href" );

            let hasUser = $(this).attr( "data-hasuser" ) == "1" ? "The related user login account will also be removed." : "";

            bootbox.dialog({
                title: 'Delete Contact',
                message: "<p>Are you sure you want to delete this contact? " + hasUser + "</p>",
                buttons: {
                    cancel: {
                        label: "Cancel",
                        className: 'btn-primary',
                    },
                    ok: {
                        label: "Delete Contact",
                        className: 'btn-danger',
                        callback: function(){
                            document.location.href = url;
                        }
                    }
                }
            });

        });
    });
</script>