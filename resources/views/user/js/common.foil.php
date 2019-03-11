<script>
    $(document).on('click', ".delete-cu2" ,function(e){
        e.preventDefault();

        let custid = $(this).attr( "data-customer" );
        let userid = $(this).attr( "data-user" );

        let urlDelete  = "<?= url( 'user' ) ?>/" + userid + "/delete-customer/" + custid;

        bootbox.confirm({
            message: `Do you really want to remove the association user/customer ?` ,
            buttons: {
                cancel: {
                    label: 'Cancel',
                    className: 'btn-primary'
                },
                confirm: {
                    label: 'Remove',
                    className: 'btn-danger'
                }
            },
            callback: function ( result ) {
                if( result) {
                    $.ajax( urlDelete ,{
                        type : 'POST'
                    })
                        .done( function( data ) {
                            window.location.href = "<?= route( 'user@list' ) ?>";
                        })
                        .fail( function(){
                            throw new Error( `Error running ajax query for ${urlDelete}` );
                        })
                }
            }
        });

    });
</script>