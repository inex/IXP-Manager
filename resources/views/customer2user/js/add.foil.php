
<script>

    $( document ).ready(function() {
        $( "#list-user tr" ).click(function() {
            $( ".radio-button" ).prop( "checked", false );
            let radioBbtn = $(this).find('td input:radio')
            radioBbtn.prop('checked', true);
            $( "#existingUserId" ).val( radioBbtn.val() )

        });

    });

</script>