<script>

    /**
     * Disable submiting form via pressing 'enter' key
     */
    $('#2fa input').on('keypress', function(e) {
        return e.which !== 13;
    });


    $( "#2fa" ).on( 'submit', function( event ) {

        console.log( $(this).find("input[type=submit]" ) );
        let btn_id = $(this).find("input[type=submit][clicked=true]" ).attr( "id" );
        let url = "<?= route( "2fa@check-password" ) ?>";

        if( btn_id == "btn-delete2fa" ) {
            url = "<?= route( "2fa@delete" ) ?>";
        } else if( btn_id == "btn-reset2fa" ){
            url = "<?= route( "2fa@reset" ) ?>";
        }

        $(this).attr( 'action', url );

        return true;
    });

</script>
