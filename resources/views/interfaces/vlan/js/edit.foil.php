<script>

//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
const duplicate       = $("#duplicate").val() === "1";

$(document).ready( function() {

    // if we are duplicating the need to select / set the IP addresses
    // BUT ONLY AFTER ALL THE AJAX HAS RUN
    if( duplicate ) {
        $.when( $, ...ajaxRequests ).then( function() {
            const ipv6 = $( "#original-ipv6address" ).val();
            const ipv4 = $( "#original-ipv4address" ).val();

            // do these exist in the dropdown?
            if( !dd_ipv6.find( `option[value='${ipv6}']` ).length > 0 ) {
                let newOption = new Option( ipv6, ipv6, true, false);
                dd_ipv6.append(newOption);
            }

            dd_ipv6.val( ipv6 ).trigger('change.select2');

            // do these exist in the dropdown?
            if( !dd_ipv4.find( `option[value='${ipv4}']` ).length > 0 ) {
                let newOption = new Option( ipv4, ipv4, true, false);
                dd_ipv4.append(newOption);
            }

            dd_ipv4.val( ipv4 ).trigger('change.select2');
        });
    }

});

</script>
