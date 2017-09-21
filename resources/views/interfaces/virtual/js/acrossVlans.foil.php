<script>



    function usedAcrossVlans() {
        const inputName = $( this ).attr( "id" );
        const ipAddress = $( '#' + inputName ).val();

        $( '#alert-' + inputName ).html( '' ).hide();

        if( ipAddress ) {

            let html = "<ul>";

            $.ajax({
                url: "<?= url( '/api/v4/vlan/ip-address/used-across-vlans' )?>",
                method: "POST",
                data: { ip: ipAddress }
            })
                .done( function( data ) {
                    $.each( data, function( key, vli ){
                        html += `<li>${ipAddress} is in use by ${vli.customer.abbreviated_name} on ${vli.vlan.name}</li>\n`;
                    });
                })
                .fail( function() {
                    html += "<li>Error running ajax query for " + url + "</li>";
                    throw new Error( "Error running ajax query for " + url );
                })
                .always( function() {
                    if( html !== "<ul>" ) {
                        $('#alert-' + inputName).html( html + '</ul>' ).show();
                    }
                });
        }
    }

</script>
