<script>

    /**
     * Disable submiting form via pressing 'enter' key
     */
    $('#2fa-form input').on('keypress', function(e) {
        return e.which !== 13;
    });


    /**
     * Set the form action depending on the button clicked
     */
    $( "input[id^='btn-2fa']" ).on( 'click', function( event ) {
        event.preventDefault();

        let btn_id = (this.id).substring( 8 );
        let url = "";

        if( btn_id == "delete" ) {
            url = "<?= route( "2fa@delete" ) ?>";
        } else if( btn_id == "reset" ){
            url = "<?= route( "2fa@reset" ) ?>";
        } else if( btn_id == "enable" ){

        }

        $( "#2fa-form" ).attr( 'action', url ).submit();

    });

</script>
