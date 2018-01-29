<script>
    $(document).ready( function() {

        $( 'a[id|="usr-list-delete"]' ).off( 'click' ).on(  'click', function( event ) {

            event.preventDefault();
            url = $(this).attr( "href" );

            bootbox.dialog({
                title: 'Delete User',
                message: "<p>Are you sure you want to delete this contact?</p>",
                buttons: {
                    cancel: {
                        label: "Cancel",
                        className: 'btn-primary',
                    },
                    noclose: {
                        label: "Remove User Access Only",
                        className: 'btn-danger',
                        callback: function(){
                            document.location.href = url + "/useronly/1";
                        }
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