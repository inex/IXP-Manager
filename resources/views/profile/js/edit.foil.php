<script>

    /**
     * Disable submiting form via pressing 'enter' key
     */
    $('#2fa input').on('keypress', function(e) {
        return e.which !== 13;
    });


    $( "#2fa" ).submit(function( event ) {

        let btn_id = $(this).find("input[type=submit]:focus" ).attr( "id" );
        let url = "<?= route( "2fa@check-password" ) ?>";

        if( btn_id == "btn-delete2fa") {
            url = "<?= route( "2fa@delete" ) ?>";
        }

        $(this).attr( 'action', url );

        return true;
    });

</script>