<script>
    $( document ).ready(function() {

        $( "#list-user tr" ).click(function() {
            $( ".radio-button" ).prop( "checked", false );
            let radioBbtn = $( this ).find('td input:radio')
            radioBbtn.prop( 'checked', true );
            $( "#user_id" ).val( radioBbtn.val() )
        });

    });
</script>